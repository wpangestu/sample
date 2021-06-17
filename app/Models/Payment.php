<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $casts = [
        'orders' => 'array'
    ];

    protected $fillable = [
        'customer_id', 
        'amount', 
        'paymentid', 
        'status', 
        'image',
        'convenience_fee',
        'type',
        'orders',
        'account_holder',
        'account_number',
        'bank_id',
        'type_payment',
        'data_id'
    ];

    public function customer(){
        return $this->belongsTo('App\Models\User','customer_id','id');
    }
    
    public function bank(){
        return $this->belongsTo('App\Models\Bank','bank_id','id');
    }


}
