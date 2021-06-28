<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankPayment extends Model
{
    use HasFactory;

    public function bank(){
        return $this->belongsTo('App\Models\Bank','bank_id','id');        
    }
}
