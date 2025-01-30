<x-front-layout>
    @push('styles')
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="{{asset('css/plugins/datatable/jquery.dataTables.min.css')}}">
        <link rel="stylesheet" href="{{asset('css/plugins/datatable/dataTables.bootstrap4.css')}}">
        <link rel="stylesheet" href="{{asset('css/plugins/datatable/dataTables.dataTables.css')}}">
        <link rel="stylesheet" href="{{asset('css/plugins/datatable/buttons.dataTables.css')}}">

        <link id="stickyTableLight" rel="stylesheet" href="{{ asset('css/custom/stickyTable.css') }}">
        <link id="stickyTableDark" rel="stylesheet" href="{{ asset('css/custom/stickyTableDark.css') }}" disabled>
        <link rel="stylesheet" href="{{ asset('css/custom/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/datatableIndex.css') }}">
        <style>
            thead th{
                font-size: 14px !important;
            }
            td .btn-icon{
                padding: 7px !important;
                width: auto !important;
                height: auto !important;
            }
            .dt-info{
                display: none !important;
            }
            .container-p-y:not([class^=pb-]):not([class*=" pb-"]){
                padding-bottom: 0 !important;
            }
        </style>
    @endpush
    <x-slot:extra_nav>
        <div class="nav-item form-group my-0 mx-2">
            <select name="year" id="year" class="form-control">
                @for ($year = date('Y'); $year >= 2023; $year--)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endfor
            </select>
        </div>
        @can('export-excel', 'App\\Models\Executive')
        <div class="nav-item">
            <button type="button" class="btn btn-icon btn-success text-white" id="excel-export" title="تصدير excel">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="16" height="16">
                    <path d="M64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-288-128 0c-17.7 0-32-14.3-32-32L224 0 64 0zM256 0l0 128 128 0L256 0zM155.7 250.2L192 302.1l36.3-51.9c7.6-10.9 22.6-13.5 33.4-5.9s13.5 22.6 5.9 33.4L221.3 344l46.4 66.2c7.6 10.9 5 25.8-5.9 33.4s-25.8 5-33.4-5.9L192 385.8l-36.3 51.9c-7.6 10.9-22.6 13.5-33.4 5.9s-13.5-22.6-5.9-33.4L162.7 344l-46.4-66.2c-7.6-10.9-5-25.8 5.9-33.4s25.8-5 33.4 5.9z"/>
                </svg>
            </button>
        </div>
        @endcan
        @can('create', 'App\\Models\Executive')
        <div class="nav-item mx-2">
            <button type="button" class="btn btn-icon text-success m-0" id="createNew">
                <i class="fa-solid fa-plus fe-16"></i>
            </button>
        </div>
        @endcan
        <div class="nav-item mx-2">
            <div class="dropdown">
                <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-2 me-n1 waves-effect waves-light" type="button" id="earningReportsId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <i class="fa-solid fa-filter fe-16"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="earningReportsId" data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(4px, 38px);">
                    <a class="dropdown-item waves-effect" id="filterBtn" href="javascript:void(0);">تصفية</a>
                    <a class="dropdown-item waves-effect" id="filterBtnClear" href="javascript:void(0);">إزالة التصفية</a>
                </div>
            </div>
        </div>
        <div class="nav-item d-flex align-items-center justify-content-center mx-2">
            <button type="button" class="btn" id="refreshData">
                <i class="fa-solid fa-arrows-rotate"></i>
            </button>
        </div>
    </x-slot:extra_nav>


    <div class="row">
        <div class="col-md-12" style="padding: 0 2px;">
            <div class="card">
                <div class="card-body table-container p-0">
                    <table id="executives-table" class="table table-striped table-bordered table-hover sticky" style="width:100%; height: calc(100vh - 175px);">
                        <thead>
                            <tr>
                                <th class="sticky" style="right: 0px;" id="nth0"></th>
                                <th class="text-white  opacity-7 text-center sticky" style="right: 40.6px;"  id="nth1">#</th>
                                <th class="sticky" style="right: 96.3px;"  id="nth2">
                                    <div class='d-flex align-items-center justify-content-between'>
                                        <span>التاريخ</span>
                                        <div class='filter-dropdown ml-4'>
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-2" type="button" id="date_filter" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="date_filter">
                                                    <div>
                                                        <input type="date" id="from_date" name="from_implementation_date" class="form-control mr-2" style="width: 200px"/>
                                                        <input type="date" id="to_date" value="{{Carbon\Carbon::now()->format('Y-m-d')}}" name="to_implementation_date" class="form-control mr-2 mt-2" style="width: 200px"/>
                                                    </div>
                                                    <div>
                                                        <button id="filter-date-btn" class='btn btn-success text-white filter-apply-btn-data' data-target="2" data-field="implementation_date">
                                                            <i class='fa-solid fa-check'></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th  class="sticky" style="right: 181.7px;"  id="nth3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>المؤسسة</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-3" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="broker_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="3" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="3" data-field="broker_name">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="3"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-3">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th class="sticky" style="right: 346.7px;"  id="nth4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>الحساب</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-4" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="account_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="4" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="4" data-field="account_name">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="4"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-4">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>الاسم</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-5" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="affiliate_name_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="5" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="5" data-field="affiliate_name">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="5"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-5">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>المشروع</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-6" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="projects_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="6" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="6" data-field="project_field">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="6"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-6">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>التفصيل..</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-7" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="detail_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="7" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="7" data-field="detail_field">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="7"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-7">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>الصنف</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-8" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="item_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="8" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="8" data-field="item_field">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="8"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-8">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>الكمية</th>
                                <th>السعر ₪</th>
                                <th>إجمالي ₪</th>
                                <th>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>المستلم</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-12" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="receiveds_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="12" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="12" data-field="received_field">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="12"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-12">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>ملاحظات</th>
                                <th>الدفعات</th>
                                <th>آلية الدفع</th>
                                <th>اسم المستخدم</th>
                                <th>المدير المستلم</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td class="sticky"></td>
                                <td class="sticky text-white text-center" style="background-color: transparent !important;" id="count_executives"></td>
                                <td class='sticky text-white  text-left' colSpan="3">المجموع</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class='text-white text-center' id="total_quantity"></td>
                                <td></td>
                                <td class='text-white text-center total_total_ils'></td>
                                <td></td>
                                <td></td>
                                <td class='text-white text-center total_amount_payments'></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- مجموع المبالغ --}}
    <div class="row justify-content-end m-0">
        <div class="col-12 p-0">
            <style>
                #total-table td , #total-table th {
                    padding: 2px 4px !important;
                }
            </style>
            <table class="table align-items-center mb-0 table-hover table-bordered" id="total-table">
                <thead>
                    <tr style="background: #27AE60;">
                        <th></th>
                        <th class="p-1">اجمالي مبالغ شيكل</th>
                        <th class="p-1">اجمالي الدفعات شيكل</th>
                        <th class="p-1">الرصيد المتبقي شيكل</th>
                        <th class="p-1">سعر الدولار / الشيكل</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background: #17a2b8;">
                        <th style="background: #27AE60;">بالشيكل</th>
                        <td style="color: #fff;" class="total_total_ils">
                        </td>
                        <td style=" color: #fff;" class="total_amount_payments">
                        </td>
                        <td  style=" color: #fff;" class="remaining">
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th style="background: #27AE60;">بالدولار</th>
                        <td class="text-danger total_total_dollars" style="background: #ddd">
                        </td>
                        <td class="text-danger total_amount_payments_dollars" style="background: #ddd">
                        </td>
                        <td class="text-danger remaining_dollars" style="background: #ddd">
                        </td>
                        <td class="text-danger">
                            {{number_format(1 / $ILS,2) ?? 0}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


    <!-- Fullscreen modal -->
    <div class="modal fade modal-full" id="editExecutive" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <button aria-label="" type="button" class="close px-2" data-bs-dismiss="modal" aria-hidden="true">
            <span aria-hidden="true">×</span>
        </button>
        <div class="modal-dialog modal-dialog-centered w-100" role="document" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <form id="editForm">
                        @include('dashboard.executives.editModal')
                    </form>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <!-- DataTables JS -->
        <script src="{{asset('js/plugins/datatable/jquery.dataTables.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/dataTables.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/dataTables.buttons.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/buttons.dataTables.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/jszip.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/pdfmake.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/vfs_fonts.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/buttons.html5.min.js')}}"></script>
        <script src="{{asset('js/plugins/datatable/buttons.print.min.js')}}"></script>
        <script src="{{asset('js/plugins/jquery.validate.min.js')}}"></script>
        
        <script type="text/javascript">
            $(document).ready(function() {
                let ils = {{ $ILS }};
                $.validator.messages.required = "هذا الحقل مطلوب";
                $("#editForm").validate({
                    rules: {
                        name: {
                            required: true,
                            maxlength: 255,
                        }
                    },
                    messages: {
                        name: "يرجى إدخال اسم المستخدم",
                    }
                });
                let formatNumber = (number,min = 0) => {
                    // التحقق إذا كانت القيمة فارغة أو غير صالحة كرقم
                    if (number === null || number === undefined || isNaN(number)) {
                        return ''; // إرجاع قيمة فارغة إذا كان الرقم غير صالح
                    }
                    return new Intl.NumberFormat('en-US', { minimumFractionDigits: min, maximumFractionDigits: 2 }).format(number);
                };
                let width0 = $('#nth0').outerWidth(true);
                let width1 = $('#nth1').outerWidth(true);
                let width2 = $('#nth2').outerWidth(true);
                let width3 = $('#nth3').outerWidth(true);
                let table = $('#executives-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    paging: false,              // تمكين الترقيم (Pagination)
                    searching: true,           // الإبقاء على البحث
                    info: true,                // تمكين النص السفلي الذي يوضح عدد السجلات
                    lengthChange: true,        // السماح للمستخدم بتغيير عدد المدخلات
                    layout: {
                        topStart: {
                            buttons: [
                                {
                                    extend: 'excelHtml5',
                                    text: 'تصدير Excel',
                                    title: 'التنفيذات', // تخصيص العنوان عند التصدير
                                    className: 'd-none', // إخفاء الزر الأصلي
                                    exportOptions: {
                                        columns: [1, 2, 3, 4, 5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21], // تحديد الأعمدة التي سيتم تصديرها (يمكن تعديلها حسب الحاجة)
                                        modifier: {
                                            search: 'applied', // تصدير البيانات المفلترة فقط
                                            order: 'applied',  // تصدير البيانات مع الترتيب الحالي
                                            page: 'all'        // تصدير جميع الصفحات المفلترة
                                        }
                                    }
                                },
                            ]
                        }
                    },
                    "language": {
                        "url": "{{ asset('files/Arabic.json')}}"
                    },
                    ajax: {
                        url: "{{ route('dashboard.executives.index') }}",
                        data: function (d) {
                            // إضافة تواريخ التصفية إلى الطلب المرسل
                            d.from_date = $('#from_date').val();
                            d.to_date = $('#to_date').val();
                            d.year = $('#year').val();
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', status, error);
                        }
                    },
                    columns: [
                        { data: 'edit', name: 'edit', orderable: false, class: 'sticky' , searchable: false, render: function(data, type, row) {
                                @can('update','App\\Models\Executive')
                                let link = `<button class="btn btn-icon text-primary edit_row"  data-id=":executive"><i class="fa-solid fa-edit"></i></button>`.replace(':executive', data);
                                return link ;
                                @else
                                return '';
                                @endcan
                        }},
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, class: 'sticky text-center'}, // عمود الترقيم التلقائي
                        { data: 'implementation_date', name: 'implementation_date'  , orderable: false, class: 'sticky text-center'},
                        { data: 'broker_name', name: 'broker_name' , orderable: false, class: 'sticky' },
                        { data: 'account', name: 'account' , orderable: false, class: 'sticky' },
                        { data: 'affiliate_name', name: 'affiliate_name' , orderable: false},
                        { data: 'project_name', name: 'project_name', orderable: false},
                        { data: 'detail', name: 'detail'  , orderable: false},
                        { data: 'item_name', name: 'item_name'  , orderable: false},
                        { data: 'quantity', name: 'quantity'  , orderable: false, class: 'text-center'},
                        { data: 'price', name: 'price'  , orderable: false, class: 'text-center'},
                        { data: 'total_ils', name: 'total_ils'  , orderable: false, class: 'text-center', render: function(data, type, row) {
                                return  formatNumber(data,2);
                        }},
                        { data: 'received', name: 'received'  , orderable: false},
                        { data: 'notes', name: 'notes'  , orderable: false,},
                        { data: 'amount_payments', name: 'amount_payments'  , orderable: false, class: 'text-center', render: function(data, type, row) {
                            return  formatNumber(data,2);
                        }},
                        { data: 'payment_mechanism', name: 'payment_mechanism'  , orderable: false},
                        { data: 'user_name', name: 'user_name'  , orderable: false,},
                        { data: 'manager_name', name: 'manager_name'  , orderable: false,},
                        {
                            data: 'delete',
                            name: 'delete',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row) {
                                @can('delete','App\\Models\Executive')
                                return `
                                        <button
                                            class="btn btn-icon text-danger delete_row"
                                            data-id="${data}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>`;
                                @else
                                return '';
                                @endcan
                            },
                        },
                    ],
                    columnDefs: [
                        { targets: 1, searchable: false, orderable: false } // تعطيل الفرز والبحث على عمود الترقيم
                    ],
                    drawCallback: function(settings) {
                        // تطبيق التنسيق على خلايا العمود المحدد
                        $('#executives-table tbody tr').each(function() {
                            $(this).find('td').eq(0).css('right', (0) + 'px');
                            $(this).find('td').eq(1).css('right', (width0) + 'px');
                            $(this).find('td').eq(2).css('right', (width0 + width1) + 'px');
                            $(this).find('td').eq(3).css('right', (width0 + width1 + width2 - 5) + 'px');
                            $(this).find('td').eq(4).css('right', (width0 + width1 + width2 + width3 - 10) + 'px');
                        });
                    },
                    footerCallback: function(row, data, start, end, display) {
                        var api = this.api();
                        // تحويل القيم النصية إلى أرقام
                        var intVal = function(i) {
                            return typeof i === 'string' ?
                                parseFloat(i.replace(/[\$,]/g, '')) :
                                typeof i === 'number' ? i : 0;
                        };
                        // 1. حساب عدد الأسطر في الصفحة الحالية
                        // count_executives 1
                        var rowCount = display.length;

                        // total_quantity 9
                        var total_quantity_sum = api
                            .column(9, { search: 'applied' }) // العمود الرابع
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        // total_total_ils 11
                        var total_total_ils_sum = api
                            .column(11, { search: 'applied' }) // العمود الرابع
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        // total_amount_payments 14
                        var total_amount_payments_sum = api
                            .column(14, { search: 'applied' }) // العمود الرابع
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        let remaining = total_total_ils_sum - total_amount_payments_sum;

                        // 4. عرض النتائج في `tfoot`
                        $('#count_executives').html(formatNumber(rowCount)); // عدد الأسطر
                        $('#total_quantity').html(formatNumber(total_quantity_sum));
                        $('.total_total_ils').html(formatNumber(total_total_ils_sum,2));
                        $('.total_total_dollars').html(formatNumber((total_total_ils_sum * ils),2));
                        $('.total_amount_payments').html(formatNumber(total_amount_payments_sum,2));
                        $('.total_amount_payments_dollars').html(formatNumber((total_amount_payments_sum * ils),2));
                        $('.remaining').html(formatNumber(remaining,2));
                        $('.remaining_dollars').html(formatNumber((remaining * ils),2));
                    }
                });
                // عندما يتم رسم الجدول (بعد تحميل البيانات أو تحديثها)
                table.on('draw', function() {
                    // التمرير إلى آخر سطر في الجدول
                    let tableContainer = $('#executives-table');
                    tableContainer.scrollTop(tableContainer[0].scrollHeight);


                    $('#executives-table thead tr').each(function() {
                        $(this).find('th.sticky').eq(0).css('right', (0) + 'px');
                        $(this).find('th.sticky').eq(1).css('right', (width0) + 'px');
                        $(this).find('th.sticky').eq(2).css('right', (width0 + width1) + 'px');
                        $(this).find('th.sticky').eq(3).css('right', (width0 + width1 + width2 - 5) + 'px');
                        $(this).find('th.sticky').eq(4).css('right', (width0 + width1 + width2 + width3 - 10) + 'px');
                    });
                    $('#executives-table tfoot tr').each(function() {
                        $(this).find('td.sticky').eq(0).css('right', (0) + 'px');
                        $(this).find('td.sticky').eq(1).css('right', (width0) + 'px');
                        $(this).find('td.sticky').eq(2).css('right', (width0 + width1) + 'px');
                    });
                });
                $('#executives-table_filter').addClass('d-none');
                // نسخ وظيفة الزر إلى الزر المخصص
                $('#excel-export').on('click', function () {
                    table.button('.buttons-excel').trigger(); // استدعاء وظيفة الزر الأصلي
                });
                $('#print-btn').on('click', function () {
                    table.button('.buttons-print').trigger(); // استدعاء وظيفة الطباعة الأصلية
                });
                // جلب الداتا في checkbox
                function populateFilterOptions(columnIndex, container,name) {
                    const uniqueValues = [];
                    table.column(columnIndex, { search: 'applied' }).data().each(function (value) {
                        const stringValue = value ? String(value).trim() : ''; // تحويل القيمة إلى نص وإزالة الفراغات
                        if (stringValue && uniqueValues.indexOf(stringValue) === -1) {
                            uniqueValues.push(stringValue);
                        }
                    });
                    // ترتيب القيم أبجديًا
                    uniqueValues.sort();
                    // إضافة الخيارات إلى div
                    const checkboxList = $(container);
                    checkboxList.empty();
                    uniqueValues.forEach(value => {
                        checkboxList.append(`
                            <label style="display: block;">
                                <input type="checkbox" value="${value}" class="${name}-checkbox"> ${value}
                            </label>
                        `);
                    });
                }
                function isColumnFiltered(columnIndex) {
                    const filterValue = table.column(columnIndex).search();
                    return filterValue !== ""; // إذا لم يكن فارغًا، الفلترة مفعلة
                }
                // دالة لإعادة بناء الفلاتر بناءً على البيانات الحالية
                function rebuildFilters() {
                    isColumnFiltered(3) ? '' : populateFilterOptions(3, '.checkbox-list-3','broker_name');
                    isColumnFiltered(4) ? '' : populateFilterOptions(4, '.checkbox-list-4','account_name');
                    isColumnFiltered(5) ? '' : populateFilterOptions(5, '.checkbox-list-5','affiliate_name');
                    isColumnFiltered(6) ? '' : populateFilterOptions(6, '.checkbox-list-6','project_field');
                    isColumnFiltered(7) ? '' : populateFilterOptions(7, '.checkbox-list-7','detail_field');
                    isColumnFiltered(8) ? '' : populateFilterOptions(8, '.checkbox-list-8','item_field');
                    isColumnFiltered(12) ? '' : populateFilterOptions(12, '.checkbox-list-12','received_field');


                    for (let i = 1; i <= 17; i++) {
                        if (isColumnFiltered(i)) {
                            $('#btn-filter-' + i).removeClass('btn-secondary');
                            $('#btn-filter-' + i + ' i').removeClass('fa-brands fa-get-pocket');
                            $('#btn-filter-' + i + ' i').addClass('fa-solid fa-filter');
                            $('#btn-filter-' + i).addClass('btn-success');
                        }else{
                            $('#btn-filter-' + i + ' i').removeClass('fa-solid fa-filter');
                            $('#btn-filter-' + i).removeClass('btn-success');
                            $('#btn-filter-' + i).addClass('btn-secondary');
                            $('#btn-filter-' + i + ' i').addClass('fa-brands fa-get-pocket');
                        }
                    }
                }
                table.on('draw', function() {
                    rebuildFilters();
                });
                // تطبيق الفلترة عند الضغط على زر "check"
                $('.filter-apply-btn').on('click', function() {
                    let target = $(this).data('target');
                    let field = $(this).data('field');
                    var filterValue = $("input[name="+ field + "]").val();
                    table.column(target).search(filterValue).draw();
                });
                // منع إغلاق dropdown عند النقر على input أو label
                $('th  .dropdown-menu .checkbox-list-box').on('click', function (e) {
                    e.stopPropagation(); // منع انتشار الحدث
                });
                // البحث داخل الـ checkboxes
                $('.search-checkbox').on('input', function() {
                    let searchValue = $(this).val().toLowerCase();
                    let tdIndex = $(this).data('index');
                    $('.checkbox-list-' + tdIndex + ' label').each(function() {
                        let labelText = $(this).text().toLowerCase();  // النص داخل الـ label
                        let checkbox = $(this).find('input');  // الـ checkbox داخل الـ label

                        if (labelText.indexOf(searchValue) !== -1) {
                            $(this).show();
                        } else {
                            $(this).hide();
                            if (checkbox.prop('checked')) {
                                checkbox.prop('checked', false);  // إذا كان الـ checkbox محددًا، قم بإلغاء تحديده
                            }
                        }
                    });
                });
                $('.all-checkbox').on('change', function() {
                    let index = $(this).data('index'); // الحصول على الـ index من الـ data-index

                    // التحقق من حالة الـ checkbox "الكل"
                    if ($(this).prop('checked')) {
                        // إذا كانت الـ checkbox "الكل" محددة، تحديد جميع الـ checkboxes الظاهرة فقط
                        $('.checkbox-list-' + index + ' input[type="checkbox"]:visible').prop('checked', true);
                    } else {
                        // إذا كانت الـ checkbox "الكل" غير محددة، إلغاء تحديد جميع الـ checkboxes الظاهرة فقط
                        $('.checkbox-list-' + index + ' input[type="checkbox"]:visible').prop('checked', false);
                    }
                });
                $('.filter-apply-btn-checkbox').on('click', function() {
                    let target = $(this).data('target'); // استرجاع الهدف (العمود)
                    let field = $(this).data('field'); // استرجاع الحقل (اسم المشروع أو أي حقل آخر)

                    // الحصول على القيم المحددة من الـ checkboxes
                    var filterValues = [];
                    // نستخدم الكلاس المناسب بناءً على الحقل (هنا مشروع)
                    $('.' + field + '-checkbox:checked').each(function() {
                        filterValues.push($(this).val()); // إضافة القيمة المحددة
                    });
                    // إذا كانت هناك قيم محددة، نستخدمها في الفلترة
                    if (filterValues.length > 0) {
                        // دمج القيم باستخدام OR (|) كما هو متوقع في البحث
                        var searchExpression = filterValues.join('|');
                        // تطبيق الفلترة على العمود باستخدام القيم المحددة
                        table.column(target).search(searchExpression, true, false).draw();
                    } else {
                        // إذا لم تكن هناك قيم محددة، نعرض جميع البيانات
                        table.column(target).search('').draw();
                    }
                });
                // تطبيق التصفية عند النقر على زر "Apply"
                $('#filter-date-btn').on('click', function () {
                    const fromDate = $('#from_date').val();
                    const toDate = $('#to_date').val();
                    table.ajax.reload(); // إعادة تحميل الجدول مع التواريخ المحدثة
                });
                $('#year').on('change', function () {
                    const year = $('#year').val();
                    table.ajax.reload();
                })
                // تفويض حدث الحذف على الأزرار الديناميكية
                $(document).on('click', '.delete_row', function () {
                    const id = $(this).data('id'); // الحصول على ID الصف
                    if (confirm('هل أنت متأكد من حذف العنصر؟')) {
                        deleteRow(id); // استدعاء وظيفة الحذف
                    }
                });
                // وظيفة الحذف
                function deleteRow(id) {
                    $.ajax({
                        url: '{{ route("dashboard.executives.destroy", ":id") }}'.replace(':id', id),
                        method: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function (response) {
                            alert('تم حذف العنصر بنجاح');
                            table.ajax.reload(); // إعادة تحميل الجدول بعد الحذف
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', status, error);
                            alert('هنالك خطاء في عملية الحذف.');
                        },
                    });
                }
                $(document).on('click', '#refreshData', function() {
                    table.ajax.reload();
                });
                $(document).on('click', '#filterBtnClear', function() {
                    $('.filter-dropdown').slideUp();
                    $('#filterBtn').text('تصفية');
                    $('.filterDropdownMenu input').val('');
                    $('th input[type="checkbox"]').prop('checked', false);
                    table.columns().search('').draw(); // إعادة رسم الجدول بدون فلاتر
                });
                $(document).on('click', '.edit_row', function () {
                    const id = $(this).data('id'); // الحصول على ID الصف
                    editExecutiveForm(id); // استدعاء وظيفة الحذف
                });
                const executive = {
                    id: '',
                    implementation_date: '',
                    month: '',
                    broker_name: '',
                    account: '',
                    affiliate_name: '',
                    project_name: '',
                    detail: '',
                    item_name: '',
                    quantity: '',
                    price: '',
                    total_ils: '',
                    received: '',
                    notes: '',
                    amount_payments: '',
                    payment_mechanism: '',
                    user_id: '',
                    user_name: '',
                    manager_name: '',
                    // files: '',
                    user : '',
                }
                function editExecutiveForm(id) {
                    $.ajax({
                        url: '{{ route("dashboard.executives.edit", ":id") }}'.replace(':id', id),
                        method: 'GET',
                        success: function (response) {
                            executive.id = response.id;
                            executive.implementation_date = response.implementation_date;
                            executive.month = response.month;
                            executive.broker_name = response.broker_name;
                            executive.account = response.account;
                            executive.affiliate_name = response.affiliate_name;
                            executive.project_name = response.project_name;
                            executive.detail = response.detail;
                            executive.item_name = response.item_name;
                            executive.quantity = response.quantity;
                            executive.price = response.price;
                            executive.total_ils = response.total_ils;
                            executive.received = response.received;
                            executive.notes = response.notes;
                            executive.amount_payments = response.amount_payments;
                            executive.payment_mechanism = response.payment_mechanism;
                            executive.user_id = response.user_id;
                            executive.user_name = response.user_name;
                            executive.manager_name = response.manager_name;
                            executive.user = response.user;
                            $.each(executive, function(key, value) {
                                const input = $('#' + key); // البحث عن العنصر باستخدام id
                                if (input.length) { // التحقق إذا كان العنصر موجودًا
                                    input.val(value); // تعيين القيمة
                                }
                            });
                            $('#addExecutive').remove();
                            $('#update').remove();
                            $('#btns_form').append(`
                                <button type="button" id="update" class="btn btn-primary mx-2">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    تعديل
                                </button>
                            `);
                            $('.editForm').css('display','block');
                            $('#editExecutive').modal('show');
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', status, error);
                            alert('هنالك خطأ في الإتصال بالسيرفر.');
                        },
                    })
                }
                $('.calculation').on('blur keypress', function (event) {
                    // تحقق إذا كان الحدث هو الضغط على مفتاح
                    if (event.type == 'keypress' && event.key != "Enter") {
                        return;
                    }
                    // استرجاع القيمة المدخلة
                    var input = $(this).val();
                    try {
                        // استخدام eval لحساب الناتج (مع الاحتياطات الأمنية)
                        var result = eval(input);
                        // عرض الناتج في الحقل
                        $(this).val(result);
                    } catch (e) {
                        // في حالة وجود خطأ (مثل إدخال غير صحيح)
                        alert('يرجى إدخال معادلة صحيحة!');
                    }
                });
                // التعديلات الحاصلة على النموذج
                $('#quantity, #price').on('input blur', function () {
                    // جلب القيم من الحقول
                    let quantity = $('#quantity').val();
                    let price = $('#price').val();

                    if(quantity != '' && price != ''){
                        quantity = parseFloat(quantity);
                        price = parseFloat(price);
                        let total_ils = quantity * price;
                        $('#total_ils').val(total_ils);
                    }
                });
                $(document).on('click', '#update', function () {
                    $.each(executive, function(key, value) {
                        const input = $('#' + key); // البحث عن العنصر باستخدام id
                        if(key == 'id'){
                            //
                        }else{
                            executive[key] = input.val();
                        }
                    });
                    $.ajax({
                        url: "{{ route('dashboard.executives.update', ':id') }}".replace(':id', executive.id),
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: executive,
                        success: function (response) {
                            $('#editExecutive').modal('hide');
                            table.ajax.reload();
                            alert('تم التعديل بنجاح');
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', status, error);
                            alert('هنالك خطأ في الإتصال بالسيرفر.');
                        },
                    })
                });
                $(document).on('click', '#createNew', function () {
                    $.ajax({
                        url: '{{ route("dashboard.executives.create") }}',
                        method: 'GET',
                        success: function (response) {
                            executive.id = response.id;
                            executive.implementation_date = response.implementation_date;
                            executive.month = response.month;
                            executive.broker_name = response.broker_name;
                            executive.account = response.account;
                            executive.affiliate_name = response.affiliate_name;
                            executive.project_name = response.project_name;
                            executive.detail = response.detail;
                            executive.item_name = response.item_name;
                            executive.quantity = response.quantity;
                            executive.price = response.price;
                            executive.total_ils = response.total_ils;
                            executive.received = response.received;
                            executive.notes = response.notes;
                            executive.amount_payments = response.amount_payments;
                            executive.payment_mechanism = response.payment_mechanism;
                            executive.user_id = response.user_id;
                            executive.user_name = response.user_name;
                            executive.manager_name = response.manager_name;
                            executive.user = response.user;
                            $.each(executive, function(key, value) {
                                const input = $('#' + key); // البحث عن العنصر باستخدام id
                                if (input.length) { // التحقق إذا كان العنصر موجودًا
                                    input.val(value); // تعيين القيمة
                                }
                            });
                            $('#addExecutive').remove();
                            $('#update').remove();
                            $('#btns_form').append(`
                                <button type="button" id="addExecutive" class="btn btn-primary mx-2">
                                    <i class="fa-solid fa-plus"></i>
                                    أضف
                                </button>
                            `);
                            $('.editForm').css('display','none');
                            $('#editExecutive').modal('show');
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', status, error);
                            alert('هنالك خطأ في الإتصال بالسيرفر.');
                        },
                    })
                });
                $(document).on('click', '#addExecutive', function () {
                    const id = $(this).data('id'); // الحصول على ID الصف
                    createExecutiveForm(id);
                });
                function createExecutiveForm(id){
                    $.each(executive, function(key, value) {
                        const input = $('#' + key); // البحث عن العنصر باستخدام id
                        if(key == 'id'){
                            executive['id'] = null;
                        }else{
                            executive[key] = input.val();
                        }
                    });
                    $.ajax({
                        url: "{{ route('dashboard.executives.store') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: executive,
                        success: function (response) {
                            $('#editExecutive').modal('hide');
                            table.ajax.reload();
                            alert('تم إضافة التنفيذ بنجاح');
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', status, error);
                            alert('هنالك خطأ في الإتصال بالسيرفر.');
                        },
                    })
                };
            });
        </script>
        <script>
            $(document).ready(function() {
                $(document).on('click', '#filterBtn', function() {
                    let text = $(this).text();
                    if (text != 'تصفية') {
                        $(this).text('تصفية');
                    }else{
                        $(this).text('إخفاء التصفية');
                    }
                    $('.filter-dropdown').slideToggle();
                });
                if (curentTheme == "light") {
                    $('#stickyTableLight').prop('disabled', false); // تشغيل النمط Light
                    $('#stickyTableDark').prop('disabled', true);  // تعطيل النمط Dark
                } else {
                    $('#stickyTableLight').prop('disabled', true);  // تعطيل النمط Light
                    $('#stickyTableDark').prop('disabled', false); // تشغيل النمط Dark
                }
            });
        </script>
    @endpush
</x-front-layout>
