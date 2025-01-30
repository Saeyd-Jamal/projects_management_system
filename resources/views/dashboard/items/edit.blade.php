<x-front-layout>
    <x-slot:breadcrumb>
        <li><a href="{{ route('dashboard.items.index')}}">الأصناف</a></li>
        <li><a href="#">تعديل الصنف : {{ $item->name }}</a></li>
    </x-slot:breadcrumb>


    <div class="row">
        <form id="UploadfileID" action="{{ route('dashboard.items.update', $item->id) }}" method="post">
            @csrf
            @method('put')
            @include('dashboard.items._form')
        </form>
    </div>
</x-front-layout>
