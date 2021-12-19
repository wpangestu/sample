<?php

namespace App\Http\Controllers\Api\Customer;

use Exception;
use Illuminate\Http\Request;
use App\Jobs\SendEmailOtpJob;
use App\Services\AuthService;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    //
    protected $authService;

    public function __construct(AuthService $authService){
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        try {
            $requestRegister = $request->only(['name','email','phone','password','id_google']);
            $userRegister = $this->authService->registerCustomer($requestRegister);
            $tokenUser = $this->authService->generateToken($userRegister);

            $emailJobs = new SendEmailOtpJob($requestRegister['email'],$userRegister->code_otp);
            $this->dispatch($emailJobs);

            $data['message'] = "Register successfully";
            $data['token'] = $tokenUser['token'];
            $data['valid_until'] = $tokenUser['expired_at'];
            $data['token_type'] = $tokenUser['token_type'];
    
            return response()->json($data, 200);

        } catch (Exception $e) {
            return response()->json(["message" => "Terjadi kesalahan : " . $e->getMessage()], $e->getCode()??422);   
        }

    }

    public function login(Request $request){
        try {
            //code...
            if ($request->has('id_google')) {

                $requestLogin = $request->only(['email','id_google','device_id']);
                $userLogin = $this->authService->loginByGoogleId($requestLogin);
    
            }else{
                $requestLogin = $request->only(['email','password','device_id']);
                $userLogin = $this->authService->login($requestLogin);
            }
    
            $tokenUser = $this->authService->generateToken($userLogin);
    
            $data['message'] = "Login successfully";
            $data['token'] = $tokenUser['token'];
            $data['valid_until'] = $tokenUser['expired_at'];
            $data['token_type'] = $tokenUser['token_type'];
    
            return response()->json($data);

        } catch (Exception $e) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan : " . $e->getMessage()], $e->getCode()??422);
        }

    }

    public function check_valid(Request $request)
    {   
        try{            

            $requestCheckValid = $request->only(['device_id']);
            $checkValid = $this->authService->checkValidDeviceId($requestCheckValid);

            if($checkValid){
                return response()->json(['message' => 'Device id correct'], 200);
            }else{
                return response()->json(['message' => 'You have logged in other device'], 422);
            }
        
        } catch (Exception $e) {
            return response()->json(["message" => "Terjadi kesalahan : " . $e->getMessage()], $e->getCode()??422);
        }
    }

    public function request_otp(Request $request)
    {
        try {

            $requestOtp = $request->only(['email']);

            $otpUser = $this->authService->generateNewOtpUser($requestOtp);
    
            $emailJobs = new SendEmailOtpJob($requestOtp['email'],$otpUser);
            $this->dispatch($emailJobs);
    
            $message = "Kode Otp sudah dikirim ke email anda";
            return response()->json(["message" => $message], 200);

        } catch (Exception $e) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan : " . $e->getMessage()], $e->getCode()??422);
        }
    }

    public function confirmation_otp(Request $request)
    {
        $requestConfirmationOtp = $request->only(['email','code_otp']);

        try {

            $confirmationOtp = $this->authService->confirmationOtp($requestConfirmationOtp);

            if($confirmationOtp){
                $message = "Konfirmasi kode otp berhasil";
                return response()->json(["message" => $message]);
            }else{
                $message = "Kode otp salah";
                return response()->json(["message" => $message],423);
            }

        } catch (Exception $e) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $e->getMessage()], $e->getCode()??422);
        }
    }

    // Same with confirmation otp
    public function forgot_password_input_otp(Request $request)
    {
        $this->confirmation_otp($request);
    }
}
