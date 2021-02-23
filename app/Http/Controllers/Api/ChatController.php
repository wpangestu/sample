<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Chatroom;
use App\Models\Chat;

class ChatController extends Controller
{
    //
    public function send_message(Request $request){

        $validator = Validator::make($request->all(), [
            'to' => 'required|',
            'message' => 'required|',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()], 400);
        }

        $to = $request->get('to');
        $from = auth()->user()->id;
        $message = $request->get('message');
        $new = false;

        try {
            //code...
            $user_1 = $to;
            $user_2 = $from;
            DB::beginTransaction();

            $chatroom = Chatroom::where('user_1',$user_1)->where('user_2',$user_2);
            $chat = [];
            if($chatroom->count() == 0){
                $chatroom = Chatroom::where('user_1',$user_2)->where('user_2',$user_1);
            }

            if($chatroom->count() > 0){
                $chatroom = $chatroom->first();
                $chat = Chat::create([
                    "to" => $to,
                    "from" => $from,
                    "message" => $message,
                    "chatroom_id" => $chatroom->id,
                ]);

                $chatroom->updated_at = date('Y-m-d H:i:s');
                $chatroom->save();

            }else{
                $new = true;
                $chatroom = Chatroom::create([
                    "user_1" => $from,
                    "user_2" => $to
                ]);

                $chat = Chat::create([
                    "to" => $to,
                    "from" => $from,
                    "message" => $message,
                    "chatroom_id" => $chatroom->id,
                ]);
            }
            $to = [];
            $to[] = $chat->user_to->fcm_token;

            fcm()->to($to)
                    ->priority('high')
                    ->timeToLive(0)
                    ->data([
                        'title' => 'Notifikasi',
                        'body' => 'Anda mendapat pesan baru',
                    ])
                    ->send();

            DB::commit();

            $response['success'] = true;
            $response['message'] = "Pesan berhasil dikirim";

            $response['chat']['id'] = $chat->id;
            $response['chat']['name'] = $chat->user_from->name;
            $response['chat']['message'] = $chat->message;
            $response['chat']['created_at'] = $chat->created_at->format('d/m/Y H:i');
            $response['chat']['new'] = $new;

            return response()->json($response);

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json($th->getMessage());
        }
    }

    public function get_message_chat(Request $request)
    {

        $user_id = auth()->user()->id;

        $chatroom = Chatroom::where('user_1',$user_id)->orWhere('user_2',$user_id);

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('size') ? $request->get('size') : 10;
        $chatroom_data = $chatroom->limit($limit)->offset(($page - 1) * $limit);
        $data = $chatroom_data->get();
        $total = $chatroom_data->count();

        $new_chatroom_data = [];
        if($total > 0){
            foreach ($data as $key => $value) {
                # code...
                if($value->user_1 === $user_id){
                    $name = $value->user_2_data->name??"";
                    $pinned = $value->pinned_user_1;
                }else{
                    $name = $value->user_1_data->name??"";
                    $pinned = $value->pinned_user_2;
                }
    
                $chat = Chat::where('chatroom_id',$value->id)->latest()->first();
                $unread_message = Chat::where('chatroom_id',$value->id)
                                ->where('to',$user_id)
                                ->where('read',false)
                                ->count();
                $chat_data = [
                    "message" => $chat->message,
                    "media" => $chat->media,
                    "from" => $chat->from,
                    "is_me" => $user_id==$chat->from?true:false,
                    "created_at" => $chat->created_at
                ];
                $chatroom_t = [
                    "id" => $value->id,
                    "name" => $name,
                    "unread_count" => $unread_message,
                    "avatar" => "",
                    "pinned" => $pinned,
                    "last_message" => $chat_data
                ];
    
                $new_chatroom_data[] = $chatroom_t;
            }
        }

        $response['page'] = $page;
        $response['size'] = $limit;
        $response['total'] = $total;
        $response['data'] = $new_chatroom_data;

        return response()->json($response);

    }
    
    public function get_history_message_chat(Request $request)
    {
        // dd('xe');
        $user_id = auth()->user()->id;

        $chatroom = Chatroom::where('open',0);
        // $chatroom->where('user_1',$user_id)->orWhere('user_2',$user_id);

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('size') ? $request->get('size') : 10;
        $chatroom_data = $chatroom->limit($limit)->offset(($page - 1) * $limit);
        $data = $chatroom_data->get();
        $total = $chatroom_data->count();

        $new_chatroom_data = [];
        if($total > 0){
            foreach ($data as $key => $value) {
                # code...
                if($value->user_1 === $user_id){
                    $name = $value->user_2_data->name??"";
                    $pinned = $value->pinned_user_1;
                }else{
                    $name = $value->user_1_data->name??"";
                    $pinned = $value->pinned_user_2;
                }
    
                $chat = Chat::where('chatroom_id',$value->id)->latest()->first();
                $unread_message = Chat::where('chatroom_id',$value->id)
                                ->where('to',$user_id)
                                ->where('read',false)
                                ->count();
                $chat_data = [
                    "message" => $chat->message,
                    "media" => $chat->media,
                    "from" => $chat->from,
                    "is_me" => $user_id==$chat->from?true:false,
                    "created_at" => $chat->created_at
                ];
                $chatroom_t = [
                    "id" => $value->id,
                    "name" => $name,
                    "unread_count" => $unread_message,
                    "avatar" => "",
                    "pinned" => $pinned,
                    "last_message" => $chat_data
                ];
    
                $new_chatroom_data[] = $chatroom_t;
            }
        }

        $response['page'] = $page;
        $response['size'] = $limit;
        $response['total'] = $total;
        $response['data'] = $new_chatroom_data;

        return response()->json($response);

    }

    public function get_message_by_chatroom_id($id,Request $request)
    {
        $user_id = auth()->user()->id;

        $chatroom_id = $id;

        $chatroom = Chatroom::find($id);

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('size') ? $request->get('size') : 10;
        
        if(!empty($chatroom)){
            // dd('in');
            $chat = Chat::where('chatroom_id',$chatroom->id);
            $chat_data = $chat->limit($limit)->offset(($page - 1) * $limit)->latest();

            $data = $chat_data->get();
            $total = $chat_data->count();

            $new_data = [];
            foreach ($data as $key => $value) {
                # code...
                $t_data = [
                    "id" => $value->id,
                    "message" => $value->message,
                    "media" => "",
                    "from" => $value->from,
                    "is_me" => $value->from==$user_id?true:false,
                    "created_at" => $value->created_at
                ];
                $new_data[] = $t_data;
            }

            $response['page'] = $page;
            $response['size'] = $limit;
            $response['total'] = $total;
            $response['data'] = $new_data;
    
            return response()->json($response);
                
        }
    }

    public function get_history_message_by_chatroom_id($id,Request $request)
    {
        $user_id = auth()->user()->id;

        $chatroom_id = $id;

        $chatroom = Chatroom::find($id);

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('size') ? $request->get('size') : 10;
        
        if(!empty($chatroom)){
            // dd('in');
            $chat = Chat::where('chatroom_id',$chatroom->id);
            $chat_data = $chat->limit($limit)->offset(($page - 1) * $limit)->latest();

            $data = $chat_data->get();
            $total = $chat_data->count();

            $new_data = [];
            foreach ($data as $key => $value) {
                # code...
                $t_data = [
                    "id" => $value->id,
                    "message" => $value->message,
                    "media" => "",
                    "from" => $value->from,
                    "is_me" => $value->from==$user_id?true:false,
                    "created_at" => $value->created_at
                ];
                $new_data[] = $t_data;
            }

            $response['page'] = $page;
            $response['size'] = $limit;
            $response['total'] = $total;
            $response['data'] = $new_data;
    
            return response()->json($response);
                
        }
    }

    public function delete_chat(Request $request){
        $validator = Validator::make($request->all(), [
            'chatroom_id' => 'required|',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()], 400);
        }

        $chatroom_id = $request->get('chatroom_id');
        // dd($chatroom_id);
        try {
            //code...
            DB::beginTransaction();

            foreach ($chatroom_id as $key => $value) {
                # code...
                $chat = Chat::where('chatroom_id',$value)->delete();

                $chatroom = Chatroom::find($value)->delete();
            }

            DB::commit();

            return response()->json(["message" => "Chat berhasil dihapus"]);            

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(["message" => "Terjadi kesalahan : ".$th->getMessage()]);
        }
    }

    public function delete_history_chat(Request $request){
        $validator = Validator::make($request->all(), [
            'chatroom_id' => 'required|',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()], 400);
        }

        $chatroom_id = $request->get('chatroom_id');
        // dd($chatroom_id);
        try {
            //code...
            DB::beginTransaction();

            foreach ($chatroom_id as $key => $value) {
                # code...
                $chat = Chat::where('chatroom_id',$value)->delete();

                $chatroom = Chatroom::find($value)->delete();
            }

            DB::commit();

            return response()->json(["message" => "Chat berhasil dihapus"]);            

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(["message" => "Terjadi kesalahan : ".$th->getMessage()]);
        }
    }

    public function pinned_chat(Request $request){
        $validator = Validator::make($request->all(), [
            'chatroom_id' => 'required|',
        ]);

        
        if($validator->fails()){
            return response()->json(["message" => $validator->errors()], 400);
        }
        
        $user_id = auth()->user()->id;
        $chatroom_id = $request->get('chatroom_id');
        
        try {
            //code...
            foreach ($chatroom_id as $key => $value) {
                # code...
                $chatroom = Chatroom::find($value);
                if($chatroom->user_1 == $user_id){
                    $chatroom->pinned_user_1 = true;
                    $chatroom->save();
                }else{
                    $chatroom->pinned_user_2 = true;
                    $chatroom->save();
                }
            }

            return response()->json(["message" => "Chat berhasil dipin"]);                        

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan : ".$th->getMessage()]);                        
        }

    }

    public function pinned_history_chat(Request $request){
        $validator = Validator::make($request->all(), [
            'chatroom_id' => 'required|',
        ]);

        
        if($validator->fails()){
            return response()->json(["message" => $validator->errors()], 400);
        }
        
        $user_id = auth()->user()->id;
        $chatroom_id = $request->get('chatroom_id');
        
        try {
            //code...
            foreach ($chatroom_id as $key => $value) {
                # code...
                $chatroom = Chatroom::find($value);
                if($chatroom->user_1 == $user_id){
                    $chatroom->pinned_user_1 = true;
                    $chatroom->save();
                }else{
                    $chatroom->pinned_user_2 = true;
                    $chatroom->save();
                }
            }

            return response()->json(["message" => "Chat berhasil dipin"]);                        

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan : ".$th->getMessage()]);                        
        }

    }
}
 