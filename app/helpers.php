<?php

use App\Models\Service;
use App\Models\User;
use App\Models\Chat;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;

function get_all_notification(){

    return get_confirm_engineer()+get_confirm_service();
}

function get_confirm_engineer()
{
    $confirm_engineer = User::latest()
                                ->Role('teknisi')
                                ->where('verified',0)
                                ->whereHas('engineer', function (Builder $query) {
                                        $query->where('status','pending');
                                })
                                ->count();

    return $confirm_engineer;
}

function get_confirm_service()
{
    $service = Service::where('status','review')->count();
    return $service;
}

function get_new_chat_engineer()
{
    $unread_message = Chat::where('to',1)
                        ->where('read',false)
                        ->whereHas('user_from', function (Builder $query) {
                            $query->where('verified',true);
                            $query->Role('teknisi');
                        })
                        ->count();
    return $unread_message;

}

function get_payment_check()
{
    $payment_check = Payment::where('status','check')->count();
    return $payment_check;    
}

function get_new_chat_customer(){
    $unread_message = Chat::where('to',1)
                            ->where('read',false)
                            ->whereHas('user_from', function (Builder $query) {
                                $query->Role('user');
                                $query->where('is_active',true);
                            })
                            ->count();
    return $unread_message;
}

function get_all_notif_chat()
{
    return get_new_chat_customer() + get_new_chat_engineer();
}

function rupiah($val)
{
    return "Rp ".number_format($val,0,',','.');
}

