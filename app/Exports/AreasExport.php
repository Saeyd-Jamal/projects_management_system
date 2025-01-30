<?php

namespace App\Exports;

use App\Models\Executive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AreasExport implements FromCollection, WithHeadings
{
    protected $areas;
    protected $items;

    public function __construct($areas, $items)
    {
        $this->areas = $areas;
        $this->items = $items;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];
        foreach ($this->areas as $area) {
            $row = [$area];  // بدء الصف باسم المنطقة

            foreach ($this->items as $item) {
                // حساب الكمية لكل عنصر في كل منطقة
                $quantity = Executive::where('received', $area)
                    ->where('item_name', $item)
                    ->sum('quantity');

                $row[] = $quantity;  // إضافة الكمية إلى الصف
            }

            $data[] = $row;
        }

        return collect($data);
    }

    public function headings(): array
    {
        $headings = ['#', 'المنطقة'];  // البداية برقم المنطقة واسمها
        foreach ($this->items as $item) {
            $headings[] = $item;  // إضافة أسماء العناصر كعناوين
        }
        return $headings;
    }
}
