<?php

namespace App\Http\Controllers\Api;

use App\Models\Chat;
use App\Models\Chatroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    //
    public function send_message(Request $request){

        $validator = Validator::make($request->all(), [
            'to' => 'required|',
            'message' => 'required|',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        $to = 1;
        $from = auth()->user()->id;

        if(auth()->user()->hasRole('teknisi')){
            $role = 'teknisi';
        }else{
            $role = 'user';
        }

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

            $chat_data = [
                "id" => $chat->id,
                "name" => $chat->user_from->name,
                "message" => $chat->message,
                "created_at" => $chat->created_at->format('d/m/Y H:i')
            ];

            fcm()->to($to)
                    ->priority('high')
                    ->timeToLive(0)
                    ->data([
                        'userid' => auth()->user()->userid,
                        'chat' => $chat_data,
                        'role' => $role
                    ])
                    ->notification([
                        'title' => 'Notifikasi',
                        'body' => 'Pesan Baru',
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
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function send_new_chat(Request $request,$chatroom_id)
    {
        try {
            //code...

            $chatroom = Chatroom::find($chatroom_id);
            if(!is_null($chatroom)){

                $user = auth()->user();
                if($chatroom->user_1 == $user->id){
                    $from = $chatroom->user_1;
                    $to = $chatroom->user_2;
    
                }else{
                    $from = $chatroom->user_2;
                    $to = $chatroom->user_1;
                }
    
                $message = $request->get('message');
                $signature = $request->get('signature');
    
                $media = null;
                if($request->hasFile('media')){
    
                    $uploadFolder = 'chat/'.$from;
                    $photo = $request->file('media');
                    $photo_path = $photo->store($uploadFolder,'public');
    
                    $media = Storage::disk('public')->url($photo_path);
                }
    
                $chat = Chat::create([
                    "to" => $to,
                    "from" => $from,
                    "message" => $message,
                    "chatroom_id" => $chatroom->id,
                    "media" => $media
                ]);

                $response = [
                    "id" => $chat->id,
                    "message" => $chat->message,
                    "signature" => $signature,
                    "media" => $chat->media,
                    "from" => (int)$chat->from,
                    "is_me" => true,
                    "created_at" => $chat->created_at
                ];

                $fcm_token[] = $chat->user_to->fcm_token;

                fcm()->to($fcm_token)
                    ->priority('high')
                    ->timeToLive(0)
                    ->notification([
                        'title' => 'Notifikasi',
                        'body' => 'Chat Baru',
                    ])
                    ->data([
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'main_click_action' => 'OPEN_CHAT_DETAIL',
                        'action_data' => [
                            "task" => "ADD_CHAT_MESSAGE",
                            "chatroom_id" => (int)$chatroom_id,
                            "avatar" => $chat->user_from->profile_photo_path??'',
                            "name" => $chat->user_from->name,
                            "data" => [
                                "room_id" => (int)$chatroom_id,
                                "id" => (int)$chat->id,
                                "message" => $chat->message,
                                "from" => (int)$chat->from,
                                "is_me" => $chat->from==$chat->to?true:false,
                                "created_at" => $chat->created_at
                            ]
                        ]
                    ])
                    ->send();

                return response()->json($response);
            }else{
                return response()->json(["message"=>"chatroom_id tidak ditemukan didatabase"],422);
            }

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function get_message_chat(Request $request)
    {
        try {
            //code...
            $user_id = auth()->user()->id;

            $chatroom = Chatroom::where(function($query) use($user_id) {
                                        $query->where('user_1', $user_id)
                                              ->Where('user_2','<>', 1);
                                    })
                                    ->orWhere(function($query) use($user_id) {
                                        $query->where('user_2', $user_id)
                                              ->Where('user_1','<>', 1);
                                    });
    
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
                        "from" => (int)$chat->from,
                        "is_me" => $user_id==$chat->from?true:false,
                        "created_at" => $chat->created_at
                    ];
                    $chatroom_t = [
                        "id" => $value->id,
                        "name" => $name,
                        "unread_count" => (int)$unread_message,
                        "avatar" => "",
                        "pinned" => (boolean)$pinned,
                        "last_message" => $chat_data
                    ];
        
                    $new_chatroom_data[] = $chatroom_t;
                }
            }
    
            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $new_chatroom_data;
    
            return response()->json($response);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }

    }
    
    public function get_history_message_chat(Request $request)
    {
        // dd('xe');
        try {
            
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
                        "pinned" => (boolean)$pinned,
                        "last_message" => $chat_data
                    ];
        
                    $new_chatroom_data[] = $chatroom_t;
                }
            }
    
            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $new_chatroom_data;
    
            return response()->json($response);
            
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }


    }

    public function get_message_by_chatroom_id($id,Request $request)
    {
        
        try {
            //code...
            $user_id = auth()->user()->id;
    
            $chatroom = Chatroom::find($id);
            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            
            $new_data = [];
            if(!empty($chatroom)){
                // dd('in');
                $chat = Chat::where('chatroom_id',$chatroom->id);
                $chat_data = $chat->limit($limit)->offset(($page - 1) * $limit)->latest();
                
                $data = $chat_data->get();
                $total = $chat_data->count();
                
    
                foreach ($data as $key => $value) {
                    # code...
                    $t_data = [
                        "id" => $value->id,
                        "message" => $value->message,
                        "media" => $value->media??"",
                        "from" => $value->from,
                        "is_me" => $value->from==$user_id?true:false,
                        "created_at" => $value->created_at
                    ];
                    $new_data[] = $t_data;
                }    
            }
            
            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $new_data;
    
            return response()->json($response);
                
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }

    }

    public function get_history_message_by_chatroom_id($id,Request $request)
    {
        try {
            //code...

            $user_id = auth()->user()->id;

            $chatroom_id = $id;

            $chatroom = Chatroom::find($id);

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            
            $new_data = [];
            if(!empty($chatroom)){
                // dd('in');
                $chat = Chat::where('chatroom_id',$chatroom->id);
                $chat_data = $chat->limit($limit)->offset(($page - 1) * $limit)->latest();

                $data = $chat_data->get();
                $total = $chat_data->count();

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
            }
            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $new_data;
    
            return response()->json($response);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
        
    }

    public function delete_chat(Request $request){
        $validator = Validator::make($request->all(), [
            'chatroom_id' => 'required|',
        ]);


        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
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
            return response()->json(["message" => "Terjadi kesalahan : ".$th->getMessage()],422);
        }
    }

    public function delete_history_chat(Request $request){
        $validator = Validator::make($request->all(), [
            'chatroom_id' => 'required|',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
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
            return response()->json(["message" => "Terjadi kesalahan : ".$th->getMessage()],422);
        }
    }

    public function pinned_chat(Request $request){
        $validator = Validator::make($request->all(), [
            'chatroom_id' => 'required|',
        ]);

        
        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 400);
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
            return response()->json(["message" => "Terjadi kesalahan : ".$th->getMessage()],422);                        
        }

    }

    public function pinned_history_chat(Request $request){
        $validator = Validator::make($request->all(), [
            'chatroom_id' => 'required|',
        ]);

        
        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all[0]], 422);
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
            return response()->json(["message" => "Terjadi kesalahan : ".$th->getMessage(),422]);
        }

    }

    public function get_support_chat(Request $request)
    {
        try {

            $user_id = auth()->user()->id;

            $chat_arr = [];
            $total = 0;
            $chatroom = Chatroom::where('user_1',$user_id)->where('user_2',1);
            if($chatroom->count() == 0){
                $chatroom = Chatroom::where('user_1',1)->where('user_2',$user_id);
            }

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            $chatroom = $chatroom->first();
            if(!is_null($chatroom)){

                $chat = Chat::where('chatroom_id',$chatroom->id)->latest();
                $total = $chat->count();
                $chat = $chat->limit($limit)->offset(($page - 1) * $limit);

                if($chat->count()>0){
                    foreach ($chat->get() as $key => $value) {
                        # code...
                        $chat_arr[] = [
                            "id" => $value->id,
                            "message" => $value->message,
                            "media" => $value->media,
                            "from" => $value->from,
                            "is_me" => $value->from === auth()->user()->id ? true:false,
                            "created_at" => $value->created_at
                        ];
                    }
                }
                
            }

            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $chat_arr;
            
            return response()->json($response);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function send_chat_support(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'media' => 'image|mimes:img,png,jpeg,jpg|max:2048',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        if(auth()->user()->hasRole('teknisi')){
            $role = 'teknisi';
        }else{
            $role = 'user';
        }

        $message = $request->get('message');
        $new = false;

        try {
            //code...
            // $user_1 = 1;
            // $user_2 = auth()->user()->id;
            $to = 1;
            $from = auth()->user()->id;
            $signature = $request->signature;

            DB::beginTransaction();

            $chat = [];
            $chatroom = Chatroom::where('user_1',$to)->where('user_2',$from);
            if($chatroom->count() == 0){
                $chatroom = Chatroom::where('user_1',$from)->where('user_2',$to);
            }

            $media = null;
            if($request->hasFile('media')){
                $uploadFolder = 'teknisi/chat/'.$from;
                $photo = $request->file('media');
                $photo_path = $photo->store($uploadFolder,'public');

                $media = Storage::disk('public')->url($photo_path);
            }
    

            if($chatroom->count() > 0){
                $chatroom = $chatroom->first();
                $chat = Chat::create([
                    "to" => $to,
                    "from" => $from,
                    "message" => $message,
                    "media" => $media,
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
                    "media" => $media,
                    "chatroom_id" => $chatroom->id,
                ]);

            }

            $to = [];
            $to[] = $chat->user_to->fcm_token;

            fcm()->to($to)
            ->priority('high')
            ->timeToLive(0)
            ->notification([
                'title' => 'Pesan Baru '.$chat->user_from->name??'',
                'body' => $chat->message,
            ])
            ->data([
                "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                "main_click_action" => "OPEN_CHAT_DETAIL",
                "chatroom_id" => (int)$chatroom->id,
                "action_data" => [
                    "task" => "ADD_CHAT_MESSAGE",
                    "chatroom_id" => (int)$chatroom->id,
                    "avatar" => $chat->user_from->profile_photo_path??'',
                    "name" => $chat->user_from->name,
                    "data" => [
                        "id" => (int)$chat->id,
                        "message" => $chat->message,
                        "from" => (int)$chat->from,
                        "is_me" => $chat->from==$chat->to?true:false,
                        "created_at" => $chat->created_at
                    ]
                ]
            ])
            ->send();
                                
            $response['id'] = $chat->id;
            $response['message'] = $chat->message;
            $response['signature'] = $signature;
            $response['media'] = $chat->media??'';
            $response['from'] = $from;
            $response['is_me'] = true;
            $response['created_at'] = $chat->created_at;
            
            DB::commit();
                    
            return response()->json($response);

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
        
        
    }

    public function store_chatroom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => "required",
            'from_type' => 'required',
            "to" => "required",
            "to_type" => "required"
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        try {
            //code...
            $to = $request->get('to');
            $from = $request->get('from');

            $chatroom = Chatroom::where('user_1',$to)->where('user_2',$from);
            if($chatroom->count() == 0){
                $chatroom = Chatroom::where('user_1',$from)->where('user_2',$to);
            }

            if($chatroom->count() > 0){
                $chatroom = $chatroom->first();
            }else{
                $chatroom = Chatroom::create([
                    "user_1" => $from,
                    "user_2" => $to
                ]);
            }

            return response()->json(["chatroom"=>$chatroom->id]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
        
    }
}
 