<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 
        'name', 
        'qty', 
        'price', 
        'base_id',
        'image'
    ]; 

    public function base_service(){
        return $this->belongsTo('App\Models\BaseService','base_id','id');
    }    
}
