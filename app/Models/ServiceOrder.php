<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    use HasFactory;
    protected $fillable = ['serviceorder_id','customer_id','engineer_id','service_id','description','status'];

    public function customer(){
        return $this->belongsTo('App\Models\User','customer_id','id');
    }

    public function engineer(){
        return $this->belongsTo('App\Models\User','engineer_id','id');
    }

    public function service(){
        return $this->belongsTo('App\Models\Service','service_id','id');
    }

}
