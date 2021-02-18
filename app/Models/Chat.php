<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'to',
        'from',
        'message',
        'chatroom_id',
        'media',
    ];

    public function user_from(){
        return $this->belongsTo('App\Models\User','from','id');
    }
    
    public function user_to(){
        return $this->belongsTo('App\Models\User','to','id');
    }
}
