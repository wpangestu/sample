<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CategoryService extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['name','icon','status','slug'];
    protected static $logAttributes = ['name','icon','status','slug'];
    protected static $logName = 'category_service';

    public function getDescriptionForEvent(string $eventName): string
    {
        if($eventName=='created'){
            $event = "membuat";
        }elseif($eventName=='updated'){
            $event = "mengubah";
        }elseif($eventName=='deleted'){
            $event = "menghapus";
        }
        return "Pengguna telah {$event} kategori jasa #:subject.id[:subject.name]";
    }
}
