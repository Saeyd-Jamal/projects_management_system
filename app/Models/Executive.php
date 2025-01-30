<?php

namespace App\Models;

use App\Observers\ExecutiveObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Executive extends Model
{
    use HasFactory;

    protected $fillable = [
        'implementation_date',
        'month',
        'broker_name',
        'account',
        'affiliate_name',
        'project_name',
        'detail',
        'item_name',
        'quantity',
        'price',
        'total_ils',
        'received',
        'notes',
        'amount_payments',
        'payment_mechanism',
        'user_id',
        'user_name',
        'manager_name',
        'files',
    ];

    // relationsheps
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

}
