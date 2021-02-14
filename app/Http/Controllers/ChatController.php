<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chatroom;
use App\Models\Chat;

class ChatController extends Controller
{
    //
    public function index()
    {
        $engineers = User::Role('teknisi')->where('verified',true)->get();
        return view('chat.index',compact('engineers'));
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
            $response['chat'] = $chat;

            return response()->json($response);
            
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($th->getMessage());
        }


    }
}
