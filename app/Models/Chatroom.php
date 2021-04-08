<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chatroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_1',
        'user_2',
    ];

    public function user_1_data(){
        return $this->belongsTo('App\Models\User','user_1','id');
    }    

    public function user_2_data(){
        return $this->belongsTo('App\Models\User','user_2','id');
    }

    public function chat(){
        return $this->hasMany('App\Models\Chat','chatroom_id','id');
    }
}
