<?php

namespace App\Exports;

use App\Models\Executive;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DetectionItemsExport implements FromCollection,WithHeadings
{
    protected $year;
    protected $lastYear;
    protected $months;
    protected $monthNameAr;
    protected $items;
    private $rowNumber = 1; // متغير لبدء الترقيم من 1


    public function __construct($year, $lastYear, $months, $monthNameAr, $items)
    {
        $this->year = $year;
        $this->lastYear = $lastYear;
        $this->months = $months;
        $this->monthNameAr = $monthNameAr;
        $this->items = $items;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];

        foreach ($this->items as $item) {
            $executive = Executive::whereBetween('implementation_date', [$this->year . '-01-01', $this->year . '-12-31'])->where('item_name', $item)->get();

            $executiveLastYear = Executive::whereBetween('implementation_date', [$this->lastYear . '-01-01', $this->lastYear . '-12-31'])->where('item_name', $item)->get();

            $quantity = $executive->sum('quantity');
            $total_ils = $executive->sum('total_ils');

            $row = [
                $this->rowNumber++, // رقم تسلسلي يبدأ من 1 ويزيد تلقائيًا
                $item, // الأصناف
                $executiveLastYear->sum('quantity') ?? 0, // السنة الماضية
            ];

            foreach ($this->months as $month) {
                $monthlyQuantity = $executive->filter(function ($value) use ($month) {
                    return Carbon::parse($value->month)->format('Y-m') == $month;
                })->sum('quantity');
                $row[] = $monthlyQuantity ?? 0;
            }

            $row[] = $quantity + $executiveLastYear->sum('quantity'); // إجمالي العدد
            $row[] = $total_ils + $executiveLastYear->sum('total_ils'); // إجمالي المبلغ بالشيكل

            $data[] = $row;
        }

        return collect($data);
    }

    public function headings(): array
    {
        $headings = ['#', 'الأصناف', $this->lastYear];

        foreach ($this->months as $month) {
            $headings[] = $this->monthNameAr[Carbon::parse($month)->format('m')];
        }

        $headings[] = 'إجمالي العدد';
        $headings[] = 'إجمالي المبلغ بالشيكل';

        return $headings;
    }
}
