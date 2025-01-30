<?php

namespace App\Exports;

use App\Models\Executive;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TradersReveExport implements FromCollection, WithHeadings, WithMapping
{
    protected $executives;
    protected $executivesTotal;
    protected $accounts;
    private $rowNumber = 1; // متغير لبدء الترقيم من 1


    public function __construct($executives,$executivesTotalArray,$accounts)
    {
        $this->executives = $executives;
        $this->executivesTotal = $executivesTotalArray;
        $this->accounts = $accounts;
    }

    // استرجاع البيانات
    public function collection()
    {
        return Executive::whereIn('account', $this->accounts)->distinct()->get('account');
    }



    // إضافة رؤوس الأعمدة
    public function headings(): array
    {
        return [
            '#',
            'الشركة',
            'المستحق',
            'الدفعات',
            'الرصيد'
        ];
    }

    // تخصيص البيانات لكل صف
    public function map($executive): array
    {
        $executiveS = Executive::where('account', $executive->account)->get();
        $total_ils = $executiveS->sum('total_ils');
        $amount_payments = $executiveS->sum('amount_payments');
        $balance = $total_ils - $amount_payments;

        return [
            $this->rowNumber++, // رقم تسلسلي يبدأ من 1 ويزيد تلقائيًا
            $executive->account,
            number_format($total_ils, 0),
            number_format($amount_payments, 0),
            number_format($balance, 0),
        ];
    }
}
