<?php

namespace App\Jobs;

use App\Mail\OtpMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Mail;

class SendEmailOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $to,$otp;

    public function __construct($to,$otp)
    {
        //
        $this->to = $to;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $emailOtp = new OtpMail($this->otp);
        Mail::to($this->to)->send($emailOtp);
    }
}
