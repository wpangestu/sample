<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'amount',
        'withdraw_id',
        'note',
        'account_holder',
        'account_number'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');        
    }
    
    public function verified()
    {
        return $this->belongsTo('App\Models\User','verified_by','id');        
    }
}
