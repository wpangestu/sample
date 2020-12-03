<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name','service_category_id','image','description'];

    public function service_category(){
        return $this->belongsTo('App\Models\CategoryService','service_category_id','id');
    }
}
