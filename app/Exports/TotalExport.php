<?php

namespace App\Exports;

use App\Models\Allocation;
use App\Models\Executive;
use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TotalExport implements FromCollection,WithHeadings
{
    protected $items;
    private $rowNumber = 1; // متغير لبدء الترقيم من 1


    public function __construct($items)
    {
        $this->items = $items;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];
        $total_allocations = 0;

        foreach ($this->items as $item) {
            $allocation = Allocation::where('item_name', $item)->get();
            $executive = Executive::where('item_name', $item)->get();

            $quantityAllocation = $allocation->sum('quantity');
            $quantityExecutive = $executive->sum('quantity');
            $total_ils = $executive->sum('total_ils');

            $item_price = Item::where('name', $item)->first();
            if($item_price != null){
                $item_price = $item_price->price;
            }else {
                $item_price = $total_ils / $quantityExecutive;
            }

            $total_allocation = $quantityAllocation * $item_price;
            $total_allocations += $total_allocation;

            $data[] = [
                $this->rowNumber++, // رقم تسلسلي يبدأ من 1 ويزيد تلقائيًا
                $item,
                $quantityAllocation,
                $quantityExecutive,
                $quantityAllocation - $quantityExecutive,
                $item_price,
                $total_allocation,
                $total_ils,
            ];
        }

        return collect($data);
    }
    public function headings(): array
    {
        return [
            '#',
            'الصنف',
            'المخصص',
            'المنفذ',
            'المتبقي للتنفيذ',
            'سعر ش',
            'مبلغ التخصيص',
            'المنفذ بالشيكل',
        ];
    }
}
