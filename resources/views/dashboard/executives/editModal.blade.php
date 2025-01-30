<div class="container-fluid">
    <h3>بيانات التنفيذ</h3>
    <div class="row">
        {{-- <div class="form-group col-md-3">
            <x-form.input type="number" name="budget_number" label="رقم الموازنة" wire:model="budget_number" placeholder="رقم الموزانة : 1212" class="text-center" required wire:input="budget_number_check($event.target.value)" />
            <div id="budget_number_error" class="text-danger" >
                @if ($budget_number_error != '')
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span title="يمكنك جعل الرقم لتخصيص آخر هذا فقط تحذير">{{ $budget_number_error  }}</span>
                @endif
            </div>
        </div> --}}
        <div class="form-group col-md-3">
            <x-form.input type="date" name="implementation_date" label="التاريخ" required />
        </div>
        <div class="form-group col-md-3">
            <label for="broker_name">الإسم المختصر</label>
            <x-form.input name="broker_name" list="brokers_list" required />
            <datalist id="brokers_list">
                @foreach ($brokers as $broker)
                    <option value="{{ $broker }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3">
            <x-form.input name="account" label="الحساب" list="account_list" required />
            <datalist id="account_list">
                @foreach ($accounts as $account)
                    <option value="{{ $account }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3">
            <x-form.input name="affiliate_name" label="الاسم" list="affiliate_name_list" required />
            <datalist id="affiliate_name_list">
                @foreach ($affiliates as $affiliate_name)
                    <option value="{{ $affiliate_name }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3">
            <label for="project_name">المشروع</label>
            <x-form.input name="project_name" list="projects_list" required />
            <datalist id="projects_list">
                @foreach ($projects as $project)
                    <option value="{{ $project }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-3">
            <x-form.input name="detail" label="التفصيل.." list="detail_list" />
            <datalist id="detail_list">
                @foreach ($details as $detail)
                    <option value="{{ $detail }}">
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
            <x-form.input type="text" class="calculation"  name="quantity" label="الكمية"/>
        </div>
        <div class="form-group col-md-3">
            <x-form.input type="text" class="calculation"  min="0" step="0.01" name="price" label="سعر الوحدة ₪" />
        </div>
        <div class="form-group col-md-3">
            <x-form.input type="text" class="calculation"  min="0" step="0.01" name="total_ils" label="الإجمالي ب ₪" />
        </div>
        <div class="form-group col-md-3">
            <x-form.input name="received" label="المستلم" list="received_list" />
            <datalist id="received_list">
                @foreach ($receiveds as $received)
                    <option value="{{ $received }}">
                @endforeach
            </datalist>
        </div>
        <div class="form-group col-md-12">
            <x-form.textarea name="notes" label="ملاجظات" rows="2" />
        </div>
    </div>
    <hr>
    <h3>بنود الدفع</h3>
    <div class="row">
        <div class="form-group col-md-3">
            <x-form.input type="text" class="calculation" min="0" step="0.01" name="amount_payments" label="الدفعات" />
        </div>
        <div class="form-group col-md-6">
            <x-form.textarea name="payment_mechanism" label="آلية الدفع" rows="2"  />
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="form-group col-md-3 editForm">
            <x-form.input name="user_id" label="اسم المستخدم" disabled />
        </div>
        <div class="form-group col-md-3 editForm">
            <x-form.input name="manager_name" label="المدير المستلم" disabled />
        </div>
    </div>
    <div class="d-flex justify-content-end"  id="btns_form">
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
        <x-form.input type="file" name="filesArray[]" label="رفع ملفات للتنفيذ" multiple />
    </div> --}}
</div>
