<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\Broker;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class BrokerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Broker::class);
        $request = request();
        if($request->ajax()) {
            $brokers = Broker::query();

            return DataTables::of($brokers)
                    ->addIndexColumn()  // إضافة عمود الترقيم التلقائي
                    ->addColumn('edit', function ($broker) {
                        return $broker->id;
                    })
                    ->addColumn('delete', function ($broker) {
                        return $broker->id;
                    })
                    ->make(true);
        }

        return view('dashboard.brokers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Broker::class);
        $broker = new Broker();
        $request = request();
        if($request->ajax()){
            return $broker;
        }
        return view('dashboard.brokers.create', compact('broker'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Broker::class);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $request->merge([
            'slug' => Str::slug($request->post('name')),
        ]);
        Broker::create($request->all());
        if($request->ajax()){
            return redirect()->route('dashboard.brokers.index')->with('success', 'تم إضافة وسيط جديد');
        }
        return redirect()->route('dashboard.brokers.index')->with('success', 'تم إضافة وسيط جديد');
    }

    /**
     * Display the specified resource.
     */
    public function show(Broker $broker)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($slug)
    {
        $this->authorize('update', Broker::class);
        $broker = Broker::where('id', $slug)->first();
        $request = request();
        if($request->ajax()) {
            return response()->json($broker);
        }
        return view('dashboard.brokers.edit', compact('broker', 'btn_label','files'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        $this->authorize('update', Broker::class);
        $broker = Broker::where('id', $slug)->first();
        $broker->update($request->all());

        if($request->ajax()) {
            return response()->json(['message' => 'تم التحديث بنجاح']);
        }
        return redirect()->route('dashboard.brokers.edit',$slug)->with('success', 'تم تحديث بيانات وسيط');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $this->authorize('delete', Broker::class);
        $broker = Broker::where('id', $slug)->first();
        $broker->delete();

        $request = request();
        if($request->ajax()) {
            return response()->json(['success' => 'تمت عملية الحذف بنجاح']);
        }
        return redirect()->route('dashboard.brokers.index')->with('danger', 'تم حذف وسيط');
    }


    public function setNew(){
        $brokers = Allocation::select('broker_name')->distinct()->pluck('broker_name')->toArray();
        foreach ($brokers as $broker) {
            $slug = Str::slug($broker);

            // تحقق إذا كان الـ slug موجودًا
            $originalSlug = $slug;
            $counter = 1;
            while (Broker::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter; // أضف رقم لجعل الـ slug فريدًا
                $counter++;
            }

            // قم بإنشاء السجل في الجدول
            Broker::create(['name' => $broker, 'slug' => $slug]);
        }
        return 'success';
    }
}
