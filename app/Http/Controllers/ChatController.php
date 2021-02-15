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
        $engineers = User::Role('teknisi')->where('verified',true)->get();
        return view('chat.index',compact('engineers'));
    }

    public function index_customer()
    {
        $engineers = User::Role('user')->where('is_active',true)->get();
        return view('chat.index_customer',compact('engineers'));
    }

    public function get_user_chat(Request $request){

        try {
            //code...
            $user_id = $request->input('user_id');
            $user_admin = auth()->user()->id;
            $chatroom = Chatroom::where('user_1',$user_id)->where('user_2',$user_admin);
            $chat = [];
            if($chatroom->count() == 0){
                $chatroom = Chatroom::where('user_1',$user_admin)->where('user_2',$user_id);
            }

            if($chatroom->count() > 0){
                $chat = Chat::where('chatroom_id',$chatroom->first()->id)->orderBy('created_at','desc')->get();
            }

            $response['chatroom'] = $chatroom->get();
            $chat_response = [];
            foreach ($chat as $key => $value) {
                # code...
                $chat_response[] = [
                    "id" => $value->id,
                    "to" => $value->id,
                    "from" => $value->from,
                    "message" => $value->message,
                    "chatroom_id" => $value->chatromm_id,
                    "media" => $value->media,
                    "name" => $value->user_from->name,
                    "created_at" => $value->created_at->format('d/m/Y H:i')
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

                    $chat = Chat::create([
                        "to" => $user_id,
                        "from" => $user_admin,
                        "message" => $message,
                        "chatroom_id" => $chatroom->first()->id,
                    ]);

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

    public function show($id){
        
    }
}
