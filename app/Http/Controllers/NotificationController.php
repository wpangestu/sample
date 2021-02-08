<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    public function test()
    {
        $recipients = [
            'fStn52P6Rike5S03z0oaUn:APA91bFwAgpakxHBojrq2FZ_MeE6-YMCtoR5hLS-4askMWUJmXBUG4Hf8G0qinT39sbUH4b8EE8_faTwNmvJiulwhvF4LNhgswIhl8X3vOLkBk_PGAFeV-JiSgP3HIyQ1BNYmcC4LNDE',
        ];
        // dd($recipients);
        try {
            //code...
            $cek = fcm()->to($recipients)
                    ->priority('high')
                    ->timeToLive(0)
                    ->data([
                        'title' => 'ini judul',
                        'body' => 'ini body',
                    ])
                    ->send();
            // dd($cek);
            return redirect()->route('service_category.index');
        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
        }

    }
}
