<?php

namespace App\Imports;

use App\Models\Executive;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExecutivesImport implements ToModel,WithHeadingRow
{
    public function formatDate($date){
        if(is_numeric($date)){
            return Carbon::createFromFormat('Y-m-d', Date::excelToDateTimeObject($date)->format('Y-m-d'));
        }else{
            return $date;
        }
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $implementation_date= $this->formatDate($row['altarykh']);
        $month = Carbon::parse($row['altarykh'])->format('Y-m');
        return new Executive([
            'implementation_date' => $implementation_date,
            'month' => $month,
            'broker_name' => $row['almoss'],
            'account' => $row['alhsab'],
            'affiliate_name' => $row['alasm'],
            'project_name' => $row['almshroaa'],
            'detail' => $row['altfsyl'],
            'item_name' => $row['alsnf'],
            'quantity' => $row['alkmy'],
            'price' => $row['alsaar'],
            'total_ils' => $row['alagmaly_sh'],
            'received' => $row['almstlm'],
            'notes' => $row['mlahthat'],
            'amount_payments' => $row['aldfaaat'],
            'payment_mechanism' => $row['aly_aldfaa'],
        ]);
    }

    public function chunkSize(): int
    {
        return 100; // حجم القطعة الواحدة
    }
}
