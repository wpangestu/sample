<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewService extends Model
{
    use HasFactory;

    protected $casts = [
        'liked' => 'array'
    ];

    protected $fillable = ['order_id','ratings','description','liked'];

    protected $table = 'reviews';

    public function service_order(){
        return $this->belongsTo('App\Models\ServiceOrder','service_order_id','id');
    }

    public function order(){
        return $this->belongsTo('App\Models\Order','order_id','id');
    }
}
