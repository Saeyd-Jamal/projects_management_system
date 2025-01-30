<x-front-layout>
    <x-row />
    <x-slot:breadcrumb>
        <li><a href="#">الرئيسية</a></li>
    </x-slot:breadcrumb>
    <h2 class="mt-4 h3">الإحصائيات</h2>
    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <div class="card shadow bg-info">
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col">
                            <span class="h4 mb-0 text-white">
                                التخصيصات
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="font-weight-bold h4 mb-0">
                                {{ $allocations_count }}
                            </span>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-12">
                            <style>
                                #allocations_table th{
                                    color: #000;
                                    font-size: 16px;
                                    font-weight: bold;
                                }
                                #allocations_table td{
                                    color: #0a5128;
                                    font-weight: bold;
                                }
                                #allocations_table tr:nth-of-type(odd) {
                                    background: #fff;
                                }
                                #allocations_table tr:nth-of-type(even) {
                                    background: #ddd;
                                }
                            </style>
                            <table class="table align-items-center table-bordered" style="border: 5px solid #7deaab;" id="allocations_table">
                                <tbody>

                                    <tr>
                                        <th>المبالغ المخصصة (الرصيد السابق)</th>
                                        <td>{{ number_format($total_amount_sub, 2) ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <th>المبالغ المخصصة</th>
                                        <td>{{ number_format($total_amount, 2) ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <th>المبالغ المستلمة</th>
                                        <td>{{ number_format($total_amount_received, 2) ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <th>المتبقي</th>
                                        <td>{{ number_format(($total_amount_sub + $total_amount) - $total_amount_received, 2) ?? 0 }}</td>
                                    </tr>
                                    <tr>
                                        <th>نسبة التحصيل</th>
                                        <td>
                                            <span class="remaining_percent">
                                                {{ number_format(($total_amount_received / ($total_amount_sub + $total_amount)) * 100, 2) ?? 0 }}
                                            </span>%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="col-md-12 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-3 text-center">
                                <span class="circle circle-sm">
                                    <i class="fa-solid fa-chart-simple mb-0 " style="color: #0a5128; font-size: 30px"></i>
                                </span>
                            </div>
                            <div class="col">
                                <p class="small text-muted mb-0">عدد المؤسسات</p>
                                <span class="h3 mb-0">
                                    {{ count(App\Models\Allocation::select('broker_name')->distinct()->pluck('broker_name')->toArray()) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-3 text-center">
                                <span class="circle circle-sm">
                                    <i class="fa-solid fa-chart-line mb-0" style="color: #0a5128; font-size: 30px"></i>
                                </span>
                            </div>
                            <div class="col">
                                <p class="small text-muted mb-0">عدد المشاريع</p>
                                <span class="h3 mb-0">
                                    {{ count(App\Models\Allocation::select('project_name')->distinct()->pluck('project_name')->toArray()) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-3 text-center">
                                <span class="circle circle-sm">
                                    <i class="fa-solid fa-users mb-0" style="color: #0a5128; font-size: 30px"></i>
                                </span>
                            </div>
                            <div class="col">
                                <p class="small text-muted mb-0">عدد التجار</p>
                                <span class="h3 mb-0">
                                    {{ count(App\Models\Executive::select('account')->distinct()->pluck('account')->toArray()) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card shadow bg-success">
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col">
                            <span class="h4 mb-0 text-white">
                                التنفيذات
                            </span>
                        </div>
                        <div class="col-auto">
                            <span class="font-weight-bold h4 mb-0">
                                {{ $executives_count }}
                            </span>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-12">
                            <table class="table align-items-center table-bordered" style="border: 5px solid #17a2b8;" id="executives_table">
                                <style>
                                    #executives_table th{
                                        color: #000;
                                        font-size: 16px;
                                        font-weight: bold;
                                    }
                                    #executives_table td{
                                        color: #0a5128;
                                        font-weight: bold;
                                    }
                                    #executives_table tr:nth-of-type(odd) {
                                        background: #fff;
                                    }
                                    #executives_table tr:nth-of-type(even) {
                                        background: #ddd;
                                    }
                                </style>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>بالشيكل</th>
                                        <th>بالدولار</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>إجمالي المبالغ (الرصيد السابق)</th>
                                        <td class="total_total_ils">
                                            {{ number_format($total_ils_sub, 2) ?? 0 }}
                                        </td>
                                        <td class="total_total_dollars">
                                            {{ number_format($total_ils_sub * $ILS, 2) ?? 0 }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>إجمالي المبالغ</th>
                                        <td class="total_total_ils">
                                            {{ number_format($total_ils, 2) ?? 0 }}
                                        </td>
                                        <td class="total_total_dollars">
                                            {{ number_format($total_ils * $ILS, 2) ?? 0 }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>إجمالي الدفعات</th>
                                        <td class="total_amount_payments">
                                            {{ number_format($total_amount_payments, 2) ?? 0 }}
                                        </td>
                                        <td class="total_amount_payments_dollars">
                                            {{ number_format($total_amount_payments * $ILS, 2) ?? 0 }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>الرصيد المتبقي</th>
                                        <td class="remaining">
                                            {{ number_format(($total_ils_sub + $total_ils) - $total_amount_payments, 2) ?? 0 }}
                                        </td>
                                        <td class="remaining_dollars">
                                            {{ number_format((($total_ils_sub + $total_ils) - $total_amount_payments) * $ILS, 2) ?? 0 }}
                                        </td>
                                    </tr>
                                    <tr >
                                        <th colspan="2">سعر الدولار / الشيكل</th>
                                        <td class="text-danger">
                                            {{number_format(1 / $ILS,2) ?? 0}}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <h2 class="mt-2 h3">العام</h2>
    <div class="row mt-4">
        {{-- @can('create', 'App\\Models\AccreditationProject')
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <a href="{{ route('dashboard.accreditations.create') }}" class="d-flex flex-column align-items-center p-3">
                        <i class="fe fe-plus" style="font-size: 65px;"></i>
                        <h3> إضافة مشروع جديد</h3>
                    </a>
                </div>
            </div>
        </div>
        @endcan --}}
    </div>
</x-front-layout>
