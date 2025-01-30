<div class="row">
    <div class="form-group col-md-6">
        <x-form.input type="text" name="name" label="اسم الوسيط" placeholder="إملأ الاسم" autofocus required />
    </div>
    <div class="form-group col-md-6">
        <x-form.textarea name="notes" rows="2" label="الملاحظات" placeholder="ملاجظات عن الوسيط" />
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