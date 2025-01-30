<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Project::class);
        $projects = Project::paginate(10);
        return view('dashboard.cataloguing.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Project::class);
        $project = new Project();
        return view('dashboard.cataloguing.projects.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);

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
                $filepath = $file->storeAs("files/projects/$request->slug", $filenameExtension, 'public');
                $files[$file->getClientOriginalName()] = $filepath;
            }
        }
        $request->merge([
            'files' => json_encode($files),
        ]);

        Project::create($request->all());
        return redirect()->route('projects.index')->with('success', 'تم إضافة مشروع عمل جديد');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', Project::class);
        $btn_label = 'تعديل';
        $files = json_decode($project->files, true) ?? [];
        return view('dashboard.cataloguing.projects.edit', compact('project', 'btn_label','files'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', Project::class);
        $slug = $project->slug;
        // استرجاع الملفات الحالية وتحويلها إلى مصفوفة
        $files = json_decode($project->files, true) ?? [];
        if ($request->hasFile('filesArray')) {
            foreach ($request->file('filesArray') as $file) {
                $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $filenameExtension = time() . '_' . $fileName . '.' . $file->extension();
                $filepath = $file->storeAs("files/projects/$slug", $filenameExtension, 'public');
                $files[$file->getClientOriginalName()] = $filepath;
            }
        }
        $request->merge([
            'files' => json_encode($files),
        ]);
        $project->update($request->all());
        return redirect()->route('projects.index')->with('success', 'تم تحديث بيانات المشروع المحدد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', Project::class);
        $files = json_decode($project->files, true) ?? [];
        foreach ($files as $file) {
            Storage::delete($file);
        }
        Storage::deleteDirectory('files/projects/' . $project->slug);
        $project->delete();
        return redirect()->route('projects.index')->with('danger', 'تم حذف المشروع المحدد');
    }
}
