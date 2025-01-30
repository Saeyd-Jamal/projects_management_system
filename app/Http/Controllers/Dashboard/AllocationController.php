<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Imports\AllocationsImport;
use App\Models\AccreditationProject;
use App\Models\Allocation;
use App\Models\Broker;
use App\Models\Currency;
use App\Models\Logs;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;


class AllocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('view', Allocation::class);

        if($request->ajax()) {
            // جلب بيانات المستخدمين من الجدول
            $year = $request->year ?? Carbon::now()->year;
            $allocations = Allocation::query()->orderBy('date_allocation', 'asc')->orderBy('budget_number', 'asc');


            // التصفية بناءً على التواريخ
            if ($request->from_date != null && $request->to_date != null) {
                $allocations = Allocation::query()->orderBy('date_allocation', 'asc')->orderBy('budget_number', 'asc');
                $allocations->whereBetween('date_allocation', [$request->from_date, $request->to_date]);
            }
            if ($request->from_date_implementation != null && $request->to_date_implementation != null) {
                $allocations = Allocation::query()->orderBy('date_allocation', 'asc')->orderBy('budget_number', 'asc');
                $allocations->whereBetween('date_implementation', [$request->from_date_implementation, $request->to_date_implementation]);
            }

            if($request->from_date == null && $request->from_date_implementation == null){
                $allocations = Allocation::query()->orderBy('date_allocation', 'asc')->orderBy('budget_number', 'asc');
                // التصفية بناءً على السنة
                $allocations->whereYear('date_allocation', $request->year);
            }

            return DataTables::of($allocations)
                    ->addIndexColumn()  // إضافة عمود الترقيم التلقائي
                    ->addColumn('edit', function ($allocation) {
                        return $allocation->id;
                    })
                    ->addColumn('currency_allocation_name', function ($allocation) {
                        $currency = Currency::where('code', $allocation->currency_allocation)->first();
                        return $currency ? "$currency->name" : '';
                    })
                    ->addColumn('select', function ($allocation) {
                        return $allocation->id;
                    })
                    ->addColumn('print', function ($allocation) {
                        return $allocation->id;
                    })
                    ->addColumn('delete', function ($allocation) {
                        return $allocation->id;
                    })
                    ->make(true);
        }

        $budgets = Allocation::select('budget_number')->distinct()->pluck('budget_number')->toArray();
        $brokers = Broker::select('name')->distinct()->pluck('name')->toArray();
        $organizations = Allocation::select('organization_name')->distinct()->pluck('organization_name')->toArray();
        $projs = Allocation::select('project_name')->distinct()->pluck('project_name')->toArray();
        $items =  Allocation::select('item_name')->distinct()->pluck('item_name')->toArray();
        $currencies = Currency::get();

        return view('dashboard.allocations.index', compact('budgets','brokers', 'organizations', 'projs', 'items', 'currencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Allocation::class);
        $allocation = new Allocation();
        if($request->ajax()){
            if(AccreditationProject::where('type', 'allocation')->count() > 0){
                $allocation->budget_number =  AccreditationProject::where('type', 'allocation')->orderBy('budget_number', 'desc')->first() ? AccreditationProject::where('type', 'allocation')->orderBy('budget_number', 'desc')->first()->budget_number + 1 : 1;
            }else{
                $allocation->budget_number =  Allocation::orderBy('budget_number', 'desc')->first() ? Allocation::orderBy('budget_number', 'desc')->first()->budget_number + 1 : 1;
            }
            $allocation->date_allocation =  Carbon::now()->format('Y-m-d');
            $allocation->currency_allocation =  'USD';
            $allocation->currency_allocation_value =  '1';
            return $allocation;
        }
        return view('dashboard.allocations.create', compact('allocation'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Allocation::class);
        $request->validate([
            'date_allocation' => 'required|date',
            'budget_number' => 'required|integer',
            'quantity' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'total_dollar' => 'nullable|numeric',
            'allocation' => 'nullable|numeric',
            'currency_allocation' => 'required|exists:currencies,code',
            'amount' => 'nullable|numeric',
            'implementation_items' => 'nullable|string',
            'date_implementation' => 'nullable|date',
            'implementation_statement' => 'nullable|string',
            'amount_received' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'number_beneficiaries' => 'nullable|integer',
        ]);
        // // رفع الملفات للتخصيص
        // $files = [];
        // $year = Carbon::parse($request->date_allocation)->format('Y');
        // if ($request->hasFile('filesArray')) {
        //     foreach ($request->file('filesArray') as $file) {
        //         $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        //         $filenameExtension = time() . '_' . $fileName . '.' . $file->extension();
        //         $filepath = $file->storeAs("files/allocations/$year/$request->budget_number", $filenameExtension, 'public');
        //         $files[$file->getClientOriginalName()] = $filepath;
        //     }
        // }

        $request->merge([
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->name,
            // 'files' => json_encode($files),
        ]);

        $allocation = Allocation::create($request->all());
        if($request->ajax()) {
            return response()->json(['message' => 'تم الإضافة بنجاح']);
        }
        return redirect()->route('dashboard.allocations.index')->with('success', 'تمت عملية الاضافة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Allocation $allocation)
    {
        if($request->ajax()){
            return response()->json($allocation);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Allocation $allocation)
    {
        if($request->ajax()) {
            $allocation->currency_allocation_name = Currency::where('code', $allocation->currency_allocation)->first() ? Currency::where('code', $allocation->currency_allocation)->first()->name : '';
            $allocation->user = $allocation->user() ?? new User();
            return response()->json($allocation);
        }
        $this->authorize('update', Allocation::class);
        $editForm = true;
        $btn_label = 'تعديل';
        $files = json_decode($allocation->files, true) ?? [];
        return view('dashboard.allocations.edit', compact('allocation', 'editForm', 'btn_label', 'files'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Allocation $allocation)
    {
        $this->authorize('update', Allocation::class);
        $request->validate([
            'date_allocation' => 'required|date',
            'budget_number' => 'required|integer',
            'quantity' => 'nullable|integer',
            'price' => 'nullable|numeric',
            'total_dollar' => 'nullable|numeric',
            'allocation' => 'nullable|numeric',
            'currency_allocation' => 'required|exists:currencies,code',
            'amount' => 'nullable|numeric',
            'implementation_items' => 'nullable|string',
            'date_implementation' => 'nullable|date',
            'implementation_statement' => 'nullable|string',
            'amount_received' => 'nullable|numeric',
            'notes' => 'nullable|string',
            'number_beneficiaries' => 'nullable|integer',
        ]);
        // رفع الملفات للتخصيص
        // $files = json_decode($allocation->files, true) ?? [];

        // $year = Carbon::parse($request->date_allocation)->format('Y');

        // if ($request->hasFile('filesArray')) {
        //     foreach ($request->file('filesArray') as $file) {
        //         $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        //         $filenameExtension = time() . '_' . $fileName . '.' . $file->extension();
        //         $filepath = $file->storeAs("files/allocations/$year/$request->budget_number", $filenameExtension, 'public');
        //         $files[$file->getClientOriginalName()] = $filepath;
        //     }
        // }

        $request->merge([
            // 'files' => $files,
        ]);

        $allocation->update($request->all());
        if($request->ajax()) {
            return response()->json(['message' => 'تم التحديث بنجاح']);
        }
        return redirect()->route('dashboard.allocations.index')->with('success', 'تمت عملية التعديل بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $this->authorize('delete', Allocation::class);
        // $files = json_decode($allocation->files, true) ?? [];
        // $year = Carbon::parse($allocation->date_allocation)->format('Y');
        // foreach ($files as $file) {
        //     Storage::delete($file);
        // }
        // Storage::deleteDirectory('files/allocations/' . $year . '/' . $allocation->budget_number);

        $allocation = Allocation::findOrFail($id);
        $allocation->delete();

        if($request->ajax()) {
            return response()->json(['success' => 'تمت عملية الحذف بنجاح']);
        }
        return redirect()->route('dashboard.allocations.index')->with('danger', 'تمت عملية الحذف بنجاح');
    }

    public function import(Request $request){
        $this->authorize('import', Allocation::class);
        if(!$request->hasFile('file')){
            return redirect()->route('allocations.index')->with('danger', 'الرجاء تحميل الملف');
        }
        $file = $request->file('file');
        Excel::import(new AllocationsImport, $file);
        return redirect()->route('dashboard.allocations.index')->with('success', 'تمت عملية الاستيراد بنجاح');
    }

    public function print(Request $request, Allocation $allocation){
        $pdf = PDF::loadView('dashboard.reports.allocation',['allocation' => $allocation],[],
        [
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 12,
            'default_font' => 'Arial',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 30,
            'margin_bottom' => 0,
        ]);
        $time = Carbon::now();
        return $pdf->stream('التخصيص رقم '.$allocation->budget_number.'  _ '.$time.'.pdf');
    }
}
