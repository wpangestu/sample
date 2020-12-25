<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewService extends Model
{
    use HasFactory;
    protected $table = 'reviews';

    public function service_order(){
        return $this->belongsTo('App\Models\ServiceOrder','service_order_id','id');
    }
}
