<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Campaign;
use App\Models\Admin\Event;
use Illuminate\Support\Str;
use App\Models\CategoryType;
use Illuminate\Http\Request;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User\UserResouce;
use App\Http\Helpers\Api\Helpers as ApiResponse;

class DashboardController extends Controller
{
    /**
     * Dashboard Data Fetch
     *
     * @method GET
     * @return \Illuminate\Http\Response
    */

    public function dashboard(Request $request){
        $lang = $request->language;
        $default = 'en';

        $campaigns = Campaign::getData(1)->orderBy('id', 'desc')->get()->map(function($data) use ($lang, $default){

            $title = isset($data->title->language->$lang) ? $data->title->language->$lang->title : $data->title->language->$default->title;
            $desc = isset($data->desc->language->$lang) ? $data->desc->language->$lang->desc : $data->desc->language->$default->desc;

            return[
                'id'         => $data->id,
                'admin_id'   => $data->admin_id,
                'slug'       => $data->slug,
                'title'      => $title,
                'desc'       => $desc,
                'our_goal'   => $data->our_goal,
                'raised'     => $data->raised,
                'to_go'      => $data->to_go,
                'image'      => $data->image,
                'status'     => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ];
        });

        // Campaign data
        $campaign_data = [
            'default_image' => get_files_public_path('default'),
            'image_path' => get_files_public_path('campaigns'),
            'data' => $campaigns,
        ];

        // Gallery
        $gallery_slug = Str::slug(SiteSectionConst::GALLERY_SCTION);
        $gallery = SiteSections::getData($gallery_slug)->first();

        if(isset($gallery->value->items)){
            $gallery_items = $gallery->value->items;
            $galleriers = [];
            foreach ($gallery_items ?? [] as $key => $value) {
                $title = isset($value->language->$lang) ? $value->language->$lang->title : $value->language->$default->title;
                $tag = isset($value->language->$lang) ? $value->language->$lang->tag : $value->language->$default->tag;
                $galleriers[] = [
                    'id'    => $value->id,
                    'title' => $title,
                    'tag'   => $tag,
                    'image' => $value->image,
                ];
            }

        }else{
            $galleriers = [];
        }

        // Gallery Data
        $gallery_data = [
            'default_image' => get_files_public_path('default'),
            'image_path' => get_files_public_path('site-section'),
            'data' => $galleriers,
        ];

        // Testimonial Data
        $testimonial =  SiteSections::where('key', 'testimonial-section')->first();
        if(isset($testimonial->value->items)){
            $testimonial_items = $testimonial->value->items;
            $testimonials = [];
            foreach ($testimonial_items ?? [] as $key => $value) {
                $name = isset($value->language->$lang) ? $value->language->$lang->name : $value->language->$default->name;
                $details = isset($value->language->$lang) ? $value->language->$lang->details : $value->language->$default->details;
                $testimonials[] = [
                    'id'      => $value->id,
                    'name'    => $name,
                    'details' => $details,
                    'image'   => $value->image,
                ];
            }

        }else{
            $testimonials = [];
        }


        $testimonial_data = [
            'default_image' => get_files_public_path('default'),
            'image_path' => get_files_public_path('site-section'),
            'data' => $testimonials,
        ];

        // User Data
        $user = Auth::guard(get_auth_guard())->user();
        $user_data =[
            'default_image' => "public/backend/images/default/profile-default.webp",
            "image_path"    => "public/frontend/user",
            'user'          => isset($user) ? new UserResouce($user) : [],
        ];

        $data = [
            'campaigns'    => $campaign_data,
            'galleries'    => $gallery_data,
            'testimonials' => $testimonial_data,
            'user'         => $user_data,
        ];

        $message =  ['success'=>[__('Dashboard data successfully fetch')]];
        return ApiResponse::success($message, $data);
    }

    /**
     * Campaigns Data Fetch
     *
     * @method GET
     * @return \Illuminate\Http\Response
    */

    public function campaigns(Request $request){

        $lang = $request->language;
        $default = 'en';

        $campaigns = Campaign::getData(1)->orderBy('id', 'desc')->get()->map(function($data) use ($lang, $default){

            $title = isset($data->title->language->$lang) ? $data->title->language->$lang->title : $data->title->language->$default->title;
            $desc = isset($data->desc->language->$lang) ? $data->desc->language->$lang->desc : $data->desc->language->$default->desc;

            return[
                'id'         => $data->id,
                'admin_id'   => $data->admin_id,
                'slug'       => $data->slug,
                'title'      => $title,
                'desc'       => $desc,
                'our_goal'   => $data->our_goal,
                'raised'     => $data->raised,
                'to_go'      => $data->to_go,
                'image'      => $data->image,
                'status'     => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ];

        });

        // Campaign data
        $data = [
            'default_image' => get_files_public_path('default'),
            'image_path'    => get_files_public_path('campaigns'),
            'campaigns'     => $campaigns,
        ];

        $message =  ['success'=>[__('Campaigns successfully fetch')]];
        return ApiResponse::success($message, $data);
    }

