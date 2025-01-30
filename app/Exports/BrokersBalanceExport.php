<?php

namespace App\Exports;

use App\Models\Allocation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BrokersBalanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $allocations;
    protected $allocationsTotal;
    protected $brokers;
    private $rowNumber = 1; // متغير لبدء الترقيم من 1



    public function __construct($allocations,$allocationsTotalArray,$brokers)
    {
        $this->allocations = $allocations;
        $this->allocationsTotal = $allocationsTotalArray;
        $this->brokers = $brokers;
    }

    // استرجاع البيانات
    public function collection()
    {
        return Allocation::whereIn('broker_name', $this->brokers)->distinct()->get('broker_name');
    }


    // إضافة رؤوس الأعمدة
    public function headings(): array
    {
        return [
            'الرقم',
            'المؤسسة',
            'نسبة التمويل',
            'مبلغ التخصيص',
            'القبض بالدولار',
            'الرصيد بالدولار',
            'نسبة التحصيل',
        ];
    }

    // تخصيص البيانات لكل صف
    public function map($allocation): array
    {
        $amount = Allocation::where('broker_name', $allocation->broker_name)->sum('amount');
        $amount_received = Allocation::where('broker_name', $allocation->broker_name)->sum('amount_received');

        // لحل مشكلة القسمة
        $amount = ($amount == 0 ? 1 : $amount);
        $amount_received = ($amount_received == 0 ? 1 : $amount_received);
        $totalAmount = ($this->allocationsTotal['amount'] == 0 ? 1 : $this->allocationsTotal['amount']);

        return [
            $this->rowNumber++, // رقم تسلسلي يبدأ من 1 ويزيد تلقائيًا
            $allocation->broker_name,
            number_format($amount / $totalAmount, 5) * 100 . ' %',
            number_format($amount, 2),
            number_format($amount_received, 2),
            number_format($amount - $amount_received, 2),
            number_format($amount_received / $amount, 5) * 100 . ' %',
        ];
    }

}
