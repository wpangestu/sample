<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    //
    public function index(Request $request)
    {
        $user_id = auth()->user()->id;
        $data = Notification::where('user_id',$user_id)->latest();

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('size') ? $request->get('size') : 10;
        $notif = $data->limit($limit)->offset(($page - 1) * $limit);
        $data = $notif->get();
        $total = $notif->count();
        $data_new = [];
        foreach($data as $d => $val){

            if(!(is_null($val->service_id))){
                $response_data = [
                    "id" => $val->id,
                    "read" => $val->read===0?false:true,
                    "title" => $val->title,
                    "type" => $val->type,
                    "service_status" => $val->service->status,
                    "date" => $val->created_at,
                ];
            }
            $data_new[] = $response_data;
        }

        $response['page'] = (int)$page;
        $response['size'] = (int)$limit;
        $response['total'] = (int)$total;
        $response['data'] = $data_new;

        return response()->json($response);           
    }
}
