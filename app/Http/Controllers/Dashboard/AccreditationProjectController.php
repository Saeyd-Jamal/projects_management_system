<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AccreditationProject;
use App\Models\Allocation;
use App\Models\Broker;
use App\Models\Currency;
use App\Models\Executive;
use App\Models\Logs;
use App\Models\User;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccreditationProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', AccreditationProject::class);
        $accreditations = AccreditationProject::paginate();
        return view('dashboard.accreditations.index', compact('accreditations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('allocation', AccreditationProject::class);
        $accreditation = new AccreditationProject();
        $allocation = new Allocation();
        if(AccreditationProject::where('type', 'allocation')->count() > 0){
            $allocation->budget_number =  AccreditationProject::where('type', 'allocation')->whereYear('date_allocation', Carbon::now()->format('Y'))->orderBy('budget_number', 'desc')->first() ? AccreditationProject::where('type', 'allocation')->whereYear('date_allocation', Carbon::now()->format('Y'))->orderBy('budget_number', 'desc')->first()->budget_number + 1 : 1;
        }else{
            $allocation->budget_number =  Allocation::orderBy('budget_number', 'desc')->whereYear('date_allocation', Carbon::now()->format('Y'))->first() ? Allocation::orderBy('budget_number', 'desc')->whereYear('date_allocation', Carbon::now()->format('Y'))->first()->budget_number + 1 : 1;
        }
        $allocation->date_allocation =  Carbon::now()->format('Y-m-d');
        $allocation->currency_allocation =  'USD';
        $allocation->currency_allocation_value =  '1';

        $brokers = Broker::select('name')->distinct()->pluck('name')->toArray();
        $organizations = Allocation::select('organization_name')->distinct()->pluck('organization_name')->toArray();
        $projects = Allocation::select('project_name')->distinct()->pluck('project_name')->toArray();
        $items =  Allocation::select('item_name')->distinct()->pluck('item_name')->toArray();
        $currencies = Currency::get();
        $USD = Currency::where('code', 'USD')->first() ? Currency::where('code', 'USD')->first()->value : 1;


        return view('dashboard.accreditations.allocations.create', compact('accreditation', 'allocation', 'brokers', 'organizations', 'projects', 'items', 'currencies', 'USD'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function createExecutive()
    {
        $this->authorize('execution', AccreditationProject::class);
        $accreditation = new AccreditationProject();
        $executive = new Executive();
        $accreditation->implementation_date = Carbon::now()->format('Y-m-d');

        $accounts = Executive::select('account')->distinct()->pluck('account')->toArray();
        $affiliates = Executive::select('affiliate_name')->distinct()->pluck('affiliate_name')->toArray();
        $receiveds = Executive::select('received')->distinct()->pluck('received')->toArray();
        $details = Executive::select('detail')->distinct()->pluck('detail')->toArray();

        // get data from models
        $brokers = Broker::select('name')->distinct()->pluck('name')->toArray();
        $projects = Executive::select('project_name')->distinct()->pluck('project_name')->toArray();
        $items =  Executive::select('item_name')->distinct()->pluck('item_name')->toArray();
        $currencies = Currency::get();

        return view('dashboard.accreditations.executives.create', compact('accreditation', 'executive', 'brokers', 'projects', 'items', 'currencies', 'accounts', 'affiliates', 'receiveds', 'details'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', AccreditationProject::class);
        if($request->implementation_date){
            $month = Carbon::parse($request->implementation_date)->format('Y-m');
        }else{
            $month = null;
        }

        if($request->type == 'allocation'){
            $this->authorize('allocation', AccreditationProject::class);
            $num_allo = $request->num_allo + 1 ?? 1;
            for($i = 1; $i <= $num_allo; $i++){
                $request->validate([
                    'date_allocation' => 'required|date',
                    'budget_number' => 'required|integer',
                    'quantity_'.$i => 'nullable|integer',
                    'price_'.$i => 'nullable|numeric',
                    'total_dollar_'.$i => 'nullable|numeric',
                    'allocation_'.$i => 'nullable|numeric',
                    'currency_allocation_'.$i => 'required|exists:currencies,code',
                    'currency_allocation_value_'.$i  => 'required|numeric',
                    'amount_'.$i => 'nullable|numeric',
                    'implementation_items_'.$i => 'nullable|string',
                    'date_implementation_'.$i => 'nullable|date',
                    'implementation_statement_'.$i => 'nullable|string',
                    'amount_received_'.$i => 'nullable|numeric',
                    'notes' => 'nullable|string',
                    'number_beneficiaries_'.$i => 'nullable|integer',
                ]);
                $request['currency_allocation_value_'.$i] = 1 / $request['currency_allocation_value_'.$i];
                $request->merge([
                    'user_id' => Auth::user()->id,
                    'user_name' => Auth::user()->name,
                    // 'files' => json_encode($files),
                ]);
                $accreditation =AccreditationProject::create([
                    'date_allocation' => $request->date_allocation ?? null,
                    'budget_number' => $request->budget_number ?? null,
                    'broker_name' => $request->broker_name ?? null,
                    'organization_name' => $request->organization_name ?? null,
                    'project_name' => $request['project_name_'.$i]?? null,
                    'item_name' => $request['item_name_'.$i] ?? null,
                    'quantity' => $request['quantity_'.$i] ?? null,
                    'price' => $request['price_'.$i] ?? null,
                    'total_dollar' => $request['total_dollar_'.$i] ?? null,
                    'allocation' => $request['allocation_'.$i] ?? null,
                    'currency_allocation' => $request['currency_allocation_'.$i] ?? null,
                    'currency_allocation_value' => $request['currency_allocation_value_'.$i] ?? null,
                    'amount' => $request['amount_'.$i] ?? null,
                    'implementation_items' => $request['implementation_items_'.$i] ?? null,
                    'date_implementation' => $request['date_implementation_'.$i] ?? null,
                    'implementation_statement' => $request['implementation_statement_'.$i] ?? null,
                    'amount_received' => $request['amount_received_'.$i] ?? null,
                    'notes' => $request['notes'] ?? null,
                    'number_beneficiaries' => $request['number_beneficiaries_'.$i] ?? null,
                    'arrest_receipt_number' => $request['arrest_receipt_number_'.$i] ?? null,
                    'user_id' => Auth::user()->id ?? null,
                    'user_name' => Auth::user()->name ?? null,
                    'manager_name' => $request->manager_name ?? null,
                    // 'files' => json_encode($files),
                ]);
            }
            // رفع الملفات للتخصيص
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

            ActivityLogService::log(
                'Created',
                'AccreditationProject',
                "تم إضافة مشروع على قائمة الإعتماد",
                null,
                $accreditation->toArray()
            );
            
            return redirect()->route('dashboard.accreditations.index')->with('success', 'تمت إضافة مشروع جديد');

        }


        if($request->type == 'executive'){
            $this->authorize('execution', AccreditationProject::class);
            $request->validate([
                'implementation_date' => 'required|date',
                // 'budget_number' => 'required|integer',
                'account' => 'required|string',
                'affiliate_name' => 'required|string',
                'detail' => 'nullable|string',
                'quantity' => 'nullable|integer',
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
                // 'notes' => ($request->notes ?? '') . ' id=' . $id,
            ]);
        }
        AccreditationProject::create($request->all());
        return redirect()->route('dashboard.accreditations.index')->with('success', 'تمت إضافة مشروع جديد');
    }

    /**
     * Display the specified resource.
     */
    public function show(AccreditationProject $accreditation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AccreditationProject $accreditation)
    {
        $this->authorize('update', AccreditationProject::class);
        $selectedForm = $accreditation->type;
        $btn_label = "تعديل";
        $editForm = true;
        $brokers = Broker::select('name')->distinct()->pluck('name')->toArray();
        $organizations = Allocation::select('organization_name')->distinct()->pluck('organization_name')->toArray();
        $projects = Allocation::select('project_name')->distinct()->pluck('project_name')->toArray();
        $items =  Allocation::select('item_name')->distinct()->pluck('item_name')->toArray();
        $currencies = Currency::get();
        $USD = Currency::where('code', 'USD')->first() ? Currency::where('code', 'USD')->first()->value : 1;
        return view('dashboard.accreditations.allocations.edit', compact('accreditation', 'brokers', 'organizations', 'projects', 'items', 'currencies', 'USD', 'selectedForm', 'btn_label', 'editForm'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function editExecutive(AccreditationProject $accreditation)
    {
        $this->authorize('update', AccreditationProject::class);
        $selectedForm = $accreditation->type;
        $btn_label = "تعديل";
        $editForm = true;

        $accounts = Executive::select('account')->distinct()->pluck('account')->toArray();
        $affiliates = Executive::select('affiliate_name')->distinct()->pluck('affiliate_name')->toArray();
        $receiveds = Executive::select('received')->distinct()->pluck('received')->toArray();
        $details = Executive::select('detail')->distinct()->pluck('detail')->toArray();

        // get data from models
        $brokers = Broker::select('name')->distinct()->pluck('name')->toArray();
        $projects = Executive::select('project_name')->distinct()->pluck('project_name')->toArray();
        $items =  Executive::select('item_name')->distinct()->pluck('item_name')->toArray();
        $currencies = Currency::get();
        return view('dashboard.accreditations.executives.edit', compact('accreditation', 'brokers', 'projects', 'items', 'currencies', 'accounts', 'affiliates', 'receiveds', 'details', 'selectedForm', 'btn_label', 'editForm'));
    }



    public function adoption(Request $request, AccreditationProject $accreditation){
        $this->authorize('adoption', AccreditationProject::class);
        $accreditation->manager_name = Auth::user()->name;
        $accreditation->user_id = $accreditation->user_id;
        $accreditation->user_name = User::find($accreditation->user_id)->name ?? '';
        if($request->type == 'allocation'){
            $allocation = Allocation::create($accreditation->toArray());
            $accreditation->delete();
            ActivityLogService::log(
                'Adopted',
                'AccreditationProject',
                "إعتماد مشروع ونقله الى قائمة التخصيصات",
                null,
                $allocation->toArray(),
            );
        }
        if($request->type == 'executive'){
            $executive = Executive::create($accreditation->toArray());
            $accreditation->delete();
            ActivityLogService::log(
                'Adopted',
                'AccreditationProject',
                "إعتماد مشروع ونقله الى قائمة التنفيذات",
                null,
                $executive->toArray(),
            );
        }

        return redirect()->route('dashboard.accreditations.index')->with('success', 'تمت اعتماد المشروع');
    }    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AccreditationProject $accreditation)
    {

        $this->authorize('update', AccreditationProject::class);
        if($request->type == 'allocation'){
            $this->authorize('allocation', AccreditationProject::class);
            $request->validate([
                'date_allocation' => 'required|date',
                'budget_number' => 'required|integer',
                'quantity' => 'nullable|integer',
                'price' => 'nullable|numeric',
                'total_dollar' => 'nullable|numeric',
                'allocation' => 'nullable|numeric',
                'currency_allocation' => 'required|exists:currencies,code',
                'currency_allocation_value'  => 'required|numeric',
                'amount' => 'nullable|numeric',
                'implementation_items' => 'nullable|string',
                'date_implementation' => 'nullable|date',
                'implementation_statement' => 'nullable|string',
                'amount_received' => 'nullable|numeric',
                'notes' => 'nullable|string',
                'number_beneficiaries' => 'nullable|integer',
            ]);
            // // رفع الملفات للتخصيص
            // $files = json_decode($accreditation->files, true) ?? [];

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
            $request['currency_allocation_value'] = 1 / $request->currency_allocation_value;
            if($request->adoption == true){
                $request->merge([
                    'manager_name' => Auth::user()->name,
                    'user_id' => $accreditation->user_id,
                    'user_name' => $accreditation->user_name,
                ]);
                $allocation = Allocation::create($request->all());
                $accreditation->delete();
                ActivityLogService::log(
                    'Adopted',
                    'AccreditationProject',
                    "إعتماد مشروع ونقله الى قائمة التخصيصات",
                    null,
                    $allocation->toArray(),
                );

            }else{
                $accreditation->update($request->all());
                ActivityLogService::log(
                    'Updated',
                    'AccreditationProject',
                    "تم تعديل مشروع في قائمة الإعتماد",
                    $accreditation->getOriginal(),
                    $accreditation->getChanges()
                );
            }
        }

        if($request->type == 'executive'){
            $this->authorize('execution', AccreditationProject::class);
            $request->validate([
                'implementation_date' => 'required|date',
                // 'budget_number' => 'required|integer',
                'account' => 'required|string',
                'affiliate_name' => 'required|string',
                'detail' => 'nullable|string',
                'quantity' => 'nullable|integer',
                'price' => 'nullable|numeric',
                'total_ils' => 'nullable|numeric',
                'received' => 'nullable|string',
                'executive' => 'nullable|numeric',
                'notes' => 'nullable|string',
                'amount_payments' => 'nullable|numeric',
                'payment_mechanism' => 'nullable|string',
            ]);


            // $notes = $request->notes;
            // $searchTerm = 'id=';

            // $startPos = strpos($notes, $searchTerm);

            // if ($startPos !== false) {
            //     $startPos += strlen($searchTerm);
            //     $extractedText = substr($notes, $startPos);
            //     $id  = $extractedText;
            // }

            // // رفع الملفات للتخصيص
            // $files = json_decode($accreditation->files, true) ?? [];
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
                // 'files' => json_encode($files),
            ]);

            if($request->adoption == true){
                $request->merge([
                    'manager_name' => Auth::user()->name,
                    'user_id' => $accreditation->user_id,
                    'user_name' => $accreditation->user_name,
                ]);
                // $request['notes'] = "";
                $executive = Executive::create($request->all());
                $accreditation->delete();
                ActivityLogService::log(
                    'Adopted',
                    'AccreditationProject',
                    "إعتماد مشروع ونقله الى قائمة التنفيذات",
                    null,
                    $executive->toArray(),
                );
            }else{
                $accreditation->update($request->all());
                ActivityLogService::log(
                    'Updated',
                    'AccreditationProject',
                    "تم تعديل مشروع في قائمة الإعتماد",
                    $accreditation->getOriginal(),
                    $accreditation->getChanges()
                );
            }
        }


        return redirect()->route('dashboard.accreditations.index')->with('success', 'تم تعديل المشروع بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AccreditationProject $accreditation)
    {
        $this->authorize('delete', $accreditation);
        $accreditation->delete();
        ActivityLogService::log(
            'Delete',
            'AccreditationProject',
            "تم حذف مشروع من قائمة الإعتماد",
            $accreditation->toArray(),
            null,
        );
        return redirect()->route('dashboard.accreditations.index')->with('danger', 'تم حذف المشروع بنجاح');
    }

    public function checkNew(Request $request){

        $accreditations_count = AccreditationProject::count();

        return response()->json($accreditations_count);
    }
}
