<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_service_id',
        'price',
        'description',
        'guarantee',
        'long_guarantee',
        'price_receive'
    ];

    public function service_category(){
        return $this->belongsTo('App\Models\CategoryService','category_service_id','id');
    }
}
