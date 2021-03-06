<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'type', 'user_id', 'read', 'service_id','id_data','id_data_string','service_status','review_id','withdraw_id','order_id','subtitle','subtitle_color','caption','action']; 

    public function service(){
        return $this->belongsTo('App\Models\Service','service_id','id');
    }
    
    public function order(){
        return $this->belongsTo('App\Models\Order','id_data_string','order_number');
    }
}
