<?php

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

function get_all_notification(){

    // $confirm_engineer = User::latest()
    //     ->Role('teknisi')
    //     ->where('verified',0)
    //     ->whereHas('engineer', function (Builder $query) {
    //             $query->where('status','pending');
    //     })
    //     ->count();

    // return $confirm_engineer;    

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