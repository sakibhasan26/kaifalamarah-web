<?php

namespace App\Http\Controllers\Api\v1\Auth;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use Illuminate\Support\Carbon;
use App\Models\UserAuthorization;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Notifications\User\Auth\SendAuthorizationCode;

class AuthorizationController extends Controller
{
    /**
     * Verify user mail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resendCode(Request $request)
    {
        $user = auth()->user();
        $resend = UserAuthorization::where("user_id",$user->id)->first();
        if($resend){
            if(Carbon::now() <= $resend->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {

                $error = ['error'=>['You can resend verification code after '.Carbon::now()->diffInSeconds($resend->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)). ' seconds']];
                return ApiResponse::error($error);
            }
        }
        $data = [
            'user_id'       =>  $user->id,
            'code'          => generate_random_code(),
            'token'         => generate_unique_string("user_authorizations","token",200),
            'created_at'    => now(),
        ];
        DB::beginTransaction();
        try{
            if($resend) {
                UserAuthorization::where("user_id", $user->id)->delete();
            }
            DB::table("user_authorizations")->insert($data);
            $user->notify(new SendAuthorizationCode((object) $data));
            DB::commit();
            $message =  ['success'=>[__('Verification code send success')]];
            return ApiResponse::onlySuccess($message);
        }catch(Exception $e) {
            DB::rollBack();
            $error = ['error'=>[__('Something went wrong! Please try again')]];
            return ApiResponse::error($error);
        }
    }

    /**
     * Verify user mail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
        ]);
        if($validator->fails()){
            $error =  ['error'=>$validator->errors()->all()];
            return ApiResponse::validation($error);
        }
        $code = $request->otp;
        $user = Auth::user();
        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        $auth_column = UserAuthorization::where("code",$code)->where('user_id', $user->id)->first();

        if(!$auth_column){
             $error = ['error'=>[__('Verification code does not match')]];
            return ApiResponse::error($error);
        }
        if($auth_column->created_at->addSeconds($otp_exp_sec) < now()) {
            $error = ['error'=>[__('Session expired. Please try again')]];
            return ApiResponse::error($error);
        }
        try{
            $auth_column->user->update([
                'email_verified'    => true,
            ]);
            $auth_column->delete();
        }catch(Exception $e) {
            $error = ['error'=>[__('Something went wrong! Please try again')]];
            return ApiResponse::error($error);
        }
        $message =  ['success'=>[__('Account successfully verified')]];
        return ApiResponse::onlySuccess($message);
    }

    /**
     * Verify user mail
     *
     * @method GET
     * @return \Illuminate\Http\Response
     */

     public function logout(){
        Auth::user()->token()->revoke();
        $message = ['success'=>[__('Logout Successful')]];
        return ApiResponse::success($message);
     }
}
