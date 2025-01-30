<div class="container-fluid">
    <h3>بيانات التخصيص</h3>
    <div class="row">
        <div class="form-group col-md-3">
            <x-form.input type="number" name="budget_number" label="رقم الموازنة" placeholder="رقم الموزانة : 1212" class="text-center" required />
            <div id="budget_number_error" class="text-danger" >
                {{-- @if ($budget_number_error != '')
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span title="يمكنك جعل الرقم لتخصيص آخر هذا فقط تحذير">{{ $budget_number_error  }}</span>
                @endif --}}
            </div>
        </div>
        <div class="form-group col-md-3">
            <x-form.input type="date" name="date_allocation" label="تاريخ التخصيص" required />
        </div>
        <div class="form-group col-md-3">
            <label for="broker_name">المؤسسة</label>
            <x-form.input name="broker_name" list="brokers_list" required/>
            <datalist id="brokers_list">
                @foreach ($brokers as $broker)
                    <option value="{{ $broker }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3">
            <label for="organization_name">المتبرع</label>
            <x-form.input name="organization_name" list="organizations_list" required/>
            <datalist id="organizations_list">
                @foreach ($organizations as $organization)
                    <option value="{{ $organization }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3">
            <label for="project_name">المشروع</label>
            <x-form.input name="project_name" list="projects_list"  required />
            <datalist id="projects_list">
                @foreach ($projs as $project)
                    <option value="{{ $project }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3">
            <label for="item_name">الصنف</label>
            <x-form.input name="item_name" list="items_list" required />
            <datalist id="items_list">
                @foreach ($items as $item)
                    <option value="{{ $item }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3">
            <x-form.input type="text" class="calculation" min="0" name="quantity" label="الكمية" />
        </div>
        <div class="form-group col-md-3">
            <x-form.input type="text" class="calculation" min="0" step="0.01" name="price" label="سعر الوحدة"   />
        </div>
        <div class="form-group col-md-3">
            <x-form.input type="number" min="0" step="0.01" name="total_dollar" label="الإجمالي"  readonly/>
        </div>
        <div class="form-group col-md-3">
            <x-form.input  type="text" class="calculation" min="0" step="0.01" name="allocation" label="التخصيص" required />
        </div>
        <div class="form-group col-md-3">
            <label for="currency_allocation">العملة</label>
            <select class="form-control text-center" name="currency_allocation" id="currency_allocation">
                <option label="فتح القائمة">
                @foreach ($currencies as $currency)
                    {{-- <option value="{{ $currency->code }}" @selected($currency->code == $allocation->currency_allocation || $currency->code == "USD")>{{ $currency->name }}</option> --}}
                    <option value="{{ $currency->code }}" data-val="{{$currency->value}}">{{ $currency->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-3">
            <x-form.input type="text" class="calculation" min="0" required name="currency_allocation_value" label="سعر الدولار للعملة" />
        </div>
        <div class="form-group col-md-3">
            <x-form.input type="number" min="0" step="0.01" name="amount" label="المبلغ $" readonly />
        </div>
        <div class="form-group col-md-3">
            <x-form.input type="text" class="calculation" min="0" name="number_beneficiaries" label="عدد المستفيدين" />
        </div>
        <div class="form-group col-md-6">
            <x-form.textarea name="implementation_items" label="بنوذ التنفيد" />
        </div>
    </div>
    <hr>
    <h3>بنود القبض</h3>
    <div class="row">
        <div class="form-group col-md-3">
            <x-form.input type="date" name="date_implementation" label="تاريخ القبض"  />
        </div>
        <div class="form-group col-md-3">
            <x-form.input type="text" class="calculation" min="0" step="0.01" name="amount_received" label="المبلغ المقبوض" />
        </div>
        <div class="form-group col-md-3">
            <x-form.input type="number" min="0" name="arrest_receipt_number" label="رقم إيصال القبض" />
        </div>
        <div class="form-group col-md-6">
            <x-form.textarea name="implementation_statement" label="بيان"  />
        </div>

    </div>
    <hr>

    <div class="row">
        <div class="form-group col-md-3 editForm">
            <x-form.input name="user_name" label="اسم المستخدم"  disabled />
        </div>
        <div class="form-group col-md-3 editForm">
            <x-form.input name="manager_name" label="المدير المستلم" disabled />
        </div>
        <div class="form-group col-md-12">
            <x-form.textarea name="notes" label="ملاجظات عن التخصيص" />
        </div>
    </div>
    <div class="d-flex justify-content-end" id="btns_form">
        <button aria-label="" type="button" class="btn btn-danger px-2" data-bs-dismiss="modal" aria-hidden="true">
            <span aria-hidden="true">×</span>
            إغلاق
        </button>
        <button type="button" id="update" class="btn btn-primary mx-2">
            <i class="fa-solid fa-edit"></i>
            تعديل
        </button>
    </div>
    {{-- <div class="form-group col-md-4">
        <x-form.input type="file" name="filesArray[]" label="رفع ملفات للتخصيص" multiple />
    </div> --}}
</div>
