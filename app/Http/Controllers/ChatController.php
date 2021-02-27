<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chatroom;
use App\Models\Chat;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    //
    public function index()
    {
        $user_id = auth()->user()->id;
        if(auth()->user()->hasRole('cs')){
            $user_id = 1;
        }
        $new_chatroom_data = $this->getChatroomNew($user_id,'teknisi');

        $new_user_id = [];
        foreach ($new_chatroom_data as $key => $value) {
            $new_user_id[] = $value['user_id'];
        }
        $engineers = User::Role('teknisi')
                            ->where('verified',true)
                            ->whereNotIn('id',$new_user_id)
                            ->get();

        return view('chat.index',compact('engineers','new_chatroom_data'));
    }

    public function index_customer()
    {

        $user_id = auth()->user()->id;
        if(auth()->user()->hasRole('cs')){
            $user_id = 1;
        }
        $new_chatroom_data = $this->getChatroomNew($user_id,'user');

        $new_user_id = [];
        foreach ($new_chatroom_data as $key => $value) {
            $new_user_id[] = $value['user_id'];
        }
        $engineers = User::Role('user')
                            ->where('is_active',true)
                            ->whereNotIn('id',$new_user_id)
                            ->get();
        
        return view('chat.index_customer',compact('engineers','new_chatroom_data'));

    }

    public function update_list_user_chat(Request $request)
    {
        $type = $request->input('type');
        $user_id = auth()->user()->id;
        if(auth()->user()->hasRole('cs')){
            $user_id = 1;
        }

        if($type==="teknisi"){

            $new_chatroom_data = $this->getChatroomNew($user_id,'teknisi');        
            $new_user_id = [];
            foreach ($new_chatroom_data as $key => $value) {
                $new_user_id[] = $value['user_id'];
            }
            $engineers = User::Role('teknisi')
                                ->where('verified',true)
                                ->whereNotIn('id',$new_user_id)
                                ->get();
            $response = [
                "user_with_new_message" => $new_chatroom_data,
                "user_with_no_message" => $engineers
            ];

            return response()->json($response);
        }

    }


    public function getChatroomNew($user_id,$role){
        // dd($role);
        $new_chatroom_data = [];
        $chatroom = Chatroom::where('user_1',$user_id)->orWhere('user_2',$user_id)->orderBy('updated_at','desc')->get();
        foreach ($chatroom as $key => $value) {
            
            if($value->user_1 === $user_id){
                $user = $value->user_2_data;
            }else{
                $user = $value->user_1_data;
            }
            // dd($user);
            // dd($user->hasRole('teknisi'));
            if($user->hasRole($role)){
                
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
                    "user_id" => $user->id,
                    "user_name" => $user->name,
                    "userid" => $user->userid,
                    "unread_count" => $unread_message,
                    "last_message" => $chat_data
                ];
                $new_chatroom_data[] = $chatroom_t;

            }
        }
        return $new_chatroom_data;
    }


    public function get_user_chat(Request $request){

        try {
            //code...
            $user_id = $request->input('user_id');
            $user_admin = auth()->user()->id;
            if(auth()->user()->hasRole('cs')){
                $user_admin = 1;
            }
            $chatroom = Chatroom::where('user_1',$user_id)->where('user_2',$user_admin);
            $chat = [];
            if($chatroom->count() == 0){
                $chatroom = Chatroom::where('user_1',$user_admin)->where('user_2',$user_id);
            }

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;

            if($chatroom->count() > 0){
                $chat = Chat::where('chatroom_id',$chatroom->first()->id)
                            ->offset(($page - 1) * $limit)
                            ->limit($limit)
                            ->orderBy('created_at','desc')
                            ->get();
            }

            $response['chatroom'] = $chatroom->get();
            $chat_response = [];
            foreach ($chat as $key => $value) {
                # code...
                $admin_cs = false;
                $role = "";
                if($value->user_from->hasRole(['admin','cs'])){
                    $admin_cs = true; 
                    if($value->user_from->hasRole(['cs'])){
                        $role = "cs";
                    }else{
                        $role = "admin";
                    }
                }
                $chat_response[] = [
                    "id" => $value->id,
                    "to" => $value->id,
                    "from" => $value->from,
                    "message" => $value->message,
                    "chatroom_id" => $value->chatromm_id,
                    "media" => $value->media,
                    "name" => $value->user_from->name,
                    "created_at" => $value->created_at->format('d/m/Y H:i'),
                    "admin_cs" => $admin_cs,
                    "role" => $role
                ];
            }            

            $response['chat'] = $chat_response;
            $response['name'] = User::find($user_id)->name;

            return response()->json($response);
            
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage());
        }

    }

    public function store_chat(Request $request)
    {

        if($request->has('user_id')){

            $user_id = $request->input('user_id');
            $user_admin = auth()->user()->id;
            
            if(auth()->user()->hasRole('cs')){
                $user_admin = 1;
            }

            $message = $request->input('message');
            $new = false;
            try {
                //code...

                DB::beginTransaction();

                $chatroom = Chatroom::where('user_1',$user_id)->where('user_2',$user_admin);
                $chat = [];
                if($chatroom->count() == 0){
                    $chatroom = Chatroom::where('user_1',$user_admin)->where('user_2',$user_id);
                }
    
                if($chatroom->count() > 0){

                    // $chatroom->first();
                    $chatroom = $chatroom->first();
                    $chat = Chat::create([
                        "to" => $user_id,
                        "from" => auth()->user()->id,
                        "message" => $message,
                        "chatroom_id" => $chatroom->id,
                    ]);

                    $chatroom->updated_at = date("Y-m-d H:i:s");
                    $chatroom->save();

                }else{
                    $new = true;
                    $chatroom = Chatroom::create([
                        "user_1" => $user_admin,
                        "user_2" => $user_id
                    ]);

                    $chat = Chat::create([
                        "to" => $user_id,
                        "from" => $user_admin,
                        "message" => $message,
                        "chatroom_id" => $chatroom->id,
                    ]);
                }

                DB::commit();

                $response['success'] = true;
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

    }

    public function show($id,Request $request){

        $user = User::where('userid',$id)->first();
        
        if(is_null($user)){
            dd('User Not Found');
        }

        $user_admin = auth()->user()->id;
        if(auth()->user()->hasRole('cs')){
            $user_admin = 1;
        }
        $chatroom = Chatroom::where('user_1',$user->id)->where('user_2',$user_admin);
        $chat = [];
        if($chatroom->count() == 0){
            $chatroom = Chatroom::where('user_1',$user_admin)->where('user_2',$user->id);
        }

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('size') ? $request->get('size') : 10;

        if($chatroom->count() > 0){
            $chat = Chat::where('chatroom_id',$chatroom->first()->id)
                        ->offset(($page - 1) * $limit)
                        ->limit($limit)
                        ->orderBy('created_at','desc')
                        ->get();
        }

        
        if($user->roles[0]->name ==="teknisi"){

            $new_chatroom_data = $this->getChatroomNew($user_admin,'teknisi');

            $new_user_id = [];
            foreach ($new_chatroom_data as $key => $value) {
                $new_user_id[] = $value['user_id'];
            }
            $engineers = User::Role('teknisi')
                                ->where('verified',true)
                                ->whereNotIn('id',$new_user_id)
                                ->get();
            return view('chat.index',compact('engineers','chat','user','new_chatroom_data','user_admin'));
        }else{

            $new_chatroom_data = $this->getChatroomNew($user_admin,'user');
            
            $new_user_id = [];
            foreach ($new_chatroom_data as $key => $value) {
                $new_user_id[] = $value['user_id'];
            }
            $engineers = User::Role('user')
                                ->where('is_active',true)
                                ->whereNotIn('id',$new_user_id)
                                ->get();

            // $engineers = User::Role('user')->where('verified',true)->get();
            return view('chat.index_customer',compact('engineers','chat','user','new_chatroom_data','user_admin'));
        }

        // return view('chat.index_customer',compact('engineers','chat'));
    }
}
