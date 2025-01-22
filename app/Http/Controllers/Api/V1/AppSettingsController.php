<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Http\Request;
use App\Models\Admin\Language;
use App\Models\Admin\AppSettings;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Models\Admin\AppOnboardScreens;
use App\Http\Resources\SplashScreenResource;
use App\Http\Resources\OnboardScreenResource;
use App\Http\Helpers\Api\Helpers as ApiResponse;

class AppSettingsController extends Controller
{
    /**
     * Language Data Fetch
     *
     * @method GET
     * @return \Illuminate\Http\Response
    */
    public function languages()
    {
        try{
            $api_languages = get_api_languages();
        }catch(Exception $e) {
            $error = ['error'=>[$e->getMessage()]];
            return ApiResponse::error($error);
        }
        $data =[
            'languages' => $api_languages,
        ];
        $message =  ['success'=>[__('Language Data Fetch Successfully')]];
        return ApiResponse::success($message, $data);
    }

    /**
     * Basic Settings Data Fetch
     *
     * @method GET
     * @return \Illuminate\Http\Response
    */

    public function basicSettings()
    {
        $image_path = get_files_public_path('app-images');
        $logo_image_path = get_files_public_path('image-assets');
        $default_logo = get_files_public_path('default');
        $onboard_screen = new OnboardScreenResource(AppOnboardScreens::first());
        $splash_screen =  new SplashScreenResource(AppSettings::first());
        $all_logo = BasicSettings::select('site_logo_dark', 'site_logo', 'site_fav_dark', 'site_fav')->first();
   

        $data = [
            'default_logo'    => $default_logo,
            'logo_image_path' => $logo_image_path,
            'image_path'      => $image_path,
            'onboard_screen'  => $onboard_screen,
            'splash_screen'   => $splash_screen,
            'all_logo'        => $all_logo,
        ];
        $message = ['success' =>  [__('Data fetched successfully')]];
        return ApiResponse::success($message, $data);
    }
}
