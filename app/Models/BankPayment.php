<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class BankPayment extends Model
{
    use HasFactory;
    use LogsActivity;


    public function bank(){
        return $this->belongsTo('App\Models\Bank','bank_id','id');        
    }

    protected static $logAttributes = [
        'bank_id',
        'account_number',
        'is_active'
    ];

    protected static $logName = 'bank_payments';

    protected $fillable = [
        'bank_id',
        'account_number',
        'is_active'
    ];

    public function getDescriptionForEvent(string $eventName): string
    {
        if($eventName=='created'){
            $event = "membuat";
        }elseif($eventName=='updated'){
            $event = "mengubah";
        }elseif($eventName=='deleted'){
            $event = "menghapus";
        }
        return "Pengguna telah {$event} bank pembayaran #:subject.id[:subject.account_number]";
    }    
}
