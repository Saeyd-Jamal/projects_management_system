<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\ExecutivesImport;
use App\Models\Currency;
use App\Models\Executive;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use Yajra\DataTables\Facades\DataTables;

class ExecutiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('view', Executive::class);
        if($request->ajax()) {
            $year = $request->year ?? Carbon::now()->year;
            // جلب بيانات المستخدمين من الجدول
            $executives = Executive::query();

            // التصفية بناءً على السنة
            $executives->whereYear('implementation_date', $request->year);

            // التصفية بناءً على التواريخ
            if ($request->from_date != null && $request->to_date != null) {
                $executives->whereBetween('implementation_date', [$request->from_date, $request->to_date]);
            }

            return DataTables::of($executives)
                    ->addIndexColumn()  // إضافة عمود الترقيم التلقائي
                    ->addColumn('edit', function ($executive) {
                        return $executive->id;
                    })
                    ->addColumn('delete', function ($executive) {
                        return $executive->id;
                    })
                    ->make(true);
        }

        $ILS = Currency::where('code', 'ILS')->first()->value;
        // get data from executive table
        $accounts = Executive::select('account')->distinct()->pluck('account')->toArray();
        $affiliates = Executive::select('affiliate_name')->distinct()->pluck('affiliate_name')->toArray();
        $details = Executive::select('detail')->distinct()->pluck('detail')->toArray();
        $receiveds = Executive::select('received')->distinct()->pluck('received')->toArray();

        // get data from models
        $brokers = Executive::select('broker_name')->distinct()->pluck('broker_name')->toArray();
        $projects = Executive::select('project_name')->distinct()->pluck('project_name')->toArray();
        $saends = Executive::select('project_name')->distinct()->pluck('project_name')->toArray();
        $items =  Executive::select('item_name')->distinct()->pluck('item_name')->toArray();

        return view('dashboard.executives.index', compact('ILS','accounts', 'affiliates', 'receiveds', 'details', 'brokers','saends', 'projects', 'items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Executive::class);
        $executive = new Executive();
        if($request->ajax()){
            $executive->implementation_date =  Carbon::now()->format('Y-m-d');
            return $executive;
        }
        return view('dashboard.executives.create', compact('executive'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Executive::class);
        $request->validate([
            'implementation_date' => 'required|date',
            // 'budget_number' => 'required|integer',
            'account' => 'required|string',
            'affiliate_name' => 'required|string',
            'detail' => 'nullable|string',
            'quantity' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'total_ils' => 'nullable|numeric',
            'received' => 'nullable|string',
            'executive' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'amount_payments' => 'nullable|numeric',
            'payment_mechanism' => 'nullable|string',
        ]);

        // $id = Executive::latest()->first() ? Executive::latest()->first()->id + 1 : 1;
        // // رفع الملفات للتخصيص
        // $files = [];
        // $year = Carbon::parse($request->implementation_date)->format('Y');
        // if ($request->hasFile('filesArray')) {
        //     foreach ($request->file('filesArray') as $file) {
        //         $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        //         $filenameExtension = time() . '_' . $fileName . '.' . $file->extension();
        //         $filepath = $file->storeAs("files/executives/$year/$id", $filenameExtension, 'public');
        //         $files[$file->getClientOriginalName()] = $filepath;
        //     }
        // }
        $month = Carbon::parse($request->implementation_date)->format('m');
        $request->merge([
            'month' => $month,
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->name,
            // 'files' => json_encode($files),
        ]);

        Executive::create($request->all());
        if($request->ajax()) {
            return response()->json(['message' => 'تم الإضافة بنجاح']);
        }
        return redirect()->route('dashboard.executives.index')->with('success', 'تمت عملية الاضافة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Executive $executive)
    {
        if($request->ajax()){
            return response()->json($executive);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Executive $executive)
    {
        if($request->ajax()) {
            $executive->user = $executive->user() ?? new User();
            return response()->json($executive);
        }
        $this->authorize('update', Executive::class);
        $editForm = true;
        $btn_label = 'تعديل';
        $files = json_decode($executive->files, true) ?? [];
        return view('dashboard.executives.edit', compact('executive', 'editForm', 'btn_label', 'files'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Executive $executive)
    {
        $this->authorize('update', Executive::class);
        $request->validate([
            'implementation_date' => 'required|date',
            // 'budget_number' => 'required|integer',
            'account' => 'required|string',
            'affiliate_name' => 'required|string',
            'detail' => 'nullable|string',
            'quantity' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'total_ils' => 'nullable|numeric',
            'received' => 'nullable|string',
            'executive' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'amount_payments' => 'nullable|numeric',
            'payment_mechanism' => 'nullable|string',
        ]);

        // رفع الملفات للتخصيص
        // $files = json_decode($executive->files, true) ?? [];
        // $year = Carbon::parse($request->implementation_date)->format('Y');
        // if ($request->hasFile('filesArray')) {
        //     foreach ($request->file('filesArray') as $file) {
        //         $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        //         $filenameExtension = time() . '_' . $fileName . '.' . $file->extension();
        //         $filepath = $file->storeAs("files/executives/$year/$executive->id", $filenameExtension, 'public');
        //         $files[$file->getClientOriginalName()] = $filepath;
        //     }
        // }
        $month = Carbon::parse($request->implementation_date)->format('m');

        $request->merge([
            'month' => $month,
            // 'files' => json_encode($files),
        ]);
        $executive->update($request->all());
        if($request->ajax()) {
            return response()->json(['message' => 'تم التحديث بنجاح']);
        }
        return redirect()->route('dashboard.executives.index')->with('success', 'تمت عملية التعديل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete', Executive::class);
        // $files = json_decode($executive->files, true) ?? [];
        // $year = Carbon::parse($executive->implementation_date)->format('Y');
        // foreach ($files as $file) {
        //     Storage::delete($file);
        // }
        // Storage::deleteDirectory('files/executives/' . $year . '/' . $executive->id);

        $executive = Executive::findOrFail($id);

        $executive->delete();
        if($request->ajax()) {
            return response()->json(['success' => 'تمت عملية الحذف بنجاح']);
        }
        return redirect()->route('dashboard.executives.index')->with('danger', 'تمت عملية الحذف بنجاح');
    }


    public function import(Request $request){
        $this->authorize('import', Executive::class);
        if(!$request->hasFile('file')){
            return redirect()->route('dashboard.executives.index')->with('danger', 'الرجاء تحميل الملف');
        }
        $file = $request->file('file');
        Excel::import(new ExecutivesImport, $file);
        $executives = Executive::query();
        foreach($executives->get() as $executive){
            $date = Carbon::parse($executive->implementation_date)->format('Y-m-d');
            $month = Carbon::parse($date)->format('m');
            $executive->update([
                'month' => $month
            ]);
        }
        return redirect()->route('dashboard.executives.index')->with('success', 'تمت عملية الاستيراد بنجاح');
    }
}
