@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom/select2.min.css') }}">
@endpush
<div class="container-fluid">
<h3> {{ $btn_label ?? 'اضافة' }} مشروع إعتماد - تخصيص </h3>
<h5>بيانات التخصيص</h5>
<div class="row">
    <div class="form-group col-md-3 my-2">
        <x-form.input type="number" name="budget_number" label="رقم الموازنة" :value="$accreditation->budget_number"
            placeholder="رقم الموزانة : 1212" class="text-center" required />
        <div id="budget_number_error" class="text-danger">
        </div>
    </div>
    <div class="form-group col-md-3 my-2">
        <x-form.input type="date" name="date_allocation" label="تاريخ التخصيص" :value="$accreditation->date_allocation"
            required />
    </div>
    <div class="form-group col-md-3 my-2">
        <label for="broker_name">المؤسسة</label>
        <select class="form-select text-center" name="broker_name" id="broker_name">
            <option label="فتح القائمة">
                @foreach ($brokers as $broker)
                <option value="{{ $broker }}" @selected($broker == $accreditation->broker_name)>{{ $broker }}
                </option>
                @endforeach
        </select>
    </div>
    <div class="form-group col-md-3 my-2">
        <label for="organization_name">المتبرع</label>
        <x-form.input name="organization_name" :value="$accreditation->organization_name" list="organizations_list" required />
        <datalist id="organizations_list">
            @foreach ($organizations as $organization)
                <option value="{{ $organization }}">
            @endforeach
        </datalist>
    </div>
    <div class="form-group col-md-3 my-2">
        <label for="project_name">المشروع</label>
        <x-form.input name="project_name" :value="$accreditation->project_name" list="projects_list" required />
        <datalist id="projects_list">
            @foreach ($projects as $project)
                <option value="{{ $project }}">
            @endforeach
        </datalist>
    </div>
    <div class="form-group col-md-3 my-2">
        <label for="item_name">الصنف</label>
        <x-form.input name="item_name" :value="$accreditation->item_name" list="items_list" required />
        <datalist id="items_list">
            @foreach ($items as $item)
                <option value="{{ $item }}">
            @endforeach
        </datalist>
    </div>
    <div class="form-group col-md-3 my-2">
        <x-form.input type="text" class="calculation" min="0" :value="$accreditation->quantity"
            name="quantity" label="الكمية" />
    </div>
    <div class="form-group col-md-3 my-2">
        <x-form.input type="text" class="calculation" min="0" :value="$accreditation->price" step="0.01"
            name="price" label="سعر الوحدة" />
    </div>
    <div class="form-group col-md-3 my-2">
        <x-form.input type="number" min="0" step="0.01" :value="$accreditation->total_dollar" name="total_dollar"
            label="الإجمالي" readonly />
    </div>
    <div class="form-group col-md-3 my-2">
        <x-form.input type="text" class="calculation" min="0" step="0.01" :value="$accreditation->allocation"
            name="allocation" label="التخصيص" required />
    </div>
    <div class="form-group col-md-3 my-2">
        <label for="currency_allocation">العملة</label>
        <select class="form-control text-center" name="currency_allocation" id="currency_allocation">
            <option label="فتح القائمة">
                @foreach ($currencies as $currency)
                    {{-- <option value="{{ $currency->code }}" @selected($currency->code == $accreditation->currency_allocation || $currency->code == "USD")>{{ $currency->name }}</option> --}}
            <option value="{{ $currency->code }}" data-val="{{ $currency->value }}"
                @selected($currency->code == $accreditation->currency_allocation)>{{ $currency->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-3 my-2">
        <x-form.input type="text" class="calculation" min="0" required :value="$accreditation->currency_allocation_value"
            name="currency_allocation_value" label="سعر الدولار للعملة" />
    </div>
    <div class="form-group col-md-3 my-2">
        <x-form.input type="number" min="0" step="0.01" name="amount" :value="$accreditation->amount"
            label="المبلغ $" readonly />
    </div>
    <div class="form-group col-md-3 my-2">
        <x-form.input type="text" class="calculation" min="0" name="number_beneficiaries"
            :value="$accreditation->number_beneficiaries" label="عدد المستفيدين" />
    </div>
    <div class="form-group col-md-6">
        <x-form.textarea name="implementation_items" label="بنوذ التنفيد" rows="2"
            :value="$accreditation->implementation_items" />
    </div>
</div>
<hr>
<h5>بنود القبض</h5>
<div class="row">
    <div class="form-group col-md-3 my-2">
        <x-form.input type="date" name="date_implementation" label="تاريخ القبض"
            :value="$accreditation->date_implementation" />
    </div>
    <div class="form-group col-md-3 my-2">
        <x-form.input type="text" class="calculation" min="0" step="0.01"
            name="amount_received" :value="$accreditation->amount_received" label="المبلغ المقبوض" />
    </div>
    <div class="form-group col-md-3 my-2">
        <x-form.input type="number" min="0" name="arrest_receipt_number"
            label="رقم إيصال القبض" :value="$accreditation->arrest_receipt_number" />
    </div>
    <div class="form-group col-md-6">
        <x-form.textarea name="implementation_statement" label="بيان" rows="2"
            :value="$accreditation->implementation_statement" />
    </div>
</div>
<hr>
<div class="row">
    @if (isset($btn_label))
        <div class="form-group col-md-3 my-2">
            <x-form.input name="user_name" label="اسم المستخدم" disabled :value="$accreditation->user_name" />
        </div>
        <div class="form-group col-md-3 my-2 ">
            <x-form.input name="manager_name" label="المدير المستلم" disabled :value="$accreditation->manager_name" />
        </div>
    @endif
    <div class="form-group col-md-12">
        <x-form.textarea name="notes" label="ملاجظات عن التخصيص" :value="$accreditation->notes"
            rows="2" />
    </div>
</div>
<div class="d-flex justify-content-end mt-3" id="btns_form">
    @can('adoption','App\\Models\AccreditationProject')
    <button type="button" class="btn btn-success btn-sm p-2 mx-2" id="adoption">
        <i class="fa-solid fa-check"></i> إعتماد
    </button>
    @endcan
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
    $('#adoption').click(function() {
        let form = new FormData();
        form.append('adoption', 1);
        form.append('type', 'allocation');
        form.append('_token', csrf_token);
        $.ajax({
            url: '{{route('dashboard.accreditations.adoption', $accreditation->id)}}',
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
</script>
@endpush
