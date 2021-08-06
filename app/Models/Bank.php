<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
class Bank extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'logo',
    ];

    protected static $logAttributes = ['name','created_at'];
    protected static $logName = 'banks';

    public function getDescriptionForEvent(string $eventName): string
    {
        if($eventName=='created'){
            $event = "membuat";
        }elseif($eventName=='updated'){
            $event = "mengubah";
        }elseif($eventName=='deleted'){
            $event = "menghapus";
        }
        return "Pengguna telah {$event} master bank #:subject.id[:subject.name]";
    }    
}
