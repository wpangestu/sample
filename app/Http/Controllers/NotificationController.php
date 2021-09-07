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
        $recipients[] = 'ehXgXLoXTICtMPz5IlEfSW:APA91bGoVKrrKj96UMRUtqkFWi5PniF0ngk3T4rt8VP0ez3M0qmRTISCParwVRBvIzb32AyhprSd_CaCpmTNUp0qQ2DkTDv2XD28M5bVFurcQ-LQQaO0Ag4sL0t88siYc1d0G2qeeci1';
        
        try {
            //code...
            $cek = fcm()->to($recipients)
                    ->priority('high')
                    ->timeToLive(60)
                    ->notification([
                        'title' => 'Test FCM',
                        'body' => 'This is a test of FCM',
                    ])
                    ->send();
            echo "sukses";
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
