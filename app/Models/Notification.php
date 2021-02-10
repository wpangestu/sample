<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'type', 'user_id', 'read', 'service_id', 'review_id','withdraw_id','order_id']; 

    public function service(){
        return $this->belongsTo('App\Models\Service','service_id','id');
    }
}
