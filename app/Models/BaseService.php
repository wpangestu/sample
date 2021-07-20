<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseService extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'category_service_id',
        'price',
        'description',
        'guarantee',
        'long_guarantee',
        'price_receive',
    ];

    protected static $logAttributes = [
        'name',
        'category_service_id',
        'price',
        'description',
        'guarantee',
        'long_guarantee',
        'price_receive',
    ];
    protected static $logName = 'base_service';

    public function service_category(){
        return $this->belongsTo('App\Models\CategoryService','category_service_id','id');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        if($eventName=='created'){
            $event = "membuat";
        }elseif($eventName=='updated'){
            $event = "mengubah";
        }elseif($eventName=='deleted'){
            $event = "menghapus";
        }
        return "Pengguna telah {$event} master jasa #:subject.id[:subject.name]";
    }
}
