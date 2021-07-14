<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class NotificationController extends Controller
{
    //
    public function test()
    {
        $users = User::Role('teknisi')
                            ->where('verified',true)
                            ->whereNotNull('fcm_token')
                            ->get();
        $recipients[] = 'e1AKEDODSwyl-l_5eY0Lth:APA91bEeL6PxE10INn2Mvy5Oz7TofhrdULlGvuFcLSqViHPQldg_ZyrOr6UkapBKQZL0teupH-_u6P9csj6NSIsBdNYhBBTgCUL5CmRLjbq_4vAyoroBp66qtvhaz5Hukx80Ecp5f4kf';
        $recipients[] = '123';
        // dd($recipients);
        
        try {
            //code...
            $cek = fcm()->to($recipients)
                    ->priority('high')
                    ->timeToLive(60)
                    ->data([
                        'title' => 'New Message',
                        'body' => 'ini body',
                    ])
                    ->send();
            // return redirect()->route('service_category.index');
        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
        }
    }

    public function saveTokenToServer(Request $request)
    {
        $token = $request->input('token');

        try {
            //code...
            $user = User::find(auth()->user()->id);

            $user->fcm_token = $token;
            $user->save();

            $response = [
                "success" => true,
                "message" => "token updated",
                "user_id" => $user->id
            ];

            return response()->json($response);
            
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
            //throw $th;
        }

    }
}
