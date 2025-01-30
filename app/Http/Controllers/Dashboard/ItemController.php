<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\Executive;
use App\Models\Item;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Item::class);
        $items = Item::get();
        return view('dashboard.items.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Item::class);
        $item = new Item();
        $itemsFromAllocation = Allocation::select('item_name')->distinct()->pluck('item_name')->toArray();
        $itemsFromExecutive = Executive::select('item_name')->distinct()->pluck('item_name')->toArray();

        $items = array_unique(array_merge($itemsFromAllocation, $itemsFromExecutive));
        return view('dashboard.items.create', compact('item', 'items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Item::class);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Item::create($request->all());
        return redirect()->route('dashboard.items.index')->with('success', 'تم إضافة صنف جديد');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $this->authorize('update', Item::class);
        $btn_label = 'تعديل';
        $itemsFromAllocation = Allocation::select('item_name')->distinct()->pluck('item_name')->toArray();
        $itemsFromExecutive = Executive::select('item_name')->distinct()->pluck('item_name')->toArray();

        $items = array_unique(array_merge($itemsFromAllocation, $itemsFromExecutive));
        return view('dashboard.items.edit', compact('item', 'btn_label', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $this->authorize('update', Item::class);
        $item->update($request->all());
        return redirect()->route('dashboard.items.index')->with('success', 'تم تحديث بيانات الصنف المحدد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $this->authorize('delete', Item::class);
        $item->delete();
        return redirect()->route('dashboard.items.index')->with('danger', 'تم حذف الصنف المحدد');
    }
}
