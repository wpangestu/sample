<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Promo extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['name','code','description','value','is_active'];

    protected static $logAttributes = ['name','code','description','value','is_active'];

    public function getDescriptionForEvent(string $eventName): string
    {
        if($eventName=='created'){
            $event = "membuat";
        }elseif($eventName=='updated'){
            $event = "mengubah";
        }elseif($eventName=='deleted'){
            $event = "menghapus";
        }
        return "Pengguna telah {$event} Promo #:subject.code[:subject.name]";
    }

}
