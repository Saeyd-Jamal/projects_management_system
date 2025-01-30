<x-front-layout>
    @push('styles')
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="{{asset('css/datatable/jquery.dataTables.min.css')}}">
        <link rel="stylesheet" href="{{asset('css/datatable/dataTables.bootstrap4.css')}}">
        <link rel="stylesheet" href="{{asset('css/datatable/dataTables.dataTables.css')}}">
        <link rel="stylesheet" href="{{asset('css/datatable/buttons.dataTables.css')}}">

        <link id="stickyTableLight" rel="stylesheet" href="{{ asset('css/custom/stickyTable.css') }}">
        <link id="stickyTableDark" rel="stylesheet" href="{{ asset('css/custom/stickyTableDark.css') }}" disabled>
        <link rel="stylesheet" href="{{ asset('css/custom/style.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom/datatableIndex.css') }}">
    @endpush
    <x-slot:extra_nav>
        @can('create', 'App\\Models\Broker')
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-body table-container p-0">
                    <table id="brokers-table" class="table table-striped table-bordered table-hover sticky" style="width:100%; height: calc(100vh - 200px);">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-white opacity-7 text-center">#</th>
                                <th class="sticky" style="right: 0px;" >
                                    <div class='d-flex align-items-center justify-content-between'>
                                        <span>المؤسسة</span>
                                        <div class="filter-dropdown ml-4">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-filter" id="btn-filter-2" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa-brands fa-get-pocket text-white"></i>
                                                </button>
                                                <div class="filterDropdownMenu dropdown-menu dropdown-menu-right p-2" aria-labelledby="name_filter">
                                                    <!-- إضافة checkboxes بدلاً من select -->
                                                    <div class="searchable-checkbox">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <input type="search" class="form-control search-checkbox" data-index="2" placeholder="ابحث...">
                                                            <button class="btn btn-success text-white filter-apply-btn-checkbox" data-target="2" data-field="name">
                                                                <i class="fa-solid fa-check"></i>
                                                            </button>
                                                        </div>
                                                        <div class="checkbox-list-box">
                                                            <label style="display: block;">
                                                                <input type="checkbox" value="all" class="all-checkbox" data-index="2"> الكل
                                                            </label>
                                                            <div class="checkbox-list checkbox-list-2">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>الملاحظات</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen modal -->
    <div class="modal fade modal-full" id="editBroker" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <button aria-label="" type="button" class="close px-2" data-bs-dismiss="modal" aria-hidden="true">
            <span aria-hidden="true">×</span>
        </button>
        <div class="modal-dialog modal-dialog-centered w-100" role="document" style="max-width: 95%;">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <form id="editForm">
                        @include('dashboard.brokers.editModal')
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
                let table = $('#brokers-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    paging: false,              // تعطيل الترقيم
                    searching: true,            // الإبقاء على البحث إذا كنت تريده
                    info: false,                // تعطيل النص السفلي الذي يوضح عدد السجلات
                    lengthChange: false,        // تعطيل قائمة تغيير عدد المدخلات
                    "language": {
                        "url": "{{ asset('files/Arabic.json')}}"
                    },
                    ajax: {
                        url: '{{ route("dashboard.brokers.index") }}',
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', status, error);
                        }
                    },
                    columns: [
                        { data: 'edit', name: 'edit', orderable: false, class: 'text-center', searchable: false, render: function(data, type, row) {
                            @can('update','App\\Models\Broker')
                            let link = `<button class="btn btn-sm p-1 btn-icon text-primary edit_row"  data-id=":broker"><i class="fa-solid fa-edit"></i></button>`.replace(':broker', data);
                            return link ;
                            @else
                            return '';
                            @endcan
                        }},
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, class: 'text-center'}, // عمود الترقيم التلقائي
                        { data: 'name', name: 'name'  , orderable: false, class: 'sticky text-center'},
                        { data: 'notes', name: 'notes'  , orderable: false},
                        { data: 'delete', name: 'delete', orderable: false, searchable: false, render: function (data, type, row) {
                            @can('delete','App\\Models\Broker')
                            return `
                                <button
                                    class="btn btn-icon p-1 text-danger delete_row"
                                    data-id="${data}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>`;
                            @else
                            return '';
                            @endcan
                        },},
                    ],
                    columnDefs: [
                        { targets: 1, searchable: false, orderable: false } // تعطيل الفرز والبحث على عمود الترقيم
                    ],
                    drawCallback: function(settings) {
                    },
                    footerCallback: function(row, data, start, end, display) {
                        // var api = this.api();
                        // // تحويل القيم النصية إلى أرقام
                        // var intVal = function(i) {
                        //     return typeof i === 'string' ?
                        //         parseFloat(i.replace(/[\$,]/g, '')) :
                        //         typeof i === 'number' ? i : 0;
                        // };
                        // // 1. حساب عدد الأسطر في الصفحة الحالية
                        // // count_allocations 1
                        // var rowCount = display.length;
                        // // total_quantity 8
                        


                        // // $('#brokers-table_filter').addClass('d-none');
                    }
                });
                // عندما يتم رسم الجدول (بعد تحميل البيانات أو تحديثها)
                table.on('draw', function() {
                    // التمرير إلى آخر سطر في الجدول
                    let tableContainer = $('#brokers-table');
                    tableContainer.scrollTop(tableContainer[0].scrollHeight);
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
                    isColumnFiltered(2) ? '' : populateFilterOptions(2, '.checkbox-list-2','name');

                    for (let i = 1; i <= 3; i++) {
                        if (isColumnFiltered(i)) {
                            $('#btn-filter-' + i).removeClass('btn-secondary');
                            $('#btn-filter-' + i + ' i').removeClass('fa-get-pocket');
                            $('#btn-filter-' + i + ' i').addClass('fa-filter');
                            $('#btn-filter-' + i).addClass('btn-success');
                        }else{
                            $('#btn-filter-' + i + ' i').removeClass('fa-filter');
                            $('#btn-filter-' + i).removeClass('btn-success');
                            $('#btn-filter-' + i).addClass('btn-secondary');
                            $('#btn-filter-' + i + ' i').addClass('fa-get-pocket');
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
                $('#filter-date-btn').on('click', function () {
                    const fromDate = $('#from_date').val();
                    const toDate = $('#to_date').val();
                    table.ajax.reload(); // إعادة تحميل الجدول مع التواريخ المحدثة
                });
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
                        url: '{{ route("dashboard.brokers.destroy", ":id") }}'.replace(':id', id),
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
                    editBrokerForm(id); // استدعاء وظيفة الحذف
                });
                const broker = {
                    id: '',
                    name : '',
                    notes : '',
                }
                function editBrokerForm(id) {
                    $.ajax({
                        url: '{{ route("dashboard.brokers.edit", ":id") }}'.replace(':id', id),
                        method: 'GET',
                        success: function (response) {
                            broker.id = response.id;
                            broker.name = response.name;
                            broker.notes = response.notes;
                            $.each(broker, function(key, value) {
                                const input = $('#' + key); // البحث عن العنصر باستخدام id
                                if (input.length) { // التحقق إذا كان العنصر موجودًا
                                    input.val(value); // تعيين القيمة
                                }
                            });
                            $('#addBroker').remove();
                            $('#update').remove();
                            $('#btns_form').append(`
                                <button type="button" id="update" class="btn btn-primary mx-2">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    تعديل
                                </button>
                            `);
                            $('.editForm').css('display','block');
                            $('#editBroker').modal('show');
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', status, error);
                            alert('هنالك خطأ في الإتصال بالسيرفر.');
                        },
                    })
                }
                $(document).on('click', '#update', function () {
                    $.each(broker, function(key, value) {
                        const input = $('#' + key); // البحث عن العنصر باستخدام id
                        if(key == 'id'){
                            //
                        }else{
                            broker[key] = input.val();
                        }
                    });
                    $.ajax({
                        url: "{{ route('dashboard.brokers.update', ':id') }}".replace(':id', broker.id),
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: broker,
                        success: function (response) {
                            $('#editBroker').modal('hide');
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
                        url: '{{ route("dashboard.brokers.create") }}',
                        method: 'GET',
                        success: function (response) {
                            broker.id = response.id;
                            broker.name = response.name;
                            broker.notes = response.notes;
                            $.each(broker, function(key, value) {
                                const input = $('#' + key); // البحث عن العنصر باستخدام id
                                if (input.length) { // التحقق إذا كان العنصر موجودًا
                                    input.val(value); // تعيين القيمة
                                }
                            });
                            $('#addBroker').remove();
                            $('#update').remove();
                            $('#btns_form').append(`
                                <button type="button" id="addBroker" class="btn btn-primary mx-2">
                                    <i class="fa-solid fa-plus"></i>
                                    أضف
                                </button>
                            `);
                            $('.editForm').css('display','none');
                            $('#editBroker').modal('show');
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', status, error);
                            alert('هنالك خطأ في الإتصال بالسيرفر.');
                        },
                    })
                });
                $(document).on('click', '#addBroker', function () {
                    const id = $(this).data('id'); // الحصول على ID الصف
                    createBrokerForm(id);
                });
                function createBrokerForm(id){
                    $.each(broker, function(key, value) {
                        const input = $('#' + key); // البحث عن العنصر باستخدام id
                        if(key == 'id'){
                            broker['id'] = null;
                        }else{
                            broker[key] = input.val();
                        }
                    });
                    console.log(broker);
                    $.ajax({
                        url: "{{ route('dashboard.brokers.store') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: broker,
                        success: function (response) {
                            $('#editBroker').modal('hide');
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
                $(document).on('click', '#import_excel_btn', function() {
                    $('#editBroker').modal('hide');
                    $('#import_excel').modal('show');
                })
            });
        </script>
    @endpush
</x-front-layout>
