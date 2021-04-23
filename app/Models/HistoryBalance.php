<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryBalance extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','amount','description','created_by'];

    public function admin(){
        return $this->belongsTo('App\Models\User','created_by','id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User','user_id','id');
    }
}
