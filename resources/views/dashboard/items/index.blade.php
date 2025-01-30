<x-front-layout>
    <x-slot:breadcrumb>
        <li><a href="#">تخصيص الأصناف</a></li>
    </x-slot:breadcrumb>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="table-responsive">
                    <div class="d-flex justify-content-end p-3">
                        @can('create','App\\Models\Item')
                        <a href="{{route('dashboard.items.create')}}" class="btn btn-primary m-0">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                        @endcan
                    </div>
                    <table class="table align-items-center mb-0 table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class="text-secondary opacity-7 text-center">#</th>
                                <th>الاسم</th>
                                <th>السعر</th>
                                <th>ملاحظات</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                            <tr>
                                <td  class="text-center">{{$loop->iteration}}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->description }}</td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        @can('update','App\\Models\Item')
                                        <a href="{{route('dashboard.items.edit', $item->id)}}" class="text-secondary font-weight-bold text-xs"
                                            data-bs-toggle="tooltip" >
                                            تعديل
                                        </a>
                                        @endcan
                                        @can('delete','App\\Models\Item')
                                        <form action="{{route('dashboard.items.destroy', $item->id)}}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" data-bs-toggle="tooltip" data-original-title="Delete user">
                                                حذف
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</x-front-layout>
