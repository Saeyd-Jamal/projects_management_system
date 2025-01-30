@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom/select2.min.css') }}">
@endpush
<div class="container-fluid">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <input type="hidden" name="type" value="allocation">
    <input type="hidden" name="num_allo" id="num_allo">
    <h3> {{ $btn_label ?? 'اضافة' }} مشروع إعتماد - تخصيص </h3>
    <h5 class="my-4">بيانات التخصيص الأساسية</h5>
    <div class="row">
        <div class="form-group col-md-3 my-2">
            <x-form.input type="number" name="budget_number" label="رقم الموازنة" :value="$allocation->budget_number"
                placeholder="رقم الموزانة : 1212" class="text-center" required />
            <div id="budget_number_error" class="text-danger">
                {{-- @if ($budget_number_error != '')
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span title="يمكنك جعل الرقم لتخصيص آخر هذا فقط تحذير">{{ $budget_number_error  }}</span>
                @endif --}}
            </div>
        </div>
        <div class="form-group col-md-3 my-2">
            <x-form.input type="date" name="date_allocation" label="تاريخ التخصيص" :value="$allocation->date_allocation" required />
        </div>
        <div class="form-group col-md-3 my-2">
            <label for="broker_name">المؤسسة</label>
            <select class="form-select text-center" name="broker_name" id="broker_name">
                <option label="فتح القائمة">
                    @foreach ($brokers as $broker)
                <option value="{{ $broker }}" @selected($broker == $allocation->broker_name)>{{ $broker }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-3 my-2">
            <label for="organization_name">المتبرع</label>
            <x-form.input name="organization_name" :value="$allocation->organization_name" list="organizations_list" required />
            <datalist id="organizations_list">
                @foreach ($organizations as $organization)
                    <option value="{{ $organization }}">
                @endforeach
            </datalist>
        </div>
    </div>
    <hr>
    <h5>التخصيصات المتعددة</h5>
    <div class="row p-2" id="allocations">
        
    </div>
    <div class="d-flex justify-content-between align-items-center my-4">
        <div class="btn btn-danger d-none" id="remove-allocation">
            <i class="fa-solid fa-trash me-1"></i>
            حذف أخر تخصيص
        </div>
        <div class="btn btn-primary" id="add-allocation">
            <i class="fa-solid fa-plus me-1"></i>
            اضافة تخصيص
        </div>
    </div>
    <hr>
    <div class="row">
        @if (isset($btn_label))
            <div class="form-group col-md-3 my-2">
                <x-form.input name="user_name" label="اسم المستخدم" disabled :value="$allocation->user_name" />
            </div>
            <div class="form-group col-md-3 my-2 ">
                <x-form.input name="manager_name" label="المدير المستلم" disabled :value="$allocation->manager_name" />
            </div>
        @endif
        <div class="form-group col-md-12">
            <x-form.textarea name="notes" label="ملاجظات عن التخصيص" :value="$allocation->notes" rows="2" />
        </div>
    </div>
    <div class="d-flex justify-content-end mt-3" id="btns_form">
        <button type="submit" id="update" class="btn btn-primary mx-2">
            <i class="fa-solid fa-edit me-1"></i>
            {{ $btn_label ?? 'إضافة' }}
        </button>
    </div>
    {{-- <div class="form-group col-md-4">
        <x-form.input type="file" name="filesArray[]" label="رفع ملفات للتخصيص" multiple />
    </div> --}}
</div>
@push('scripts')
    <script>
        const csrf_token = "{{ csrf_token() }}";
        const app_link = "{{ config('app.url') }}";
    </script>
    <script src='{{ asset('js/plugins/select2.min.js') }}'></script>
    <script>
        $('#broker_name').select2();

        $(document).ready(function() {
            $(document).on('blur keypress', '.calculation', function (event) {
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

            $(document).on('keypress', 'form', function (event) {
                // تحقق إذا كان الحدث هو الضغط على مفتاح
                if (event.key == "Enter") {
                    event.preventDefault();
                    return;
                }
            });


            function addAllocation() {
                const allocationCount = $('.allocation-card').length;
                let i = allocationCount + 1;

                const html = `
                    <div class="card shadow mb-3 allocation-card" id="allocation-card_${i}">
                        <div class="card-body">
                            <h5>بنود التخصيص رقم <span class="text-danger">${i}</span></h5>
                            <div class="row">    
                                <div class="form-group col-md-3">
                                    <label for="project_name_${i}">المشروع</label>
                                    <x-form.input name="project_name_${i}" list="projects_list_${i}" required />
                                    <datalist id="projects_list_${i}">
                                        @foreach ($projects as $project)
                                            <option value="{{ $project }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>
                    
                                <div class="form-group col-md-3">
                                    <label for="item_name_${i}">الصنف</label>
                                    <x-form.input name="item_name_${i}" list="items_list_${i}" required />
                                    <datalist id="items_list_${i}">
                                        @foreach ($items as $item)
                                            <option value="{{ $item }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>
                    
                                <div class="form-group col-md-3">
                                    <x-form.input type="text" min="0" name="quantity_${i}" class="calculation quantity" data-index="${i}"  label="الكمية" />
                                </div>
                    
                                <div class="form-group col-md-3">
                                    <x-form.input type="text" min="0" step="0.01" name="price_${i}" label="سعر الوحدة" class="calculation price" data-index="${i}" />
                                </div>
                    
                                <div class="form-group col-md-3">
                                    <x-form.input type="text" min="0" step="0.01" name="total_dollar_${i}" label="الإجمالي" class="total_dollar" data-index="${i}" readonly />
                                </div>
                    
                                <div class="form-group col-md-3">
                                    <x-form.input type="text" min="0" step="0.01" name="allocation_${i}" label="التخصيص" class="calculation allocation" data-index="${i}" />
                                </div>
                    
                                <div class="form-group col-md-3">
                                    <label for="currency_allocation_${i}">العملة</label>
                                    <select class="form-control text-center currency_allocation" name="currency_allocation_${i}" data-index="${i}">
                                        <option label="اختر العملة"></option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->code }}" data-val="{{ $currency->value }}" @selected($currency->code == "USD")>{{ $currency->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                    
                                <div class="form-group col-md-3">
                                    <x-form.input type="text" min="0" step="0.01" name="currency_allocation_value_${i}" label="سعر الدولار للعملة" class="calculation currency_allocation_value" :value="$USD" data-index="${i}" />
                                </div>
                    
                                <div class="form-group col-md-3">
                                    <x-form.input type="number" min="0" step="0.0000001" name="amount_${i}" label="المبلغ $" 
                                        class="amount" data-index="${i}" readonly />
                                </div>
                    
                                <div class="form-group col-md-3">
                                    <x-form.input type="text" min="0" class="calculation" name="number_beneficiaries_${i}" 
                                        label="عدد المستفيدين" class="calculation number_beneficiaries" data-index="${i}" />
                                </div>
                    
                                <div class="form-group col-md-6">
                                    <x-form.textarea name="implementation_items_${i}" class="implementation_items" data-index="${i}" label="بنود التنفيذ" />
                                </div>
                
                            </div>
                            <h5>بنود القبض</h5>
                            <div class="row">
                                <div class="form-group col-md-3 my-2">
                                    <x-form.input type="date" name="date_implementation_${i}" label="تاريخ القبض" class="date_implementation" data-index="${i}" />
                                </div>
                                <div class="form-group col-md-3 my-2">
                                    <x-form.input type="text" class="calculation amount_received" min="0" step="0.01" name="amount_received_${i}"
                                        data-index="${i}" label="المبلغ المقبوض" />
                                </div>
                                <div class="form-group col-md-3 my-2">
                                    <x-form.input type="number" min="0" name="arrest_receipt_number_${i}" label="رقم إيصال القبض"
                                        class="arrest_receipt_number" data-index="${i}" />
                                </div>
                                <div class="form-group col-md-6">
                                    <x-form.textarea name="implementation_statement_${i}" data-index="${i}" class="implementation_statement" label="بيان" rows="2" />
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#allocations').append(html);
                $('#num_allo').val(allocationCount);
            }

            function removeAllocation(index) {
                $('#allocation-card_' + index).remove();
            }

            $(document).on('click', '#add-allocation', function () {
                $('#remove-allocation').removeClass('d-none');
                addAllocation();
            });

            $(document).on('click', '#remove-allocation', function () {
                const allocationCount = $('.allocation-card').length;
                const index = allocationCount;
                removeAllocation(index);
                if(allocationCount == 1) {
                    $('#remove-allocation').addClass('d-none');
                }
                $('#num_allo').val(allocationCount);
            });


            // حساب المبلغ
            // $(document).on('input', '.calculation', function () {});
            $(document).on('input', '.quantity , .price', function () {
                const index = $(this).data('index');

                let quantity = $('#quantity_' + index).val();
                let price = $('#price_' + index).val();
                if(quantity != '' && price != ''){
                    quantity = parseFloat(quantity) || 0;
                    price = parseFloat(price) || 0;
                    let totalDollar = quantity * price;
                    let currencyAllocation = $('#currency_allocation_value_' + index).val() || 0; //إذا كان الحقل فارغًا، اعتبر القيمة 0
                    $('#total_dollar_' + index).val(totalDollar);
                    $('#allocation_' + index).val(totalDollar);
                    $('#amount_' + index).val(parseFloat(totalDollar) / currencyAllocation);
                }
            });

            $(document).on('input', '.currency_allocation_value', function () {
                const index = $(this).data('index');

                var currencyAllocation = parseFloat($('#currency_allocation_value_' + index).val()) || 0; //إذا كان الحقل فارغًا، اعتبر القيمة 0
                $('#amount_' + index).val(parseFloat($('#allocation_' + index).val()) / currencyAllocation);
            });

            $(document).on('change', '.currency_allocation', function () {
                const index = $(this).data('index');
                var currencyAllocation = parseFloat($(this).find('option:selected').data('val')) || 0; //إذا كان الحقل فارغًا، اعتبر القيمة 0
                $('#currency_allocation_value_' + index).val(1 / currencyAllocation)
                $('#currency_allocation_value_' + index).trigger('input');
            });




        });
    </script>
@endpush
