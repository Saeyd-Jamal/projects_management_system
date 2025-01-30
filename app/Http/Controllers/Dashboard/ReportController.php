<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Exports\AllocationExport;
use App\Exports\AreasExport;
use App\Exports\ModelExport;
use App\Exports\BrokersBalanceExport;
use App\Exports\DetectionItemsExport;
use App\Exports\ExecutivesExport;
use App\Exports\TotalExport;
use App\Exports\TradersReveExport;
use App\Models\Allocation;
use App\Models\Currency;
use App\Models\Executive;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ReportController extends Controller
{
    protected $monthNameAr;

    public function __construct(){
        // مصفوفة لأسماء الأشهر باللغة العربية
        $this->monthNameAr = [
            '01' => 'يناير',
            '02' => 'فبراير',
            '03' => 'مارس',
            '04' => 'أبريل',
            '05' => 'مايو',
            '06' => 'يونيو',
            '07' => 'يوليو',
            '08' => 'أغسطس',
            '09' => 'سبتمبر',
            '10' => 'أكتوبر',
            '11' => 'نوفمبر',
            '12' => 'ديسمبر'
        ];
    }

    public function filterAllocations($data){
        $allocations = Allocation::query();
        if(isset($data["broker"])){
            $allocations = $allocations->whereIn("broker_name",$data["broker"]);
        }
        if(isset($data["organization"])){
            $allocations = $allocations->whereIn("organization_name",$data["organization"]);
        }
        if(isset($data["project"])){
            $allocations = $allocations->whereIn("project_name",$data["project"]);
        }
        if(isset($data["item"])){
            $allocations = $allocations->whereIn("item_name",$data["item"]);
        }
        if($data["month"] != null){
            $allocations = $allocations->where("date_allocation",">=",Carbon::parse($data["month"]));
        }
        if($data["to_month"] != null){
            $allocations = $allocations->where("date_allocation","<",Carbon::parse($data["to_month"])->addMonth());
        }
        return $allocations;
    }

    public function filterExecutives($data){
        $executives = Executive::query();
        if(isset($data["broker"])){
            $executives = $executives->whereIn("broker_name",$data["broker"]);
        }
        if(isset($data["account"])){
            $executives = $executives->whereIn("account",$data["account"]);
        }
        if(isset($data["affiliate"])){
            $executives = $executives->whereIn("affiliate_name",$data["affiliate"]);
        }
        if(isset($data["project"])){
            $executives = $executives->whereIn("project_name",$data["project"]);
        }
        if(isset($data["item"])){
            $executives = $executives->whereIn("item_name",$data["item"]);
        }
        if(isset($data["received"])){
            $executives = $executives->whereIn("received",$data["received"]);
        }
        if($data["month"] != null){
            $executives = $executives->where("implementation_date",">=",Carbon::parse($data["month"]));
        }
        if($data["to_month"] != null){
            $executives = $executives->where("implementation_date","<=",Carbon::parse($data["to_month"]));
        }
        return $executives;
    }



    public function index()
    {
        $this->authorize('reports.view');
        // البيانات من نموذج Allocation
        $brokersFromAllocation = Allocation::select('broker_name')->distinct()->pluck('broker_name')->toArray();
        $organizations = Allocation::select('organization_name')->distinct()->pluck('organization_name')->toArray();
        $projectsFromAllocation = Allocation::select('project_name')->distinct()->pluck('project_name')->toArray();
        $itemsFromAllocation = Allocation::select('item_name')->distinct()->pluck('item_name')->toArray();

        // البيانات من نموذج Executive
        $brokersFromExecutive = Executive::select('broker_name')->distinct()->pluck('broker_name')->toArray();
        $projectsFromExecutive = Executive::select('project_name')->distinct()->pluck('project_name')->toArray();
        $itemsFromExecutive = Executive::select('item_name')->distinct()->pluck('item_name')->toArray();

        // دمج البيانات من كلا النموذجين مع إزالة التكرارات
        $brokers = array_unique(array_merge($brokersFromAllocation, $brokersFromExecutive));
        $projects = array_unique(array_merge($projectsFromAllocation, $projectsFromExecutive));
        $items = array_unique(array_merge($itemsFromAllocation, $itemsFromExecutive));


        $accounts = Executive::select('account')->distinct()->pluck('account')->toArray();
        $affiliates = Executive::select('affiliate_name')->distinct()->pluck('affiliate_name')->toArray();
        $receiveds = Executive::select('received')->distinct()->pluck('received')->toArray();
        $details = Executive::select('detail')->distinct()->pluck('detail')->toArray();


        return view('dashboard.pages.report',compact('brokers','organizations','projects','items','accounts','affiliates','receiveds','details'));
    }

    protected function createLogs($type,$method){
        ActivityLogService::log(
            'Print',
            'Report',
            'تم طباعة تقرير  :' . $type . ' ل :' . $method,
            null,
            null,
        );
    }

    public function export(Request $request)
    {

        $time = Carbon::now();
        $month = $request->month ?? '1970-01';
        $to_month = $request->to_month ?? Carbon::now()->format('Y-m');
        $year = ($request->month != null) ? Carbon::parse($request->month)->format('Y') : Carbon::now()->format('Y');
        $allocations = $this->filterAllocations($request->all());
        $executives = $this->filterExecutives($request->all());
        // المؤسسات الداعمة
        if($request->report_type == 'brokers_balance'){

            $brokers = $allocations->select('broker_name')->distinct()->pluck('broker_name')->toArray();

            $allocations = $this->filterAllocations($request->all())->get();
            // دوال الموجوع اخر سطر في التقرير
            $allocationsTotal_amout = $allocations->sum('amount');
            $allocationsTotal_amount_received = $allocations->sum('amount_received');

            $allocationsTotalArray = [
                'amount' => $allocationsTotal_amout,
                'amount_received' => $allocationsTotal_amount_received,
            ];
            if($request->export_type == 'view'){
                $this->createLogs('المؤسسات الداعمة','pdf');
                $pdf = PDF::loadView('dashboard.reports.brokers_balance',
                    [
                        'allocations' => $allocations,
                        'allocationsTotal' => $allocationsTotalArray,
                        'brokers' => $brokers,
                        'month' => $month,
                        'to_month' => $to_month
                    ]
                ,[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream();
            }
            if($request->export_type == 'export_pdf'){
                $this->createLogs('المؤسسات الداعمة','pdf');
                $pdf = PDF::loadView('dashboard.reports.brokers_balance',
                    [
                        'allocations' => $allocations,
                        'allocationsTotal' => $allocationsTotalArray,
                        'brokers' => $brokers,
                        'month' => $month,
                        'to_month' => $to_month
                    ]
                ,[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream('كشف ارصدة المؤسسات الداعمة _ '.$time.'.pdf');
            }
            if($request->export_type == 'export_excel'){
                $this->createLogs('المؤسسات الداعمة','excel');
                $filename = 'كشف أرصد المؤسسات الداعمة _ ' . $time .'.xlsx';
                return Excel::download(new BrokersBalanceExport($allocations,$allocationsTotalArray,$brokers), $filename);
            }
        }

        // التجار
        if($request->report_type == 'traders_reve'){

            $accounts = $executives->select('account')->distinct()->pluck('account')->toArray();

            $executives = $this->filterExecutives($request->all())->get();

            // دوال الموجوع اخر سطر في التقرير
            $executivesTotal = collect($executives)->map(function ($executive) use ($month,$to_month) {
                return [
                    "total_ils" => $executive->total_ils ?? '0',
                    'amount_payments' => $executive->amount_payments ?? '0',
                ];
            });
            $executivesTotalArray = [
                'total_ils' => collect($executivesTotal->pluck('total_ils')->toArray())->sum(),
                'amount_payments' => collect($executivesTotal->pluck('amount_payments')->toArray())->sum(),
            ];

            if($request->export_type == 'view'){
                $this->createLogs('التجار','pdf');
                $pdf = PDF::loadView('dashboard.reports.traders_reve',['executives' => $executives,'executivesTotal' => $executivesTotalArray,'accounts' => $accounts,'month' => $month,'to_month' => $to_month],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream();
            }
            if($request->export_type == 'export_pdf'){
                $this->createLogs('التجار','pdf');
                $pdf = PDF::loadView('dashboard.reports.traders_reve',['executives' => $executives,'executivesTotal' => $executivesTotalArray,'accounts' => $accounts,'month' => $month,'to_month' => $to_month],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream('كشف التجار _ '.$time.'.pdf');
            }
            if($request->export_type == 'export_excel'){
                $this->createLogs('التجار','excel');
                $filename = 'كشف التجار _ ' . $time .'.xlsx';
                return Excel::download(new TradersReveExport($executives,$executivesTotalArray,$accounts), $filename);
            }
        }

        //  الأصناف حسب الأشهر
        if($request->report_type == 'detection_items_month'){

            $items = $executives->select('item_name')->distinct()->pluck('item_name')->toArray();
            $months = $executives->whereBetween('implementation_date', [$year . '-01-01', $year . '-12-31'])->select('month')->distinct()->pluck('month')->toArray();
            $executives = $this->filterExecutives($request->all())->whereBetween('implementation_date', [$year . '-01-01', $year . '-12-31'])->get();

            $lastYear = Carbon::now()->subYear()->format('Y');
            $executivesTotalArray = [
                "01" => $executives->where('month', $year . '-01')->sum('quantity') ?? '0',
                '02' => $executives->where('month', $year . '-02')->sum('quantity') ?? '0',
                '03' => $executives->where('month', $year . '-03')->sum('quantity') ?? '0',
                '04' => $executives->where('month', $year . '-04')->sum('quantity') ?? '0',
                '05' => $executives->where('month', $year . '-05')->sum('quantity') ?? '0',
                '06' => $executives->where('month', $year . '-06')->sum('quantity') ?? '0',
                '07' => $executives->where('month', $year . '-07')->sum('quantity') ?? '0',
                '08' => $executives->where('month', $year . '-08')->sum('quantity') ?? '0',
                '09' => $executives->where('month', $year . '-09')->sum('quantity') ?? '0',
                '10' => $executives->where('month', $year . '-10')->sum('quantity') ?? '0',
                '11' => $executives->where('month', $year . '-11')->sum('quantity') ?? '0',
                '12' => $executives->where('month', $year . '-12')->sum('quantity') ?? '0',
                "$lastYear" => $this->filterExecutives($request->all())->whereBetween('month', [$lastYear . '-01', $lastYear . '-12'])->sum('quantity') ?? '0',
                'quantity' => $executives->sum('quantity') ?? '0',
                'total_ils' => $this->filterExecutives($request->all())->get()->sum('total_ils') ?? '0',
            ];

            if($request->export_type == 'view'){
                $this->createLogs('الأصناف حسب الأشهر','pdf');
                $pdf = PDF::loadView('dashboard.reports.detection_items_month',['executives' => $executives,'executivesTotal' => $executivesTotalArray,'items' => $items,'year' => $year,'lastYear' => $lastYear,'month' => $month,'to_month' => $to_month,'months' => $months,'monthNameAr' => $this->monthNameAr],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream();
            }
            if($request->export_type == 'export_pdf'){
                $this->createLogs('الأصناف حسب الأشهر','pdf');
                $pdf = PDF::loadView('dashboard.reports.detection_items_month',['executives' => $executives,'executivesTotal' => $executivesTotalArray,'items' => $items,'year' => $year,'lastYear' => $lastYear,'month' => $month,'to_month' => $to_month,'months' => $months,'monthNameAr' => $this->monthNameAr],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream('تقرير كميات أصناف المشاريع المنفذة  _ '.$time.'.pdf');
            }
            if($request->export_type == 'export_excel'){
                $this->createLogs('الأصناف حسب الأشهر','excel');
                $filename = 'تقرير كميات أصناف المشاريع المنفذة _ ' . $time .'.xlsx';
                return Excel::download(new DetectionItemsExport($year, $lastYear, $months, $this->monthNameAr,$items, $executives), $filename);
            }
        }

        // الإجمالي
        if($request->report_type == 'total'){

            $items = $executives->select('item_name')->distinct()->pluck('item_name')->toArray();


            $executives = $this->filterExecutives($request->all())->get();
            $allocations = $this->filterAllocations($request->all())->get();

            $executivesTotalArray = [
                "quantity_allocations" => $allocations->sum('quantity') ?? '0',
                'quantity_executives' => $executives->sum('quantity') ?? '0',
                'total_ils' => $executives->sum('total_ils') ?? '0',
            ];

            $executives = $this->filterExecutives($request->all());
            $allocations = $this->filterAllocations($request->all());

            if($request->export_type == 'view'){
                $this->createLogs('الإجمالي','pdf');
                $pdf = PDF::loadView('dashboard.reports.total',['executives' => $executives,'executivesTotal' => $executivesTotalArray,'allocations' => $allocations,'items' => $items,'month' => $month,'to_month' => $to_month],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream();
            }
            if($request->export_type == 'export_pdf'){
                $this->createLogs('الإجمالي','pdf');
                $pdf = PDF::loadView('dashboard.reports.total',['executives' => $executives,'executivesTotal' => $executivesTotalArray,'allocations' => $allocations,'items' => $items,'month' => $month,'to_month' => $to_month],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream('كشف الإجمالي _ '.$time.'.pdf');
            }
            if($request->export_type == 'export_excel'){
                $this->createLogs('الإجمالي','excel');
                $filename = 'كشف الإجمالي _ ' . $time .'.xlsx';
                return Excel::download(new TotalExport($items), $filename);
            }
        }


        // المناطق
        if($request->report_type == 'areas'){
            $items = $executives->select('item_name')->distinct()->pluck('item_name')->toArray();
            $items = array_slice($items, 0, 10);
            $areas = array_filter(
                $executives->select('received')->distinct()->pluck('received')->toArray(),
                fn($value) => !is_null($value) && $value !== ''
            );
            $executives = $this->filterExecutives($request->all());

            if($request->export_type == 'view'){
                $this->createLogs('المناطق','pdf');
                $pdf = PDF::loadView('dashboard.reports.areas',['executives' => $executives,'items' => $items,'month' => $month,'to_month' => $to_month,'areas' => $areas],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream();
            }
            if($request->export_type == 'export_pdf'){
                $this->createLogs('المناطق','pdf');
                $pdf = PDF::loadView('dashboard.reports.areas',['executives' => $executives,'items' => $items,'month' => $month,'to_month' => $to_month,'areas' => $areas],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream('كشف الأصناف حسب المناطق _ '.$time.'.pdf');
            }
            if($request->export_type == 'export_excel'){
                $this->createLogs('المناطق','excel');
                $filename = 'كشف الأصناف حسب المناطق _ ' . $time .'.xlsx';
                return Excel::download(new AreasExport($areas,$items), $filename);
            }
        }



        // أرصدة الأصناف
        if($request->report_type == 'item_balances'){

            $itemsFromExecutive = $executives->select('item_name')->distinct()->pluck('item_name')->toArray();
            $itemsFromAllocation = $allocations->select('item_name')->distinct()->pluck('item_name')->toArray();

            // دمج البيانات من كلا النموذجين مع إزالة التكرارات
            $items = array_unique(array_merge($itemsFromAllocation, $itemsFromExecutive));
            $items = array_slice($items, 0, 10);

            $brokers = $executives->select('broker_name')->distinct()->pluck('broker_name')->toArray();

            $executives = $this->filterExecutives($request->all());
            $allocations = $this->filterAllocations($request->all());

            $allocationsTotalArray = [
                "amounts_allocations" => $allocations->sum('amount') ?? '0',
            ];

            if($request->export_type == 'view' || $request->export_type == 'export_excel'){
                $this->createLogs('أرصدة الأصناف','pdf');
                $pdf = PDF::loadView('dashboard.reports.item_balances',['executives' => $executives,'allocations' => $allocations,'allocationsTotalArray' => $allocationsTotalArray,'items' => $items,'month' => $month,'to_month' => $to_month,'brokers' => $brokers],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream();
            }
            if($request->export_type == 'export_pdf'){
                $this->createLogs('أرصدة الأصناف','pdf');
                $pdf = PDF::loadView('dashboard.reports.item_balances',['executives' => $executives,'allocations' => $allocations,'allocationsTotalArray' => $allocationsTotalArray,'items' => $items,'month' => $month,'to_month' => $to_month,'brokers' => $brokers],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream('كشف أرصدة الأصناف _ '.$time.'.pdf');
            }
        }


        // التخصيصات
        if($request->report_type == 'allocations'){

            $allocations = $this->filterAllocations($request->all())->get();

            $allocationsTotal = [
                "quantity" => $allocations->sum('quantity') ?? '0',
                "amount" => $allocations->sum('amount') ?? '0',
                "amount_received" => $allocations->sum('amount_received') ?? '0',
            ];


            $amounts_allocated = $allocations->sum('amount');
            $amounts_received = $allocations->sum('amount_received');
            $remaining = $amounts_allocated - $amounts_received;

            if($amounts_allocated != 0 && $amounts_received != 0){
                $collection_rate = ($amounts_received / $amounts_allocated) * 100;
            }else{
                $collection_rate = 0;
            }



            if($request->export_type == 'view'){
                $this->createLogs('التخصيصات','pdf');
                $allocations = $this->filterAllocations($request->all())->limit(500)->get();
                $pdf = PDF::loadView('dashboard.reports.allocations',['allocations' => $allocations,'allocationsTotal' => $allocationsTotal,'amounts_allocated' => $amounts_allocated,'amounts_received' => $amounts_received,'collection_rate' => $collection_rate,'remaining' => $remaining,'month' => $month,'to_month' => $to_month],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream();
            }
            if($request->export_type == 'export_pdf'){
                $this->createLogs('التخصيصات','pdf');
                $allocations = $this->filterAllocations($request->all())->limit(500)->get();
                $pdf = PDF::loadView('dashboard.reports.allocations',['allocations' => $allocations,'month' => $month,'to_month' => $to_month],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream('كشف التخصيصات _ '.$time.'.pdf');
            }
            if($request->export_type == 'export_excel'){
                $this->createLogs('التخصيصات','excel');
                $filename = 'كشف التخصيصات _ ' . $time .'.xlsx';
                return Excel::download(new AllocationExport($allocations), $filename);
            }
        }

        // التنفيذات
        if($request->report_type == 'executives'){

            $executives = $this->filterExecutives($request->all())->get();

            $executivesTotal = [
                "quantity" => $executives->sum('quantity') ?? '0',
                "total_ils" => $executives->sum('total_ils') ?? '0',
                "amount_payments" => $executives->sum('amount_payments') ?? '0',
            ];

            $total_amounts = $executives->sum('total_ils');
            $total_payments = $executives->sum('amount_payments');
            $remaining_balance = $total_amounts - $total_payments;

            $ILS = Currency::where('code', 'ILS')->first()->value;



            if($request->export_type == 'view'){
                $this->createLogs('التنفيذات','pdf');
                $executives = $this->filterExecutives($request->all())->limit(500)->get();

                $pdf = PDF::loadView('dashboard.reports.executives',['executives' => $executives,'executivesTotal' => $executivesTotal,'total_amounts' => $total_amounts,'total_payments' => $total_payments,'remaining_balance' => $remaining_balance,'ILS' => $ILS,'month' => $month,'to_month' => $to_month],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream();
            }
            if($request->export_type == 'export_pdf'){
                $this->createLogs('التنفيذات','pdf');
                $executives = $this->filterExecutives($request->all())->limit(500)->get();
                $pdf = PDF::loadView('dashboard.reports.executives',['executives' => $executives,'executivesTotal' => $executivesTotal,'total_amounts' => $total_amounts,'total_payments' => $total_payments,'remaining_balance' => $remaining_balance,'ILS' => $ILS,'month' => $month,'to_month' => $to_month],[],
                [
                    'mode' => 'utf-8',
                    'format' => 'A4-L',
                    'default_font_size' => 12,
                    'default_font' => 'Arial',
                ]);
                return $pdf->stream('كشف التنفيذات _ '.$time.'.pdf');
            }
            if($request->export_type == 'export_excel'){
                $this->createLogs('التنفيذات','excel');
                $filename = 'كشف التنفيذات _ ' . $time .'.xlsx';
                return Excel::download(new ExecutivesExport($executives), $filename);
            }
        }


    }
}
