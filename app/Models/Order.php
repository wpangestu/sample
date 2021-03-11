<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 
        'order_type', 
        'order_status', 
        'is_take_away', 
        'customer_id', 
        'engineer_id',
        'shipping',
        'note'
    ]; 

    public function customer(){
        return $this->belongsTo('App\Models\User','customer_id','id');
    }

    public function engineer(){
        return $this->belongsTo('App\Models\User','engineer_id','id');
    }
}
