@push('styles')
    <link rel="stylesheet" href="{{ asset('css/custom/select2.min.css') }}">
@endpush
<div class="container-fluid">
    <input type="hidden" name="type" value="executive">

    <h3> {{ $btn_label ?? 'اضافة' }} مشروع إعتماد - تنفيذ </h3>
    <h5 class="my-4">بيانات التنفيذ</h5>
    <div class="row">
        {{-- <div class="form-group col-md-3 my-2">
            <x-form.input type="number" name="budget_number" label="رقم الموازنة" wire:model="budget_number" placeholder="رقم الموزانة : 1212" class="text-center" required wire:input="budget_number_check($event.target.value)" />
            <div id="budget_number_error" class="text-danger" >
                @if ($budget_number_error != '')
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span title="يمكنك جعل الرقم لتخصيص آخر هذا فقط تحذير">{{ $budget_number_error  }}</span>
                @endif
            </div>
        </div> --}}
        <div class="form-group col-md-3 my-2">
            <x-form.input type="date" name="implementation_date" label="التاريخ" required :value="$accreditation->implementation_date" />
        </div>
        <div class="form-group col-md-3 my-2">
            <label for="broker_name">المؤسسة</label>
            <select class="form-select text-center" name="broker_name" id="broker_name">
                <option label="فتح القائمة">
                    @foreach ($brokers as $broker)
                        <option value="{{ $broker }}" @selected($broker == $accreditation->broker_name)>{{ $broker }}</option>
                    @endforeach
            </select>
        </div>
        <div class="form-group col-md-3 my-2">
            <x-form.input name="account" label="الحساب" list="account_list" required :value="$accreditation->account" />
            <datalist id="account_list">
                @foreach ($accounts as $account)
                    <option value="{{ $account }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3 my-2">
            <x-form.input name="affiliate_name" label="الاسم" list="affiliate_name_list" required :value="$accreditation->affiliate_name" />
            <datalist id="affiliate_name_list">
                @foreach ($affiliates as $affiliate_name)
                    <option value="{{ $affiliate_name }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3 my-2">
            <label for="project_name">المشروع</label>
            <x-form.input name="project_name" list="projects_list" required :value="$accreditation->project_name" />
            <datalist id="projects_list">
                @foreach ($projects as $project)
                    <option value="{{ $project }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3 my-2">
            <x-form.input name="detail" label="التفصيل.." list="detail_list" :value="$accreditation->detail" />
            <datalist id="detail_list">
                @foreach ($details as $detail)
                    <option value="{{ $detail }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3 my-2">
            <label for="item_name">الصنف</label>
            <x-form.input name="item_name" list="items_list" required :value="$accreditation->item_name" />
            <datalist id="items_list">
                @foreach ($items as $item)
                    <option value="{{ $item }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3 my-2">
            <x-form.input type="text" class="calculation"  name="quantity" label="الكمية" :value="$accreditation->quantity"/>
        </div>
        <div class="form-group col-md-3 my-2">
            <x-form.input type="text" class="calculation"  min="0" step="0.01" name="price" label="سعر الوحدة ₪" :value="$accreditation->price" />
        </div>
        <div class="form-group col-md-3 my-2">
            <x-form.input type="text" class="calculation"  min="0" step="0.01" name="total_ils" label="الإجمالي ب ₪" :value="$accreditation->total_ils" />
        </div>
        <div class="form-group col-md-3 my-2">
            <x-form.input name="received" label="المستلم" list="received_list" :value="$accreditation->received" />
            <datalist id="received_list">
                @foreach ($receiveds as $received)
                    <option value="{{ $received }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-12">
            <x-form.textarea name="notes" label="ملاجظات" rows="2" :value="$accreditation->notes" />
        </div>
    </div>
    <hr>
    <h3>بنود الدفع</h3>
    <div class="row">
        <div class="form-group col-md-3 my-2">
            <x-form.input type="text" class="calculation" min="0" step="0.01" name="amount_payments" label="الدفعات" :value="$accreditation->amount_payments" />
        </div>
        <div class="form-group col-md-6">
            <x-form.textarea name="payment_mechanism" label="آلية الدفع" rows="2" :value="$accreditation->payment_mechanism"  />
        </div>
    </div>
    <hr>
    @if (isset($btn_label))
    <div class="row">
        <div class="form-group col-md-3 my-2">
            <x-form.input name="user_id" label="اسم المستخدم" :value="$accreditation->user_name"  disabled />
        </div>
        <div class="form-group col-md-3 my-2">
            <x-form.input name="manager_name" label="المدير المستلم" :value="$accreditation->manager_name" disabled />
        </div>
    </div>
    @endif

    <div class="d-flex justify-content-end"  id="btns_form">
        @if (isset($btn_label))
            @can('adoption','App\\Models\AccreditationProject')
            <button type="button" class="btn btn-success btn-sm p-2 mx-2" id="adoption">
                <i class="fa-solid fa-check"></i> إعتماد
            </button>
            @endcan
        @endif

        <button type="submit" id="update" class="btn btn-primary mx-2">
            <i class="fa-solid fa-edit"></i>
            {{ $btn_label ?? 'إضافة' }}
        </button>
    </div>
    {{-- <div class="form-group col-md-4">
        <x-form.input type="file" name="filesArray[]" label="رفع ملفات للتنفيذ" multiple />
    </div> --}}
</div>
@push('scripts')
    <script>
        const csrf_token = "{{ csrf_token() }}";
        const app_link = "{{ config('app.url') }}";
    </script>
    <script src='{{ asset('js/plugins/select2.min.js') }}'></script>
    <script>


        $(document).ready(function() {

            $('#broker_name').select2();

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

            // حساب المبلغ
            // $(document).on('input', '.calculation', function () {});
            $(document).on('input', '#quantity , #price', function () {
                let quantity = $('#quantity').val();
                let price = $('#price').val();
                if(quantity != '' && price != ''){
                    quantity = parseFloat(quantity) || 0;
                    price = parseFloat(price) || 0;
                    let total_ils = quantity * price;
                    $('#total_ils').val(total_ils);
                }
            });

            $('#adoption').click(function() {
                let form = new FormData();
                form.append('adoption', 1);
                form.append('type', 'executive');
                form.append('_token', csrf_token);
                $.ajax({
                    url: `{{route('dashboard.accreditations.adoption', $accreditation->id ?? '0')}}`,
                    type: 'POST',
                    data: form,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        window.location.href = '{{route('dashboard.accreditations.index')}}';
                    },
                    error: function(err) {
                        console.log(err);
                    }
                })
            });
        });
    </script>
@endpush