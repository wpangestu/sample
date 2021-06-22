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

    protected $fillable = ['order_number_id','ratings','description','liked'];

    protected $table = 'reviews';

    public function order(){
        return $this->belongsTo('App\Models\Order','order_number_id','order_number');
    }
}
