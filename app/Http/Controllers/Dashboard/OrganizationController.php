<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Organization::class);
        $organizations = Organization::paginate(10);

        return view('dashboard.cataloguing.organizations.index', compact('organizations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Organization::class);
        $organization = new Organization();
        return view('dashboard.cataloguing.organizations.create', compact('organization'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Organization::class);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $request->merge([
            'slug' => Str::slug($request->post('name')),
        ]);

        $files = [];
        if ($request->hasFile('filesArray')) {
            foreach ($request->file('filesArray') as $file) {
                $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $filenameExtension = time() . '_' . $fileName . '.' . $file->extension();
                $filepath = $file->storeAs("files/organizations/$request->slug", $filenameExtension, 'public');
                $files[$file->getClientOriginalName()] = $filepath;
            }
        }
        $request->merge([
            'files' => json_encode($files),
        ]);
        Organization::create($request->all());
        return redirect()->route('organizations.index')->with('success', 'تم إضافة مؤسسة جديدة');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($slug)
    {
        $this->authorize('update', Organization::class);
        $organization = Organization::where('slug', $slug)->first();
        $btn_label = 'تعديل';
        $files = json_decode($organization->files, true) ?? [];
        return view('dashboard.cataloguing.organizations.edit', compact('organization', 'btn_label','files'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        $this->authorize('update', Organization::class);
        $organization = Organization::where('slug', $slug)->first();

        // استرجاع الملفات الحالية وتحويلها إلى مصفوفة
        $files = json_decode($organization->files, true) ?? [];
        if ($request->hasFile('filesArray')) {
            foreach ($request->file('filesArray') as $file) {
                $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $filenameExtension = time() . '_' . $fileName . '.' . $file->extension();
                $filepath = $file->storeAs("files/organizations/$slug", $filenameExtension, 'public');
                $files[$file->getClientOriginalName()] = $filepath;
            }
        }
        $request->merge([
            'files' => json_encode($files),
        ]);
        $organization->update($request->all());
        return redirect()->route('organizations.index',$slug)->with('success', 'تم تحديث بيانات المؤسسة');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $this->authorize('delete', Organization::class);
        $organization = Organization::where('slug', $slug)->first();
        $files = json_decode($organization->files, true) ?? [];
        foreach ($files as $file) {
            Storage::delete($file);
        }
        Storage::deleteDirectory('files/organizations/' . $slug);
        $organization->delete();
        return redirect()->route('organizations.index')->with('danger', 'تم حذف المؤسسة');
    }
}
