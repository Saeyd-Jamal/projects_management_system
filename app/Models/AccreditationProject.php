<?php

namespace App\Models;

use App\Observers\AccreditationProjectObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccreditationProject extends Model
{
    use HasFactory;

    protected $table = 'accreditation_projects';
    protected $guarded = ['id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
