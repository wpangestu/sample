<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_number',
        'account_holder',
        'bank_id',
        'user_id'
    ];

    public function bank(){
        return $this->belongsTo('App\Models\Bank','bank_id','id');
    }

}
