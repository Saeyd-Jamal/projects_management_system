@push('styles')
<link href="{{asset('css/custom/select2.min.css')}}" rel="stylesheet" />
@endpush
<div class="row">
    <div class="form-group col-md-6">
        <label for="item_name">اسم الصنف</label>
        <select class="form-control js-example-basic-single" name="name" id="name" @if (isset($btn_label))  disabled @endif>
            @foreach ($items as $value )
                <option value="{{$value}}" @if($item->name == $value) selected @endif>{{$value}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-6">
        <x-form.input type="number" step="0.01" min="0" name="price" label="سعر الوحدة شيكل" :value="$item->price" placeholder="إملأ سعر الوحدة" />
    </div>
    <div class="form-group col-md-12">
        <x-form.textarea name="notes" label="الملاحظات" placeholder="ملاجظات عن الصنف" :value="$item->description" />
    </div>
</div>
<div class="d-flex justify-content-end">
    <button type="submit" class="btn btn-primary m-0">
        @if (isset($btn_label))
            <i class="fa-solid fa-pen-to-square"></i>
            {{$btn_label}}
        @else
            <i class="fa-solid fa-plus"></i>
            اضافة
        @endif
    </button>
</div>
@push('scripts')
<script src="{{asset('js/plugins/select2.min.js')}}"></script>
<script>
    // In your Javascript (external .js resource or <script> tag)
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>
@endpush
