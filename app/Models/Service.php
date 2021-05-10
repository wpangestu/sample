<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $casts = [
        'skill' => 'array'
    ];

    protected $fillable = ['name','category_service_id','image','description','price','engineer_id','skill','sertification_image','image','base_service_id'];

    public function service_category(){
        return $this->belongsTo('App\Models\CategoryService','category_service_id','id');
    }

    public function base_service(){
        return $this->belongsTo('App\Models\BaseService','base_service_id','id');
    }
    
    public function engineer(){
        return $this->belongsTo('App\Models\User','engineer_id','id');
    }

    public function admin(){
        return $this->belongsTo('App\Models\User','verified_by','id');
    }
}
