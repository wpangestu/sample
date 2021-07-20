<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Service extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $casts = [
        'skill' => 'array'
    ];

    protected $fillable = [
        'engineer_id',
        'skill',
        'sertification_image',
        'base_service_id'
    ];

    protected static $logAttributes = [
        'engineer.userid',
        'engineer.name',
        'sertification_image',
        'base_service_id',
        'status',
        'base_service.name'
    ];

    protected static $logName = 'service';

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

    public function getDescriptionForEvent(string $eventName): string
    {
        if($eventName=='created'){
            $event = "membuat";
        }elseif($eventName=='updated'){
            $event = "mengubah";
        }elseif($eventName=='deleted'){
            $event = "menghapus";
        }
        return "Pengguna telah {$event} jasa teknisi #:subject.id[:subject.engineer_id]";
    }
}
