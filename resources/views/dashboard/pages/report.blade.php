<x-front-layout>
    @push('styles')
    <link rel="stylesheet" href="{{asset('css/custom/select2.min.css')}}">
    @endpush
    <x-slot:breadcrumb>
        <li><a href="#">التقارير</a></li>
    </x-slot:breadcrumb>
    <div class="card">
        <div class="card-header pb-0">
            <h2 class="h page-title">إنتاج التقارير</h2>
        </div>
        <div class="card-body">
            <div class="row justify-content-between">
                <form action="{{route('dashboard.reports.export')}}" method="post" class="col-12" target="_blank">
                    @csrf
                    <h4>أساسي</h4>
                    <div class="row">
                        <div class="form-group col-md-3 my-2">
                            <x-form.input type="month" name="month" label="الشهر المطلوب (الشهر الاول)" />
                        </div>
                        <div class="form-group col-md-3 my-2">
                            <x-form.input type="month"  name="to_month" label="الى شهر" />
                        </div>
                        <div class="form-group col-md-3 my-2">
                            <label for="broker">المؤسسة</label>
                            <select name="broker[]" id="broker" class="form-select select2-multi" multiple>
                                @foreach ($brokers as $broker)
                                    <option value="{{ $broker }}">{{ $broker }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3 my-2">
                            <label for="organization">المتبرع (فقط للتخصيص)</label>
                            <select name="organization[]" id="organization" class="form-select select2-multi" multiple>
                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization }}">{{ $organization }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3 my-2">
                            <label for="project">المشروع</label>
                            <select name="project[]" id="project" class="form-select select2-multi" multiple>
                                @foreach ($projects as $project)
                                    <option value="{{ $project }}">{{ $project }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3 my-2">
                            <label for="item">الصنف</label>
                            <select name="item[]" id="item" class="form-select select2-multi" multiple>
                                @foreach ($items as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <h4>التنفيذات</h4>
                    <div class="row">
                        <div class="form-group col-md-3 my-2">
                            <label for="account">الحساب</label>
                            <select name="account[]" id="account" class="form-select select2-multi" multiple>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account }}">{{ $account }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3 my-2">
                            <label for="affiliate">الاسم</label>
                            <select name="affiliate[]" id="affiliate" class="form-select select2-multi" multiple>
                                @foreach ($affiliates as $affiliate)
                                    <option value="{{ $affiliate }}">{{ $affiliate }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3 my-2">
                            <label for="received">المستلم</label>
                            <select name="received[]" id="received" class="form-select select2-multi" multiple>
                                @foreach ($receiveds as $received)
                                    <option value="{{ $received }}">{{ $received }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <h4>إعدادات</h4>
                    <div class="row">
                        {{-- إضافات --}}
                        <div class="form-group col-md-3 my-2">
                            <label for="report_type">نوع الكشف</label>
                            <select class="form-select " name="report_type" id="report_type" required>
                                <option  value="" disabled selected>حدد نوع الكشف</option>
                                <optgroup label="الكشوفات الأساسية">
                                    <option value="allocations">التخصيصات</option>
                                    <option value="executives">التنفيذات</option>
                                    <option value="brokers_balance">ارصدة المؤسسات الداعمة</option>
                                    <option value="traders_reve">التجار</option>
                                    <option value="detection_items_month">تقرير كميات أصناف المشاريع المنفذة</option>
                                    <option value="total">الإجمالي</option>
                                    <option value="areas">المناطق</option>
                                    <option value="item_balances">أرصدة الأصناف</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="form-group col-md-3 my-2">
                            <label for="export_type">طريقة التصدير</label>
                            <select class="form-select " name="export_type" id="export_type">
                                <option selected="" value="view">معاينة</option>
                                <option value="export_pdf">PDF</option>
                                <option value="export_excel">Excel</option>
                            </select>
                        </div>
                    </div>
                    <div class="row align-items-center mb-2">
                        <div class="col">
                            <h2 class="h5 page-title"></h2>
                        </div>
                        <div class="col-auto">
                            <button type="submit"  class="btn btn-primary">
                                تصدير
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        const csrf_token = "{{csrf_token()}}";
        const app_link = "{{config('app.url')}}";
    </script>
    {{-- <script src="{{asset('js/report.js')}}"></script> --}}
    <script src='{{asset('js/plugins/select2.min.js')}}'></script>
    <script>
        $('.select2-multi').select2(
        {
            multiple: true,
        });
    </script>
    @endpush

</x-front-layout>