    /**
     * Campaign Details Data Fetch
     *
     * @method GET
     * @return \Illuminate\Http\Response
    */

    public function campaignDetails(Request $request){
        $lang = $request->language;
        $default = 'en';

        $data = Campaign::where('id', $request->id)->first();

        $title = isset($data->title->language->$lang) ? $data->title->language->$lang->title : $data->title->language->$default->title;
        $desc = isset($data->desc->language->$lang) ? $data->desc->language->$lang->desc : $data->desc->language->$default->desc;

        $campaign = [
            'id'         => $data->id,
            'admin_id'   => $data->admin_id,
            'slug'       => $data->slug,
            'title'      => $title,
            'desc'       => $desc,
            'our_goal'   => $data->our_goal,
            'raised'     => $data->raised,
            'to_go'      => $data->to_go,
            'image'      => $data->image,
            'status'     => $data->status,
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,
        ];

        // Campaign data
        $data = [
            'default_image' => get_files_public_path('default'),
            'image_path'    => get_files_public_path('campaigns'),
            'campaign'      => $campaign,
        ];

        $message =  ['success'=>[__('Campaign Details successfully fetch')]];
        return ApiResponse::success($message, $data);
    }

    /**
     * Events Data Fetch
     *
     * @method GET
     * @return \Illuminate\Http\Response
    */

    public function events(Request $request){
        $lang = $request->language;
        $default = 'en';

        // All Events
        $events = Event::with('category:id,name')->where('status', 1)->get()->map(function($data) use ($lang, $default){

            $title   = isset($data->title->language->$lang) ? $data->title->language->$lang->title : $data->title->language->$default->title;
            $details = isset($data->details->language->$lang) ? $data->details->language->$lang->details : $data->details->language->$default->details;
            $tags    = isset($data->tags->language->$lang) ? $data->tags->language->$lang->tags : $data->tags->language->$default->tags;

            return[
                'id'         => $data->id,
                'admin_id'   => $data->admin_id,
                'slug'       => $data->slug,
                'title'      => $title,
                'details'    => $details,
                'tags'       => $tags,
                'image'      => $data->image,
                'status'     => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ];
        });


        // Recent Events
        $recent_events = Event::with('category:id,name')->where('status', 1)->orderBy('id', 'desc')->limit(3)->get()->map(function($data) use ($lang, $default){

            $title   = isset($data->title->language->$lang) ? $data->title->language->$lang->title : $data->title->language->$default->title;
            $details = isset($data->details->language->$lang) ? $data->details->language->$lang->details : $data->details->language->$default->details;
            $tags    = isset($data->tags->language->$lang) ? $data->tags->language->$lang->tags : $data->tags->language->$default->tags;

            return[
                'id'         => $data->id,
                'admin_id'   => $data->admin_id,
                'slug'       => $data->slug,
                'title'      => $title,
                'details'    => $details,
                'tags'       => $tags,
                'image'      => $data->image,
                'status'     => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ];
        });

        // Evemts data
        $events_data = [
            'default_image' => get_files_public_path('default'),
            'image_path'    => get_files_public_path('events'),
            'events'        => $events,
        ];

        // Recent Events data
        $recent_events_data = [
            'default_image' => get_files_public_path('default'),
            'image_path'    => get_files_public_path('events'),
            'events'        => $recent_events,
        ];

        $categories = CategoryType::where('type', 2)->where('status', 1)->orderBy('id','desc')->get();
        if(isset($categories)){
            $category = [];
            foreach ($categories ?? [] as $value) {
                $name =  $value->data->language->$lang->name ?? $value->data->language->$default->name;
                $category[] = [
                    'id'      => $value->id,
                    'name'    => $name,
                ];
            }

        }else{
            $category = [];
        }
        $data = [
            'events'        => $events_data,
            'recent_events' => $recent_events_data,
            'categories'    => $category,
        ];

        $message =  ['success'=>[__('Events successfully fetch')]];
        return ApiResponse::success($message, $data);
    }


