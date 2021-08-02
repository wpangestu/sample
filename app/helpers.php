<?php

use App\Models\Chat;
use App\Models\User;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Withdraw;
use Illuminate\Database\Eloquent\Builder;

function get_all_notification(){

    return get_confirm_engineer()+get_confirm_service();
}

function get_confirm_engineer()
{
    $confirm_engineer = User::with(['engineer' => function($query){
                                    $query->where('status','pending');
                                }])
                                ->Role('teknisi')
                                ->where('verified',0)
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
                        ->with(['user_from' => function ($query){
                            $query->where('verified',true);
                            $query->Role('teknisi');
                        }])
                        ->count();
    return $unread_message;
}

function get_new_chat_customer(){
    $unread_message = Chat::where('to',1)
                            ->where('read',false)
                            ->with(['user_from'=>function($query){
                                $query->Role('user');
                                $query->where('is_active',true);
                            }])
                            ->count();
    return $unread_message;
}

function get_payment_check()
{
    $payment_check = Payment::where('status','check')->count();
    return $payment_check;    
}

function get_withdraw_technician_check()
{
    $data = Withdraw::where('status','pending')
                    ->with(['user'=>function($query){
                        $query->Role('teknisi')->where('verified',true);
                    }])
                    ->count();
    return $data;    
}

function get_withdraw_customer_check()
{
    $data = Withdraw::where('status','pending')
                    ->with(['user'=>function($query){
                        $query->Role('user');
                    }])
                    ->count();
    return $data;    
}

function get_all_notif_chat()
{
    return get_new_chat_customer() + get_new_chat_engineer();
}

function rupiah($val)
{
    return "Rp ".number_format($val,0,',','.');
}

