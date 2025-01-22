<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Validated;
use App\Http\Resources\User\UserResouce;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Providers\Admin\BasicSettingsProvider;
use App\Http\Helpers\Api\Helpers as ApiResponse;

class ProfileController extends Controller
{
    /**
     * Profile Get Data
     *
     * @method GET
     * @return \Illuminate\Http\Response
    */

    public function profile(){
        $user = Auth::user();

        $data =[
            'default_image' => "public/backend/images/default/profile-default.webp",
            "image_path"    => "public/frontend/user",
            'user'          => new UserResouce($user),
        ];

        $message =  ['success'=>['User Profile']];

        return ApiResponse::success($message,$data);
    }

    /**
     * Profile Update
     *
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function profileUpdate(Request $request){

        $validator = Validator::make($request->all(),[
            'first_name'    => "required|string|max:60",
            'last_name'     => "required|string|max:60",
            'image'         => "nullable|image|mimes:jpg,png,svg,webp|max:10240",
        ]);

        $user = auth()->user();

        if($validator->fails()){
            $error = ['error' => [$validator->errors()->all()]];
            return ApiResponse::validation($error);
        }

        $validated = $validator->validated();

        $validated['firstname']   = $validated['first_name'];
        $validated['lastname']    = $validated['last_name'];

        if($request->hasFile('image')){

            if($user->image == null){
                $oldImage = null;
            }else{
                $oldImage = $user->image;
            }

            $image = upload_file($validated['image'],'user-profile', $oldImage);
            $upload_image = upload_files_from_path_dynamic([$image['dev_path']],'user-profile');
            delete_file($image['dev_path']);
            $validated['image']     = $upload_image;
        }

        try {
            $user->update($validated);
        } catch (\Throwable $th) {
            $error = ['error'=>[__('Something went wrong! Please try again')]];
            return ApiResponse::error($error);
        }

        $message =  ['success'=>[__('Profile successfully updated')]];
        return ApiResponse::onlySuccess($message);
    }


    public function passwordUpdate(Request $request){
        $basic_settings = BasicSettingsProvider::get();

        $passowrd_rule = 'required|string|min:6|confirmed';

        if($basic_settings->secure_password) {
            $passowrd_rule = ["required",Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised(),"confirmed"];
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:6',
            'password' =>$passowrd_rule,
        ]);

        if($validator->fails()){
            $error =  ['error'=>$validator->errors()->all()];
            return ApiResponse::validation($error);
        }

        $validated = $validator->validate();

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            $message = ['error' =>  ['Current password didn\'t match']];
            return ApiResponse::error($message);
        }
        try {
            Auth::user()->update(['password' => Hash::make($validated['password'])]);
            $message = ['success' =>  [__('Password updated successfully')]];
            return ApiResponse::onlySuccess($message);
        } catch (Exception $ex) {
            // info($ex);
            $message = ['error' =>  [__('Something went wrong! Please try again')]];
            return ApiResponse::error($message);
        }

    }


    /**
     * Account Delete
     *
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function deleteAccount(Request $request){
        $user = Auth::guard(get_auth_guard())->user();
        if(!$user){
            $message = ['success' =>  [__('No user found')]];
            return ApiResponse::error($message, []);
        }

        try {
            $user->status            = 0;
            $user->deleted_at        = now();
            $user->save();
        } catch (\Throwable $th) {
            $message = ['success' =>  [__('Something went wrong! Please try again')]];
            return ApiResponse::error($message, []);
        }

        $message = ['success' =>  [__('User deleted successful')]];
        return ApiResponse::success($message, $user);
    }

}