    /**
     * Event Details Data Fetch
     *
     * @method GET
     * @return \Illuminate\Http\Response
    */

    public function eventDetails(Request $request){
        $lang = $request->language;
        $default = 'en';

        $data = Event::where('id', $request->id)->first();

        $title   = isset($data->title->language->$lang) ? $data->title->language->$lang->title : $data->title->language->$default->title;
        $details = isset($data->details->language->$lang) ? $data->details->language->$lang->details : $data->details->language->$default->details;
        $tags    = isset($data->tags->language->$lang) ? $data->tags->language->$lang->tags : $data->tags->language->$default->tags;

        $event = [
            'id'         => $data->id,
            'admin_id'   => $data->admin_id,
            'slug'       => $data->slug,
            'title'      => $title,
            'details'    => $details,
            'tags'       => $tags,
            'image'      => $data->image,
            'status'     => $data->status,
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,
        ];

        // Campaign data
        $data = [
            'default_image' => get_files_public_path('default'),
            'image_path'    => get_files_public_path('events'),
            'event'         => $event,
        ];

        $message =  ['success'=>[__('Event Details successfully fetch')]];
        return ApiResponse::success($message, $data);
    }


    /**
     * About Us Data Fetch
     *
     * @method GET
     * @return \Illuminate\Http\Response
    */
    public function aboutUs(Request $request){
        $lang = $request->language;
        $default = 'en';

        $section_slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $about = SiteSections::getData( $section_slug)->first();

        if(isset($about->value)){

            $about = $about->value;

            $images = $about->images;
            $details = [
                'fitst_section_title' => isset($about->language->$lang) ? $about->language->$lang->fitst_section_title : $about->language->$default->fitst_section_title,
                'fitst_section_heading' => isset($about->language->$lang) ? $about->language->$lang->fitst_section_heading : $about->language->$default->fitst_section_heading,
                'first_section_sub_heading' => isset($about->language->$lang) ? $about->language->$lang->first_section_sub_heading : $about->language->$default->first_section_sub_heading,
                'first_section_button_name' => isset($about->language->$lang) ? $about->language->$lang->first_section_button_name : $about->language->$default->first_section_button_name,
                'first_section_button_link' =>  url($about->language->$default->first_section_button_link),
                'second_section_video_link' =>  url($about->language->$default->second_section_video_link),
                'second_section_title' => isset($about->language->$lang) ? $about->language->$lang->second_section_title : $about->language->$default->second_section_title,
                'second_section_heading' => isset($about->language->$lang) ? $about->language->$lang->second_section_heading : $about->language->$default->second_section_heading,
                'second_section_sub_heading' => isset($about->language->$lang) ? $about->language->$lang->second_section_sub_heading : $about->language->$default->second_section_sub_heading,
            ];


            $about_item = [];
            foreach ($about->items ?? [] as $kwy => $value) {
                $title = isset($value->language->$lang) ? $value->language->$lang->title : $value->language->$default->title;
                $about_item[] = [
                    'id'    => $value->id,
                    'title' => $title,
                ];
            }

            $about_us = [
                'images'     => $images,
                'details'    => $details,
                'about_item' => $about_item,
            ];

        }else{
            $about_us = [];
        }

        // Testimonial Data
        $testimonial =  SiteSections::where('key', 'testimonial-section')->first();
        if(isset($testimonial->value->items)){
            $testimonial_items = $testimonial->value->items;
            $testimonials = [];
            foreach ($testimonial_items ?? [] as $key => $value) {
                $name = isset($value->language->$lang) ? $value->language->$lang->name : $value->language->$default->name;
                $details = isset($value->language->$lang) ? $value->language->$lang->details : $value->language->$default->details;
                $testimonials[] = [
                    'id'      => $value->id,
                    'name'    => $name,
                    'details' => $details,
                    'image'   => $value->image,
                ];
            }

        }else{
            $testimonials = [];
        }


        $testimonial_data = [
            'default_image' => get_files_public_path('default'),
            'image_path' => get_files_public_path('site-section'),
            'data' => $testimonials ?? [],
        ];

        $about_data = [
            'default_image' => get_files_public_path('default'),
            'image_path' => get_files_public_path('site-section'),
            'about_us' => $about_us ?? [],
        ];

        $data = [
            'about_us'    => $about_data,
            'testimonial' => $testimonial_data,
        ];

        $message =  ['success'=>[__('Dashboard data successfully fetch')]];
        return ApiResponse::success($message, $data);
    }


}
