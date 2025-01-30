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
        @can('copy', 'App\\Models\Allocation')
        <div class="nav-item">
            <button type="button" class="btn btn-icon btn-info text-white mx-1" id="copy-export" title="نسخ">
                <i class="fa-solid fa-copy fe-16"></i>
            </button>
        </div>
        @endcan
        @can('export-excel', 'App\\Models\Allocation')
        <div class="nav-item">
            <button type="button" class="btn btn-icon btn-success text-white" id="excel-export" title="تصدير excel">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="16" height="16">
                    <path d="M64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-288-128 0c-17.7 0-32-14.3-32-32L224 0 64 0zM256 0l0 128 128 0L256 0zM155.7 250.2L192 302.1l36.3-51.9c7.6-10.9 22.6-13.5 33.4-5.9s13.5 22.6 5.9 33.4L221.3 344l46.4 66.2c7.6 10.9 5 25.8-5.9 33.4s-25.8 5-33.4-5.9L192 385.8l-36.3 51.9c-7.6 10.9-22.6 13.5-33.4 5.9s-13.5-22.6-5.9-33.4L162.7 344l-46.4-66.2c-7.6-10.9-5-25.8 5.9-33.4s25.8-5 33.4 5.9z"/>
                </svg>
            </button>
        </div>
        @endcan
        @can('create', 'App\\Models\Allocation')
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
                    <table id="allocations-table" class="table table-striped table-bordered table-hover sticky" style="width:100%; height: calc(100vh - 155px);">
                        <thead>
                            <tr>
                                <th class="sticky" id="nth0"></th>
                                <th class="text-white opacity-7 text-center sticky"  id="nth1">#</th>
                                <th class="sticky" style="right: 36.5px;"  id="nth2">
                                    <div class='d-flex align-items-center justify-content-between'>
                                        <span>تاريخ التخصيص</span>
                                        <div class='filter-dropdown ml-4'>
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-2" type="button" id="date_filter" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="date_filter">
                                                    <div>
                                                        <input type="date" id="from_date" name="from_date_allocation" class="form-control mr-2" style="width: 200px"/>
                                                        <input type="date" id="to_date" value="{{Carbon\Carbon::now()->format('Y-m-d')}}" name="to_date_allocation" class="form-control mr-2 mt-2" style="width: 200px"/>
                                                    </div>
                                                    <div>
                                                        <button id="filter-date-btn" class='btn btn-success text-white filter-apply-btn-data' data-target="2" data-field="date_name">
                                                            <i class='fa-solid fa-check'></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th class="sticky" style="right: 153px;" id="nth3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span style="font-size: 12px;">رقم <br> الموازنة</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-3" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="budget_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="3" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="3" data-field="budget_name">
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
                                {{-- <th class="sticky" style="right:  220px;" id="nth3">
                                    <div class='d-flex align-items-center justify-content-between'>
                                        <span>الاسم المختصر</span>
                                        <div class='filter-dropdown ml-4'>
                                            <div class="dropdown">
                                                <button class="btn" style="padding: 0; margin: 0; border: none;" type="button" id="broker_filter" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-solid fa-filter text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="broker_filter">
                                                    <input type="text" name="broker_name" class="form-control mr-2  py-0 px-2" list="brokers_list" style="width: 200px"/>
                                                    <datalist id="brokers_list">
                                                        @foreach ($brokers as $broker)
                                                            <option value="{{$broker}}" >
                                                        @endforeach
                                                    </datalist>
                                                    <div>
                                                        <button class='btn btn-success text-white filter-apply-btn' data-bs-target="4" data-field="broker_name">
                                                            <i class='fa-solid fa-check'></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th> --}}
                                <th class="sticky" style="right: 217.6px;" id="nth4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>المؤسسة</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-4" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="broker_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="4" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="4" data-field="broker_name">
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
                                        <span>المتبرع</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-5" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="organization_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="5" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="5" data-field="organization_name">
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
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="proj_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="6" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="6" data-field="proj_name">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            {{-- <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="6"> الكل
                                                            </label> --}}
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
                                        <span>الصنف</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-7" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="item_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="7" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="7" data-field="item_name">
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
                                        <span>الكمية</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-8" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="quantity_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="8" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="8" data-field="quantity_field">
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
                                <th>السعر</th>
                                <th>إجمالي</th>
                                <th>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>التخصيص</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-11" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="allocation_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="11" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="11" data-field="allocation_field">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="11"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-11">
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
                                        <span>العملة</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-25" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="currency_allocation_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="25" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="25" data-field="currency_allocation_field">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="25"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-25">
                                                                <label style="display: block;">
                                                                    <input type="checkbox" value="USD" class="currency_allocation_field-checkbox"> الدولار
                                                                </label>
                                                                <label style="display: block;">
                                                                    <input type="checkbox" value="ILS" class="currency_allocation_field-checkbox"> شيكل
                                                                </label>
                                                                <label style="display: block;">
                                                                    <input type="checkbox" value="EUR" class="currency_allocation_field-checkbox"> اليورو
                                                                </label>
                                                                <label style="display: block;">
                                                                    <input type="checkbox" value="JOD" class="currency_allocation_field-checkbox"> الدينار الأردني
                                                                </label>
                                                                <label style="display: block;">
                                                                    <input type="checkbox" value="KWD" class="currency_allocation_field-checkbox"> الدينار الكويتي
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>سعر العملة</th>
                                <th>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>المبلغ $</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-14" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="amount_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="14" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="14" data-field="amount_field">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="14"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-14">
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
                                        <span>عدد المستفيدين</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-15" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="number_beneficiaries_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="15" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="15" data-field="number_beneficiaries_field">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="15"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-15">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>بنود التنفيد</th>
                                <th>
                                    <div class='d-flex align-items-center justify-content-between'>
                                        <span>تاريخ القبض</span>
                                        <div class='filter-dropdown ml-4'>
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-2" type="button" id="date_filter" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="date_filter">
                                                    <div>
                                                        <input type="date" id="from_date_implementation" name="from_date_implementation" class="form-control mr-2" style="width: 200px"/>
                                                        <input type="date" id="to_date_implementation" value="{{Carbon\Carbon::now()->format('Y-m-d')}}" name="to_date_implementation" class="form-control mr-2 mt-2" style="width: 200px"/>
                                                    </div>
                                                    <div>
                                                        <button id="filter-date-btn_implementation" class='btn btn-success text-white filter-apply-btn-data' data-bs-target="17" data-field="date_implementation">
                                                            <i class='fa-solid fa-check'></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>بيان</th>
                                <th>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>المبلغ المقبوض</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-19" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="amount_received_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="19" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="19" data-field="amount_received_field">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="19"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-19">
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
                                        <span>رقم الإيصال</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-20" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="arrest_receipt_number_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="20" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="20" data-field="arrest_receipt_number_field">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="20"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-20">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>ملاحظات</th>
                                <th>اسم المستخدم</th>
                                <th>المدير المستلم</th>
                                <th></th>
                                <th>
                                    <i class="fa-solid fa-print text-white fe-16"></i>
                                </th>
                                <th></th>
                                <th style="display: none;"></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td class="sticky"></td>
                                <td class="sticky text-white text-center" id="count_allocations"></td>
                                <td class='text-white sticky text-left' colSpan="3">المجموع</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class='text-white text-center' id="total_quantity"></td>
                                <td></td>
                                <td></td>
                                <td class='text-white text-center' id="total_allocation"></td>
                                <td></td>
                                <td></td>
                                <td class='text-white text-center total_amount'></td>
                                <td class='text-white text-center' id="total_number_beneficiaries"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class='text-white text-center total_amount_received'></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="display: none;"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-end m-0">
        <div class="col-12 p-0">
            <table class="table align-items-center mb-0 table-bordered">
                <thead>
                    <tr style="background: #27AE60;">
                        <th class="p-1">المبالغ المخصصة</th>
                        <th class="p-1">المبالغ المستلمة</th>
                        <th class="p-1">المتبقي</th>
                        <th class="p-1">نسبة التحصيل</th>
                    </tr>
                </thead>
                <tbody  style=" color: #fff;">
                    <tr>
                        <td  style="background: #17a2b8; color: #fff !important;" class="total_amount">
                        </td>
                        <td  style="background: #17a2b8; color: #fff !important;" class="total_amount_received">
                        </td>
                        <td class="remaining" style="background: #ddd; color: #000;">
                        </td>
                        <td style="color: #C0392B !important; background: #ddd;">
                            <span class="remaining_percent"></span>%
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Fullscreen modal -->
    <div class="modal fade modal-full" id="editAllocation" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <button aria-label="" type="button" class="close px-2" data-bs-dismiss="modal" aria-hidden="true">
            <span aria-hidden="true">×</span>
        </button>
        <div class="modal-dialog modal-dialog-centered w-100" role="document" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <form id="editForm">
                        @include('dashboard.allocations.editModal')
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- small modal -->

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
                let table = $('#allocations-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    paging: false,              // تعطيل الترقيم
                    searching: true,            // الإبقاء على البحث إذا كنت تريده
                    info: false,                // تعطيل النص السفلي الذي يوضح عدد السجلات
                    lengthChange: false,        // تعطيل قائمة تغيير عدد المدخلات
                    layout: {
                        topStart: {
                            buttons: [
                                {
                                    extend: 'excelHtml5',
                                    text: 'تصدير Excel',
                                    title: 'التخصيصات', // تخصيص العنوان عند التصدير
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
                                {
                                    extend: 'copy',
                                    text: 'نسخ الأسطر المحددة',
                                    exportOptions: {
                                        // تحديد الأعمدة المطلوبة فقط
                                        columns: [
                                            3,
                                            2,
                                            4,
                                            5,
                                            6,
                                            7,
                                            8,
                                            9,
                                            10,
                                            11,
                                            12,
                                            14,
                                            16,
                                            17,
                                            18,
                                            19,
                                        ], // ضع أرقام الأعمدة التي تريد نسخها فقط
                                        rows: function (idx, data, node) {
                                            return $(node).find('.select_row').prop('checked');
                                        },
                                        format: {
                                            body: function(data, row, column, node) {
                                                // تأكد من تحويل البيانات إلى نص لتجنب الأخطاء
                                                let cleanData = String(data)
                                                    // إزالة HTML tags والتنسيقات
                                                    .replace(/<[^>]+>/g, ' ')
                                                    // إزالة multiple spaces
                                                    .replace(/\s+/g, ' ')
                                                    // إزالة line breaks
                                                    .replace(/(\r\n|\n|\r)/gm, ' ')
                                                    // إزالة الفراغات من البداية والنهاية
                                                    .trim();
                                                    return cleanData;
                                                }
                                        }
                                    },
                                    className: 'd-none', // إخفاء الزر الأصلي
                                    title: '', // إزالة عنوان الملف
                                    header: false, // إزالة رؤوس الأعمدة
                                    footer: false,
                                    copySuccess: {
                                        1: '%d سطر تم نسخه'
                                    }
                                }
                            ]
                        }
                    },
                    "language": {
                        "url": "{{ asset('files/Arabic.json')}}"
                    },
                    ajax: {
                        url: '{{ route("dashboard.allocations.index") }}',
                        data: function (d) {
                            // إضافة تواريخ التصفية إلى الطلب المرسل
                            d.from_date = $('#from_date').val();
                            d.to_date = $('#to_date').val();
                            d.from_date_implementation = $('#from_date_implementation').val();
                            d.to_date_implementation = $('#to_date_implementation').val();
                            d.year = $('#year').val();
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', status, error);
                        }
                    },
                    columns: [
                        { data: 'edit', name: 'edit', orderable: false, class: 'sticky text-center', searchable: false, render: function(data, type, row) {
                            @can('update','App\\Models\Allocation')
                            let link = `<button class="btn btn-icon btn-icon text-primary edit_row"  data-id=":allocation"><i class="fa-solid fa-edit"></i></button>`.replace(':allocation', data);
                            return link ;
                            @else
                            return '';
                            @endcan
                        }},
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, class: 'sticky text-center'}, // عمود الترقيم التلقائي
                        { data: 'date_allocation', name: 'date_allocation'  , orderable: false, class: 'sticky text-center'},
                        { data: 'budget_number', name: 'budget_number' , orderable: false, class: 'sticky text-center'},
                        { data: 'broker_name', name: 'broker_name' , orderable: false, class: 'sticky' },
                        { data: 'organization_name', name: 'organization_name' , orderable: false, render: function(data, type, row) {
                            return  `<span class="organization">${data}</span>`;
                        }},
                        { data: 'project_name', name: 'project_name', orderable: false},
                        { data: 'item_name', name: 'item_name'  , orderable: false},
                        { data: 'quantity', name: 'quantity'  , orderable: false, class: 'text-center'},
                        { data: 'price', name: 'price'  , orderable: false, class: 'text-center'},
                        { data: 'total_dollar', name: 'total_dollar', orderable: false, class: 'text-center', render: function(data, type, row) {
                            return  formatNumber(data,2);
                        }},
                        { data: 'allocation', name: 'allocation', orderable: false, class: 'text-center', render: function(data, type, row) {
                            return  formatNumber(data,2);
                        }},
                        // { data: 'currency_allocation_name_column', name: 'currency_allocation_name_column', orderable: false , searchable: true},
                        { data: 'currency_allocation_name', name: 'currency_allocation_name', orderable: false , searchable: true,render: function(data, type, row) {
                            return data;
                        }},
                        { data: 'currency_allocation_value', name: 'currency_allocation_value' , orderable: false, class: 'text-center', render: function(data, type, row) {
                            return  formatNumber((1/data),2);
                        }},
                        { data: 'amount', name: 'amount'  , orderable: false, class: 'text-center', render: function(data, type, row) {
                            return  formatNumber(data,2);
                        }},
                        { data: 'number_beneficiaries', name: 'number_beneficiaries'  , orderable: false, class: 'text-center'},
                        { data: 'implementation_items', name: 'implementation_items'  , orderable: false},
                        { data: 'date_implementation', name: 'date_implementation'  , orderable: false},
                        { data: 'implementation_statement', name: 'implementation_statement'  , orderable: false,},
                        { data: 'amount_received', name: 'amount_received'  , orderable: false, class: 'text-center', render: function(data, type, row) {
                            return  formatNumber(data,2);
                        }},
                        { data: 'arrest_receipt_number', name: 'arrest_receipt_number', orderable: false, class: 'text-center', render: function(data, type, row) {
                            return  formatNumber(data,0);
                        }},
                        { data: 'notes', name: 'notes'  , orderable: false},
                        { data: 'user_name', name: 'user_name'  , orderable: false},
                        { data: 'manager_name', name: 'manager_name'  , orderable: false},
                        { data: 'select', name: 'select', orderable: false, searchable: false, render: function (data, type, row) {
                            return `
                                <input type="checkbox" class="select_row form-check-input" name="id[]" value=":allocation">
                                `.replace(':allocation', data);
                        }},
                        { data: 'print', name: 'print', orderable: false, searchable: false, render: function (data, type, row) {
                            @can('print','App\\Models\Allocation')
                            return `
                                <form method="post" action="{{ route('dashboard.allocations.print', ':allocation') }}" target="_blank">
                                    @csrf
                                    <button
                                        class="btn btn-icon text-info">
                                        <i class="fa-solid fa-print"></i>
                                    </button>
                                </form>
                                `.replace(':allocation', data);
                            @else
                            return '';
                            @endcan
                        },},
                        { data: 'delete', name: 'delete', orderable: false, searchable: false, render: function (data, type, row) {
                            @can('delete','App\\Models\Allocation')
                            return `
                                <button
                                    class="btn btn-icon text-danger delete_row"
                                    data-id="${data}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>`;
                            @else
                            return '';
                            @endcan
                        },},
                        { data: 'currency_allocation', name: 'currency_allocation', orderable: false , class: 'd-none' ,render: function(data, type, row) {
                            return data;
                        }}
                    ],
                    columnDefs: [
                        { targets: 1, searchable: false, orderable: false } // تعطيل الفرز والبحث على عمود الترقيم
                    ],
                    drawCallback: function(settings) {
                        // تطبيق التنسيق على خلايا العمود المحدد
                        $('#allocations-table tbody tr').each(function() {
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
                        // count_allocations 1
                        var rowCount = display.length;
                        // total_quantity 8
                        var total_quantity_sum = api
                            .column(8, { page: 'current' }) // العمود الرابع
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        // total_allocation 11
                        var total_allocation_sum = api
                            .column(11, { page: 'current' }) // العمود الرابع
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        // total_amount 14
                        var total_amount_sum = api
                            .column(14, { page: 'current' }) // العمود الرابع
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        // total_number_beneficiaries 15
                        var total_number_beneficiaries_sum = api
                            .column(15, { page: 'current' }) // العمود الرابع
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        // total_amount_received 19
                        var total_amount_received_sum = api
                            .column(19, { page: 'current' }) // العمود الخامس
                            .data()
                            .reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);

                        // المتبقي
                        let remaining = total_amount_sum - total_amount_received_sum;
                        let remaining_percent = (total_amount_received_sum / total_amount_sum) * 100;

                        // 4. عرض النتائج في `tfoot`

                        $('#count_allocations').html(formatNumber(rowCount));
                        $('#total_quantity').html(formatNumber(total_quantity_sum));
                        $('#total_allocation').html(formatNumber(total_allocation_sum,2));
                        $('.total_amount').html(formatNumber(total_amount_sum,2));
                        $('#total_number_beneficiaries').html(formatNumber(total_number_beneficiaries_sum));
                        $('.total_amount_received').html(formatNumber(total_amount_received_sum,2));

                        $('.remaining').html(formatNumber(remaining,2));
                        $('.remaining_percent').html(formatNumber(remaining_percent,2));


                        // $('#allocations-table_filter').addClass('d-none');
                    }
                });
                // عندما يتم رسم الجدول (بعد تحميل البيانات أو تحديثها)
                table.on('draw', function() {
                    // التمرير إلى آخر سطر في الجدول
                    let tableContainer = $('#allocations-table');
                    tableContainer.scrollTop(tableContainer[0].scrollHeight);

                    $('#allocations-table thead tr').each(function() {
                        $(this).find('th.sticky').eq(0).css('right', (0) + 'px');
                        $(this).find('th.sticky').eq(1).css('right', (width0) + 'px');
                        $(this).find('th.sticky').eq(2).css('right', (width0 + width1) + 'px');
                        $(this).find('th.sticky').eq(3).css('right', (width0 + width1 + width2 - 5) + 'px');
                        $(this).find('th.sticky').eq(4).css('right', (width0 + width1 + width2 + width3 - 10) + 'px');
                    });
                    
                    $('#allocations-table tfoot tr').each(function() {
                        $(this).find('td.sticky').eq(0).css('right', (0) + 'px');
                        $(this).find('td.sticky').eq(1).css('right', (width0) + 'px');
                        $(this).find('td.sticky').eq(2).css('right', (width0 + width1) + 'px');
                    });
                });
                // نسخ وظيفة الزر إلى الزر المخصص
                $('#excel-export').on('click', function () {
                    table.button('.buttons-excel').trigger(); // استدعاء وظيفة الزر الأصلي
                });
                $('#copy-export').on('click', function () {
                    table.button('.buttons-copy').trigger(); // استدعاء وظيفة النسخ من مكتبة Buttons
                });
                $('#print-btn').on('click', function () {
                    table.button('.buttons-print').trigger(); // استدعاء وظيفة الطباعة الأصلية
                });
                $('#allocations-table_filter').addClass('d-none');
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
                    isColumnFiltered(3) ? '' : populateFilterOptions(3, '.checkbox-list-3','budget_name');
                    isColumnFiltered(4) ? '' : populateFilterOptions(4, '.checkbox-list-4','broker_name');
                    isColumnFiltered(5) ? '' : populateFilterOptions(5, '.checkbox-list-5','organization_name');
                    isColumnFiltered(6) ? '' : populateFilterOptions(6, '.checkbox-list-6','proj_name');
                    isColumnFiltered(7) ? '' : populateFilterOptions(7, '.checkbox-list-7','item_name');
                    isColumnFiltered(8) ? '' : populateFilterOptions(8, '.checkbox-list-8','quantity_field');
                    isColumnFiltered(11) ? '' : populateFilterOptions(11, '.checkbox-list-11','allocation_field');
                    // isColumnFiltered(25) ? '' : populateFilterOptions(25, '.checkbox-list-25','currency_allocation_field');
                    isColumnFiltered(14) ? '' : populateFilterOptions(14, '.checkbox-list-14','amount_field');
                    isColumnFiltered(15) ? '' : populateFilterOptions(15, '.checkbox-list-15','number_beneficiaries_field');
                    isColumnFiltered(19) ? '' : populateFilterOptions(19, '.checkbox-list-19','amount_received_field');
                    isColumnFiltered(20) ? '' : populateFilterOptions(20, '.checkbox-list-20','arrest_receipt_number_field');
                    for (let i = 1; i <= 20; i++) {
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
                // // تطبيق الفلترة عند الضغط على زر "check"
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
                        table.column(target).search(searchExpression, true, false).draw(); // Use regex search
                    } else {
                        // إذا لم تكن هناك قيم محددة، نعرض جميع البيانات
                        table.column(target).search('').draw();
                    }
                });
                // تطبيق التصفية عند النقر على زر "Apply"
                $('#filter-date-btn , #filter-date-implementation-btn').on('click', function () {
                    const fromDate = $('#from_date').val();
                    const toDate = $('#to_date').val();
                    const fromDateImplementation = $('#from_date_implementation').val();
                    const toDateImplementation = $('#to_date_implementation').val();
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
                        url: '{{ route("dashboard.allocations.destroy", ":id") }}'.replace(':id', id),
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
                    editAllocationForm(id); // استدعاء وظيفة الحذف
                });
                const allocation = {
                    id: '',
                    date_allocation : '',
                    budget_number : '',
                    broker_name : '',
                    organization_name : '',
                    project_name : '',
                    item_name : '',
                    quantity : '',
                    price : '',
                    total_dollar : '',
                    allocation : '',
                    currency_allocation : '',
                    currency_allocation_name : '',
                    currency_allocation_value : '',
                    amount : '',
                    number_beneficiaries : '',
                    implementation_items : '',
                    date_implementation : '',
                    implementation_statement : '',
                    amount_received : '',
                    arrest_receipt_number : '',
                    notes : '',
                    user_name : '',
                    manager_name : '',
                    user : '',
                }
                function editAllocationForm(id) {
                    $.ajax({
                        url: '{{ route("dashboard.allocations.edit", ":id") }}'.replace(':id', id),
                        method: 'GET',
                        success: function (response) {
                            allocation.id = response.id;
                            allocation.date_allocation = response.date_allocation;
                            allocation.budget_number = response.budget_number;
                            allocation.broker_name = response.broker_name;
                            allocation.organization_name = response.organization_name;
                            allocation.project_name = response.project_name;
                            allocation.item_name = response.item_name;
                            allocation.quantity = response.quantity;
                            allocation.price = response.price;
                            allocation.total_dollar = response.total_dollar;
                            allocation.allocation = response.allocation;
                            allocation.currency_allocation = response.currency_allocation;
                            allocation.currency_allocation_name = response.currency_allocation_name;
                            allocation.currency_allocation_value = formatNumber(1 / response.currency_allocation_value, 2);
                            allocation.amount = response.amount;
                            allocation.number_beneficiaries = response.number_beneficiaries;
                            allocation.implementation_items = response.implementation_items;
                            allocation.date_implementation = response.date_implementation;
                            allocation.implementation_statement = response.implementation_statement;
                            allocation.amount_received = response.amount_received;
                            allocation.arrest_receipt_number = response.arrest_receipt_number;
                            allocation.notes = response.notes;
                            allocation.user_name = response.user_name;
                            allocation.manager_name = response.manager_name;
                            allocation.user = response.user;
                            $.each(allocation, function(key, value) {
                                const input = $('#' + key); // البحث عن العنصر باستخدام id
                                if (input.length) { // التحقق إذا كان العنصر موجودًا
                                    input.val(value); // تعيين القيمة
                                }
                            });
                            $('#addAllocation').remove();
                            $('#update').remove();
                            $('#btns_form').append(`
                                <button type="button" id="update" class="btn btn-primary mx-2">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    تعديل
                                </button>
                            `);
                            $('.editForm').css('display','block');
                            $('#editAllocation').modal('show');
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
                        let totalDollar = quantity * price;
                        let currencyAllocation = $('#currency_allocation_value').val() || 0; //إذا كان الحقل فارغًا، اعتبر القيمة 0
                        $('#total_dollar').val(totalDollar);
                        $('#allocation').val(totalDollar);
                        $('#amount').val(parseFloat(totalDollar) / currencyAllocation);
                    }
                });
                $('#currency_allocation').on('input blur', function () {
                    var currencyAllocation = parseFloat($('#currency_allocation option:selected').data('val')) || 0; //إذا كان الحقل فارغًا، اعتبر القيمة 0
                    $('#currency_allocation_value').val(1 / currencyAllocation)
                    $('#currency_allocation_value').trigger('input');
                });
                $('#allocation,#currency_allocation_value').on('input blur', function () {
                    // جلب القيم من الحقول
                    var allocation = parseFloat($('#allocation').val()) || 0; // إذا كان الحقل فارغًا، اعتبر القيمة 0
                    var amount = allocation / $('#currency_allocation_value').val();
                    $('#amount').val(amount);
                });
                $(document).on('click', '#update', function () {
                    $.each(allocation, function(key, value) {
                        const input = $('#' + key); // البحث عن العنصر باستخدام id
                        if(key == 'id'){
                            //
                        }else if(key == 'currency_allocation_value'){
                            allocation['currency_allocation_value'] = 1 / $('#currency_allocation_value').val();
                        }else{
                            allocation[key] = input.val();
                        }
                    });
                    $.ajax({
                        url: "{{ route('dashboard.allocations.update', ':id') }}".replace(':id', allocation.id),
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: allocation,
                        success: function (response) {
                            $('#editAllocation').modal('hide');
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
                        url: '{{ route("dashboard.allocations.create") }}',
                        method: 'GET',
                        success: function (response) {
                            allocation.id = response.id;
                            allocation.date_allocation = response.date_allocation;
                            allocation.budget_number = response.budget_number;
                            allocation.broker_name = response.broker_name;
                            allocation.organization_name = response.organization_name;
                            allocation.project_name = response.project_name;
                            allocation.item_name = response.item_name;
                            allocation.quantity = response.quantity;
                            allocation.price = response.price;
                            allocation.total_dollar = response.total_dollar;
                            allocation.allocation = response.allocation;
                            allocation.currency_allocation = response.currency_allocation;
                            allocation.currency_allocation_name = response.currency_allocation_name;
                            allocation.currency_allocation_value = response.currency_allocation_value;
                            allocation.amount = response.amount;
                            allocation.number_beneficiaries = response.number_beneficiaries;
                            allocation.implementation_items = response.implementation_items;
                            allocation.date_implementation = response.date_implementation;
                            allocation.implementation_statement = response.implementation_statement;
                            allocation.amount_received = response.amount_received;
                            allocation.arrest_receipt_number = response.arrest_receipt_number;
                            allocation.notes = response.notes;
                            allocation.user_name = response.user_name;
                            allocation.manager_name = response.manager_name;
                            $.each(allocation, function(key, value) {
                                const input = $('#' + key); // البحث عن العنصر باستخدام id
                                if (input.length) { // التحقق إذا كان العنصر موجودًا
                                    input.val(value); // تعيين القيمة
                                }
                            });
                            $('#addAllocation').remove();
                            $('#update').remove();
                            $('#btns_form').append(`
                                <button type="button" id="addAllocation" class="btn btn-primary mx-2">
                                    <i class="fa-solid fa-plus"></i>
                                    أضف
                                </button>
                            `);
                            $('.editForm').css('display','none');
                            $('#editAllocation').modal('show');
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', status, error);
                            alert('هنالك خطأ في الإتصال بالسيرفر.');
                        },
                    })
                });
                $(document).on('click', '#addAllocation', function () {
                    const id = $(this).data('id'); // الحصول على ID الصف
                    createAllocationForm(id);
                });
                function createAllocationForm(id){
                    $.each(allocation, function(key, value) {
                        const input = $('#' + key); // البحث عن العنصر باستخدام id
                        if(key == 'id'){
                            allocation['id'] = null;
                        }else if(key == 'currency_allocation_value'){
                            allocation['currency_allocation_value'] = 1 / $('#currency_allocation_value').val();
                        }else{
                            allocation[key] = input.val();
                        }
                    });
                    console.log(allocation);
                    $.ajax({
                        url: "{{ route('dashboard.allocations.store') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: allocation,
                        success: function (response) {
                            $('#editAllocation').modal('hide');
                            table.ajax.reload();
                            alert('تم إضافة التخصيص بنجاح');
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
