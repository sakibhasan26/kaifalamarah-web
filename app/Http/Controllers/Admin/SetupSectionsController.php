<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\FaqSection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\CategoryType;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Models\Admin\Language;
use App\Constants\LanguageConst;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;

class SetupSectionsController extends Controller
{
    protected $languages;

    public function __construct()
    {
        $this->languages = Language::whereNot('code',LanguageConst::NOT_REMOVABLE)->get();
    }

    /**
     * Register Sections with their slug
     * @param string $slug
     * @param string $type
     * @return string
     */
    public function section($slug,$type) {
        $sections = [
            'login-section'    => [
                'view'   => "loginView",
                'update' => "loginUpdate",
            ],
            'register-section'    => [
                'view'   => "registerView",
                'update' => "registerUpdate",
            ],
            'breadcrumb-section'    => [
                'view'   => "breadcrumbView",
                'update' => "breadcrumbUpdate",
            ],
            'home_banner'    => [
                'view'   => "bannerView",
                'update' => "bannerUpdate",
            ],
            'service_section'  => [
                'view'       => "serviceView",
                'itemStore'  => "serviceStore",
                'itemUpdate' => "serviceUpdate",
                'itemDelete' => "serviceDelete",
            ],
            'overview-section-left'  => [
                'view'       => "overviewLeftView",
                'update'     => "overviewLeftUpdate",
                'itemStore'  => "overviewLeftItemStore",
                'itemUpdate' => "overviewLeftItemUpdate",
                'itemDelete' => "overviewLeftItemDelete",
            ],
            'overview-section-right'  => [
                'view'       => "overviewRightView",
                'update'     => "overviewRightUpdate",
                'itemStore'  => "overviewRightItemStore",
                'itemUpdate' => "overviewRightItemUpdate",
                'itemDelete' => "overviewRightItemDelete",
            ],
            'subscribe-section'  => [
                'view'       => "subscribeView",
                'update'     => "subscribeUpdate",
            ],
            'statistics-section'  => [
                'view'       => "statisticsView",
                'update'     => "statisticsUpdate",
            ],
            'about_section'  => [
                'view'       => "aboutView",
                'update'     => "aboutUpdate",
                'itemStore'  => "aboutItemStore",
                'itemUpdate' => "aboutItemUpdate",
                'itemDelete' => "aboutItemDelete",
            ],
            'download-app'    => [
                'view'   => "downloadAppView",
                'update' => "downloadAppUpdate",
            ],
            'gallery-section'    => [
                'view'       => "galleryView",
                'itemStore'  => "galleryItemStore",
                'itemUpdate' => "galleryItemUpdate",
                'itemDelete' => "galleryItemDelete",
            ],
            'partner_section'  => [
                'view'       => "partnerView",
                'update'     => "partnerUpdate",
                'itemStore'  => "partnerItemStore",
                'itemUpdate' => "partnerItemUpdate",
                'itemDelete' => "partnerItemDelete",
            ],
            'testimonial_section'  => [
                'view'       => "testimonialView",
                'update'     => "testimonialUpdate",
                'itemStore'  => "testimonialItemStore",
                'itemUpdate' => "testimonialItemUpdate",
                'itemDelete' => "testimonialItemDelete",
            ],
            'video-section'  => [
                'view'   => "videoView",
                'update' => "videoUpdate",
            ],
             'contact'    => [
                'view'   => "contactView",
                'update' => "contactUpdate",
            ],
            'footer-section'  => [
                'view'       => "footerView",
                'update'     => "footerUpdate",
                'itemStore'  => "footerItemStore",
                'itemUpdate' => "footerItemUpdate",
                'itemDelete' => "footerItemDelete",
            ],
            'category'    => [
                'view' => "categoryView",
            ],
            'faq-section'    => [
                'view' => "faqView",
            ],

        ];

        if(!array_key_exists($slug,$sections)) abort(404);
        if(!isset($sections[$slug][$type])) abort(404);
        $next_step = $sections[$slug][$type];
        return $next_step;
    }

    /**
     * Method for getting specific step based on incomming request
     * @param string $slug
     * @return method
     */
    public function sectionView($slug) {
        $section = $this->section($slug,'view');
        return $this->$section($slug);
    }

    /**
     * Method for distribute store method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemStore(Request $request, $slug) {
        $section = $this->section($slug,'itemStore');
        return $this->$section($request,$slug);
    }

    /**
     * Method for distribute update method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemUpdate(Request $request, $slug) {
        $section = $this->section($slug,'itemUpdate');
        return $this->$section($request,$slug);
    }

    /**
     * Method for distribute delete method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemDelete(Request $request,$slug) {
        $section = $this->section($slug,'itemDelete');
        return $this->$section($request,$slug);
    }

    /**
     * Method for distribute update method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionUpdate(Request $request,$slug) {
        $section = $this->section($slug,'update');
        return $this->$section($request,$slug);
    }
    //========================LOGIN SECTION  Section Start============================
    public function loginView($slug) {
        $page_title = __('Login Section');
        $section_slug = Str::slug(SiteSectionConst::LOGIN_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.login-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function loginUpdate(Request $request,$slug) {
        $basic_field_name = [
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::LOGIN_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        $data['images']['login_image'] = $section->value->images->login_image ?? "";
        if($request->hasFile("login_image")) {
            $data['images']['login_image']      = $this->imageValidate($request,"login_image",$section->value->images->auth_image ?? null);
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }
    //========================LOGIN SECTION  Section End============================
    //========================REGISTER SECTION  Section Start============================
    public function registerView($slug) {
        $page_title = __('Register Section');
        $section_slug = Str::slug(SiteSectionConst::REGISTER_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.register-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function registerUpdate(Request $request,$slug) {
        $validator = Validator::make($request->all(),[
            'agree_policy_link' => 'required|string'
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','category-add');
        }

        $validated = $validator->validate();

        $basic_field_name = [
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string",
            'agree_policy_title' => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::REGISTER_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        $validated['images']['register_image'] = $section->value->images->register_image ?? "";
        if($request->hasFile("register_image")) {
            $validated['images']['register_image']      = $this->imageValidate($request,"register_image",$section->value->images->auth_image ?? null);
        }

        $validated['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $validated;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }
    //========================REGISTER SECTION  Section End============================
    //=======================Video SECTION  Section Start============================
    public function videoView($slug) {
        $page_title = __('Video Section');
        $section_slug = Str::slug(SiteSectionConst::VIDEO_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.video-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function videoUpdate(Request $request,$slug) {
        $basic_field_name = [
            'view_info' => "required|string|max:100",
            'heading' => "required|string|max:100",
            'video_link' => "required|string|url|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::VIDEO_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }
    //=======================video SECTION Section End==============================
    //=======================breadcrumb SECTION  Section Start============================
    public function breadcrumbView($slug) {
        $page_title = __('Breadcrumb Section');
        $section_slug = Str::slug(SiteSectionConst::BREADCRUMB_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.breadcrumb-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function breadcrumbUpdate(Request $request,$slug) {
        $slug = Str::slug(SiteSectionConst::BREADCRUMB_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        $data['images']['banner_image'] = $section->value->images->banner_image ?? "";
        if($request->hasFile("banner_image")) {
            $data['images']['banner_image']      = $this->imageValidate($request,"banner_image",$section->value->images->banner_image ?? null);
        }
        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }
    //=======================breadcrumb SECTION Section End==============================

    /**
     * Mehtod for show banner section page
     * @param string $slug
     * @return view
     */
    public function bannerView($slug) {
        $page_title = __('Home Banner Section');
        $section_slug = Str::slug(SiteSectionConst::HOME_BANNER);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.home-banner',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Mehtod for update banner section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function bannerUpdate(Request $request,$slug) {
        $basic_field_name = ['heading' => "required|string|max:100",'sub_heading' => "required|string|max:255",'button_name' => "required|string|max:50",'button_link' => "required|string|max:255"];

        $slug = Str::slug(SiteSectionConst::HOME_BANNER);
        $section = SiteSections::where("key",$slug)->first();


        $data['images']['banner_image'] = $section->value->images->banner_image ?? "";
        if($request->hasFile("banner_image")) {
            $data['images']['banner_image']      = $this->imageValidate($request,"banner_image",$section->value->images->banner_image ?? null);
        }



        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


    // ====================================  Service Section ===============================

    /**
     * Mehtod for show solutions section page
     * @param string $slug
     * @return view
     */
    public function serviceView($slug) {
        $page_title = __("Service Section");
        $section_slug = Str::slug(SiteSectionConst::SERVICE_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.service-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }


    public function serviceStore(Request $request,$slug) {
        $basic_field_name = [
            'service_icon' => "required|string|max:100",
            'heading' => "required|string|max:300",
            'description' => "required|string|max:500",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"about-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::SERVICE_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('About item added successfully!')]]);
    }


    public function serviceUpdate(Request $request,$slug) {

        $basic_field_name = [
            'service_icon_edit' => "required|string|max:100",
            'heading_edit' => "required|string|max:300",
            'description_edit' => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::SERVICE_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => ['Team item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Team item is invalid!']]);

        // dd($request->all(), $basic_field_name);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "testimonial-edit");
        // dd($language_wise_data);
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);


        // $section_values['items'][$request->target]['social_links']    = $social_links;
        $section_values['items'][$request->target]['language'] = $language_wise_data;
        if ($request->hasFile("image")) {
            $section_values['items'][$request->target]['image']    = $this->imageValidate($request, "image", $section_values['items'][$request->target]['image'] ?? null);
        }

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Information updated successfully!')]]);

    }

    public function serviceDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::SERVICE_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);
        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return  $e->getMessage();
        }

        return back()->with(['success' => ['About item delete successfully!']]);
    }


    // =========================  Overview Left Section ======================

    /**
     * Mehtod for show solutions section page
     * @param string $slug
     * @return view
     */
    public function overviewLeftView($slug) {
        $page_title = __("Overview Left Section");
        $section_slug = Str::slug(SiteSectionConst::OVERVIEW_LEFT_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.service-left-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    public function overviewLeftUpdate(Request $request,$slug) {

        $basic_field_name = [
            'title' => "required|string|max:300",
        ];

        $slug = Str::slug(SiteSectionConst::OVERVIEW_LEFT_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


    public function overviewLeftItemCreate() {
        $page_title = __("Create Overview");
        $languages = $this->languages;
        return view('admin.sections.overview-left-section.store',compact('page_title','languages'));
    }


    public function overviewLeftItemStore(Request $request) {

        $basic_field_name = [
            'heading' => "required|string|max:500",
            'details' => "required|string|max:1000",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"about-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::OVERVIEW_LEFT_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('item added successfully!')]]);
    }


    public function overviewLeftItemEdit($id) {
        $page_title = __('Edit Overview');
        $slug = Str::slug(SiteSectionConst::OVERVIEW_LEFT_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Over item not found!']]);
        $section_language =  $section_values['items'][$id];
        $languages = $this->languages;

        return  view('admin.sections.overview-left-section.edit',compact('page_title','section_language','id','languages'));
    }


    public function overviewLeftItemUpadte(Request $request) {
        // dd($request->all());
        $basic_field_name = [
            'heading' => "required|string|max:255",
            // 'title_edit'   => "required|string|max:300",
            'details' => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::OVERVIEW_LEFT_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => ['Team item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Team item is invalid!']]);
        $language_wise_data = $this->contentValidate($request, $basic_field_name, "about-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        // $language_wise_data = array_map(function ($language) {
        //     return replace_array_key($language, "_edit");
        // }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Information updated successfully!')]]);

    }

    public function overviewLeftItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::OVERVIEW_LEFT_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);
        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return  $e->getMessage();
        }

        return back()->with(['success' => ['item delete successfully!']]);
    }


    // ========================= End Overview Left Section ======================



      // =========================  Overview right Section ======================

    /**
     * Mehtod for show solutions section page
     * @param string $slug
     * @return view
     */
    public function overviewRightView($slug) {
        $page_title = __("Overview Right Section");
        $section_slug = Str::slug(SiteSectionConst::OVERVIEW_RIGHT_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.service-right-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

       /**
     * Mehtod for update solutions section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function overviewRightUpdate(Request $request,$slug) {

        $basic_field_name = [
            'title' => "required|string|max:300",
        ];

        $slug = Str::slug(SiteSectionConst::OVERVIEW_RIGHT_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }



    public function overviewRightItemStore(Request $request,$slug) {
        $request->validate([
            'image' => "required|image|mimes:png,jpg,webp,jpeg,svg",
        ]);
        $basic_field_name = [
            'heading' => "required|string|max:255",
            'details' => "required|string|max:1000",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"about-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::OVERVIEW_RIGHT_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;

        $section_data['items'][$unique_id]['image'] = "";
        if($request->hasFile("image")) {
            $section_data['items'][$unique_id]['image'] = $this->imageValidate($request,"image",$section->value->items->image ?? null);
        }

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('item added successfully!')]]);
    }


    public function overviewRightItemUpdate(Request $request,$slug) {

        $basic_field_name = [
            'heading_edit' => "required|string|max:255",
            'details_edit' => "required|string|max:1000",
        ];

        $slug = Str::slug(SiteSectionConst::OVERVIEW_RIGHT_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => ['Team item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Team item is invalid!']]);
        $language_wise_data = $this->contentValidate($request, $basic_field_name, "about-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;

        $request->merge(['old_image' => $section_values['items'][$request->target]['image'] ?? null]);
        $section_values['items'][$request->target]['image'] = $section_values['items'][$request->target]['image'];
        if($request->hasFile("image")) {
            $section_values['items'][$request->target]['image']    = $this->imageValidate($request,"image",$section_values['items'][$request->target]['image'] ?? null);
        }
        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Information updated successfully!')]]);

    }

    public function overviewRightItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::OVERVIEW_RIGHT_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);
        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return  $e->getMessage();
        }

        return back()->with(['success' => ['item delete successfully!']]);
    }


    // ========================= End Overview Right Section ======================




    // ==========================  Subscribe section ======================


     /**
     * Mehtod for show solutions section page
     * @param string $slug
     * @return view
     */
    public function subscribeView($slug) {
        $page_title = __("Subscribe Section");
        $section_slug = Str::slug(SiteSectionConst::SUBSCRIBE_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.subscribe-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }




    public function subscribeUpdate(Request $request,$slug) {
        $basic_field_name = [
            'title'     => "required|string|max:100",
            'sub_title' => "required|string|max:300",
            'details'   => "required|string|max:500",
        ];

        $slug = Str::slug(SiteSectionConst::SUBSCRIBE_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }
        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


     // ====================================  End Subscribe Section ===============================

    // ========================== Start Statistics section ======================


     /**
     * Mehtod for show solutions section page
     * @param string $slug
     * @return view
     */
    public function statisticsView($slug) {
        $page_title = __("Statistics Section");
        $section_slug = Str::slug(SiteSectionConst::STATISTICS_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.statistics-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }



    public function statisticsUpdate(Request $request,$slug) {

        $basic_field_name = [
            'volunteers_icon' => "required|string|max:100",
            'volunteers'      => "required|string|max:100",
            'donations_icon'  => "required|string|max:100",
            'donations'       => "required|string|max:100",
            'followers_icon'  => "required|string|max:100",
            'followers'       => "required|string|max:100",
            'likes_icon'      => "required|string|max:100",
            'likes'           => "required|string|max:100",
        ];

        $slug = Str::slug(SiteSectionConst::STATISTICS_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }
        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }


     // ====================================  End Statistics Section ===============================





    /**
     * Mehtod for show solutions section page
     * @param string $slug
     * @return view
     */
    public function aboutView($slug) {
        $page_title = __("About Section");
        $section_slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.about-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Mehtod for update solutions section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutUpdate(Request $request,$slug) {
        $basic_field_name = [
            // 'fitst_section_title' => "required|string|max:100",
            'fitst_section_heading' => "required|string|max:100",
            'first_section_sub_heading' => "required|string",
            'first_section_button_name' => "required|string|max:100",
            'first_section_button_link' => "required|string|max:255",
            'first_section_button_link' => "required|string|max:255",
            'second_section_title' => "required|string|max:100",
            'second_section_heading' => "required|string|max:100",
            'second_section_sub_heading' => "required|string|max:255",
            'second_section_video_link' => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }
        $data['images']['first_section_image'] = $section->value->images->first_section_image ?? "";

        if($request->hasFile("first_section_image")) {
            $data['images']['first_section_image']      = $this->imageValidate($request,"first_section_image",$section->value->images->first_section_image ?? null);
        }

        $data['images']['second_section_image'] = $section->value->images->second_section_image ?? "";
        if($request->hasFile("second_section_image")) {
            $data['images']['second_section_image']      = $this->imageValidate($request,"second_section_image",$section->value->images->second_section_image ?? null);
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }

    /**
     * Mehtod for store solution item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutItemStore(Request $request,$slug) {
        $basic_field_name = [
            'title'     => "required|string|max:255",
            'link'     => "required|string|max:255"
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"about-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('About item added successfully!')]]);
    }

    /**
     * Mehtod for update about item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutItemUpdate(Request $request,$slug) {
     
        $request->validate([
            'target'    => "required|string",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:300",
            'link_edit'     => "required|string|max:255"
        ];

        $slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);


        $language_wise_data = $this->contentValidate($request,$basic_field_name,"about-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Information updated successfully!')]]);
    }

    /**
     * Mehtod for delete about item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);
        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return  $e->getMessage();
        }

        return back()->with(['success' => ['About item delete successfully!']]);
    }
    //=======================About  Section End===================================
    //=======================Download App Section Start============================
    public function downloadAppView($slug) {
        $page_title = __('Download App Section');
        $section_slug = Str::slug(SiteSectionConst::DOWNLOAD_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.download-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function downloadAppUpdate(Request $request,$slug) {
        $basic_field_name = [
            // 'title' => "required|string|max:100",
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::DOWNLOAD_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        // $data['images']['home_image'] = $section->value->images->home_image ?? "";
        // if($request->hasFile("home_image")) {
        //     $data['images']['home_image']      = $this->imageValidate($request,"home_image",$section->value->images->home_image ?? null);
        // }
        $data['images']['play_store_qr_image'] = $section->value->images->play_store_qr_image ?? "";
        if($request->hasFile("play_store_qr_image")) {
            $data['images']['play_store_qr_image']      = $this->imageValidate($request,"play_store_qr_image",$section->value->images->play_store_qr_image ?? null);
        }
        $data['images']['app_store_qr_image'] = $section->value->images->app_store_qr_image ?? "";
        if($request->hasFile("app_store_qr_image")) {
            $data['images']['app_store_qr_image']      = $this->imageValidate($request,"app_store_qr_image",$section->value->images->app_store_qr_image ?? null);
        }
        $data['images']['google_play'] = $section->value->images->google_play ?? "";
        if($request->hasFile("google_play")) {
            $data['images']['google_play']      = $this->imageValidate($request,"google_play",$section->value->images->google_play ?? null);
        }
        $data['images']['app_store'] = $section->value->images->app_store ?? "";
        if($request->hasFile("app_store")) {
            $data['images']['app_store']      = $this->imageValidate($request,"app_store",$section->value->images->app_store ?? null);
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }
    //=======================Download App Section End=========================

    //=======================Gallery Section End==============================
    public function galleryView($slug) {
        $page_title = __("Gallery Section");
        $section_slug = Str::slug(SiteSectionConst::GALLERY_SCTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.gallery-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function  galleryItemStore(Request $request,$slug) {
        $request->validate([
            'image' => "required|image|mimes:png,jpg,webp,jpeg,svg",
        ]);
        $basic_field_name = [
            'title' => "required|string|max:100",
            'tag'   => "required|string|max:30",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"gallery-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug = Str::slug(SiteSectionConst::GALLERY_SCTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['image'] = "";
        if($request->hasFile("image")) {
            $section_data['items'][$unique_id]['image'] = $this->imageValidate($request,"image",$section->value->items->image ?? null);
        }
        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }
    public function galleryItemUpdate(Request $request,$slug) {
        $request->validate([
            'target'    => "required|string",
            'image' => "nullable|image|mimes:png,jpg,webp,jpeg,svg"
        ]);

        $basic_field_name = [
            'title' => "required|string|max:100",
            'tag'   => "required|string|max:30",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"partner-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug = Str::slug(SiteSectionConst::GALLERY_SCTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);
        $request->merge(['old_image' => $section_values['items'][$request->target]['image'] ?? null]);
        $section_values['items'][$request->target]['image'] = $section_values['items'][$request->target]['image'];
        if($request->hasFile("image")) {
            $section_values['items'][$request->target]['image']    = $this->imageValidate($request,"image",$section_values['items'][$request->target]['image'] ?? null);
        }

        $section_values['items'][$request->target]['language'] = $language_wise_data;

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Information updated successfully!')]]);
    }
    public function galleryItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::GALLERY_SCTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try{
            $image_link = get_files_path('site-section') . '/' . $section_values['items'][$request->target]['image'];
            unset($section_values['items'][$request->target]);
            delete_file($image_link);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item delete successfully!')]]);
    }

    //=======================Gallery Section End==============================
    //=======================Top Partnet Section Start=============================
    public function partnerView($slug) {
        $page_title = __("Top Partner Section");
        $section_slug = Str::slug(SiteSectionConst::TOP_PARTNER);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.partner-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Mehtod for update solutions section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function partnerUpdate(Request $request,$slug) {
        $basic_field_name = ['heading' => "required|string|max:100"];
        $slug = Str::slug(SiteSectionConst::TOP_PARTNER);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $section_data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;
        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Partner Section updated successfully!')]]);
    }

    /**
     * Mehtod for store solution item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function partnerItemStore(Request $request,$slug) {
        $request->validate([
            'image' => "required|image|mimes:png,jpg,webp,jpeg,svg"
        ]);


        $slug = Str::slug(SiteSectionConst::TOP_PARTNER);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['image'] = "";
        if($request->hasFile("image")) {
            $section_data['items'][$unique_id]['image'] = $this->imageValidate($request,"image",$section->value->items->image ?? null);
        }
        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }

    /**
     * Mehtod for update solution item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function partnerItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'    => "required|string",
            'image' => "required|image|mimes:png,jpg,webp,jpeg,svg"
        ]);

        $slug = Str::slug(SiteSectionConst::TOP_PARTNER);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);
        $request->merge(['old_image' => $section_values['items'][$request->target]['image'] ?? null]);
        if($request->hasFile("image")) {
            $section_values['items'][$request->target]['image']    = $this->imageValidate($request,"image",$section_values['items'][$request->target]['image'] ?? null);
        }

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Information updated successfully!')]]);
    }

    /**
     * Mehtod for delete solution item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function partnerItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::TOP_PARTNER);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try{
            $image_link = get_files_path('site-section') . '/' . $section_values['items'][$request->target]['image'];
            unset($section_values['items'][$request->target]);
            delete_file($image_link);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item delete successfully!')]]);
    }
    //=======================Top Partner Section End===============================
    //=======================testimonial Section End===============================

    public function testimonialView($slug) {
        $page_title = __("Testimonial Section");
        $section_slug = Str::slug(SiteSectionConst::TESTIMONIAL_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.testimonial-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function testimonialUpdate(Request $request,$slug) {
        $basic_field_name = [
            // 'title' => "required|string|max:100",
            'heading' => "required|string|max:100"
        ];

        $slug = Str::slug(SiteSectionConst::TESTIMONIAL_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }

        // $data['images']['small_image_one'] = $section->value->images->small_image_one ?? "";
        // if($request->hasFile("small_image_one")) {
        //     $data['images']['small_image_one']      = $this->imageValidate($request,"small_image_one",$section->value->images->small_image_one ?? null);
        // }
        // $data['images']['small_image_two'] = $section->value->images->small_image_two ?? "";
        // if($request->hasFile("small_image_two")) {
        //     $data['images']['small_image_two']      = $this->imageValidate($request,"small_image_two",$section->value->images->small_image_two ?? null);
        // }
        // $data['images']['small_image_three'] = $section->value->images->small_image_three ?? "";
        // if($request->hasFile("small_image_three")) {
        //     $data['images']['small_image_three']      = $this->imageValidate($request,"small_image_three",$section->value->images->small_image_three ?? null);
        // }
        // $data['images']['small_image_four'] = $section->value->images->small_image_four ?? "";
        // if($request->hasFile("small_image_four")) {
        //     $data['images']['small_image_four']      = $this->imageValidate($request,"small_image_four",$section->value->images->small_image_four ?? null);
        // }

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }
    public function testimonialItemStore(Request $request,$slug) {
        $basic_field_name = [
            'name'     => "required|string|max:100",
            'details'   => "required|string|max:255",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"testimonial-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::TESTIMONIAL_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['image'] = "";

        if($request->hasFile("image")) {
            $section_data['items'][$unique_id]['image'] = $this->imageValidate($request,"image",$section->value->items->image ?? null);
        }

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }
    public function testimonialItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'    => "required|string",
        ]);

        $basic_field_name = [
            'name_edit'     => "required|string|max:100",
            'details_edit'   => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::TESTIMONIAL_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $request->merge(['old_image' => $section_values['items'][$request->target]['image'] ?? null]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"testimonial-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;

        if($request->hasFile("image")) {
            $section_values['items'][$request->target]['image']    = $this->imageValidate($request,"image",$section_values['items'][$request->target]['image'] ?? null);
        }

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Information updated successfully!')]]);
    }

    public function testimonialItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::TESTIMONIAL_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try{
            $image_link = get_files_path('site-section') . '/' . $section_values['items'][$request->target]['image'];
            unset($section_values['items'][$request->target]);
            delete_file($image_link);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item delete successfully!')]]);
    }

    //=======================testimonial Section End===============================
    public function contactView($slug) {
        $page_title = __('Contact Section');
        $section_slug = Str::slug(SiteSectionConst::CONTACT_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.contact-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function contactUpdate(Request $request,$slug) {
        $basic_field_name = [
            'title' => "required|string|max:100",
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string",
            'location'  => "required|string|max:255",
            'phone'  => "required|string|max:13",
            'office_hours'  => "required|string|max:255",
            'email'  => "required|string|max:100",

        ];

        $slug = Str::slug(SiteSectionConst::CONTACT_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }
    //=======================Download App Section End==============================

      //=======================footer Section End===============================

    public function  footerView($slug) {
        $page_title = __("Footer Section");
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $data = SiteSections::getData($section_slug)->first();

        $languages = $this->languages;

        return view('admin.sections.setup-sections.footer-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function  footerUpdate(Request $request,$slug) {
        $basic_field_name = [
            'footer_text' => "required|string|max:100",
            'details' => "required|string",
            'newsltter_details' => "required|string"
        ];

        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }
        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section updated successfully!')]]);
    }
    public function  footerItemStore(Request $request,$slug) {
        $basic_field_name = [
            'name'     => "required|string|max:100",
            'social_icon'   => "required|string|max:255",
            'link'   => "required|string|url|max:255",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"icon-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item added successfully!')]]);
    }
    public function  footerItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'    => "required|string",
        ]);

        $basic_field_name = [
            'name_edit'     => "required|string|max:100",
            'social_icon_edit'   => "required|string|max:255",
            'link_edit'   => "required|string|url|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"icon-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Information updated successfully!')]]);
    }

    public function footerItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => [__('Section item not found!')]]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Section item delete successfully!')]]);
    }

    //=======================footer Section End===============================


    //=======================Category  Section Start=======================
        public function categoryView(){
            $page_title = __("Setup Category Type");
            $allCategory = CategoryType::orderByDesc('id')->paginate(10);
            $languages = Language::get();
            return view('admin.sections.categoryType.index',compact(
                'page_title',
                'allCategory',
                'languages'
            ));
        }
        public function storeCategory(Request $request){
            $basic_field_name = [
                'name'          => "required|string|max:150",
            ];

            $data['language']  = $this->contentValidate($request,$basic_field_name);
            if($request->type == 1){
                $slugData = "faq-".Str::slug($data['language']['en']['name']);
            }elseif($request->type == 2){
                $slugData = "event-".Str::slug($data['language']['en']['name']);
            }

            $makeUnique = CategoryType::where('slug',  $slugData)->first();
            if($makeUnique){
                return back()->with(['error' => [ $data['language']['en']['name'].' '.'Category Already Exists!']]);
            }
            $validated['name']       = $data['language']['en']['name'];
            $validated['slug']       = $slugData;
            $validated['data']       = $data;
            $validated['type']       = $request->type;
            try{
                CategoryType::create($validated);
                return back()->with(['success' => [__('Category Saved Successfully!')]]);
            }catch(Exception $e) {
                return back()->with(['error' => [__('Something went wrong! Please try again')]]);
            }


        }



        public function categoryUpdate(Request $request){

            $target = $request->target;
            $category = CategoryType::where('id',$target)->first();
            $basic_field_name = [
                'name_edit'          => "required|string|max:150",
            ];
            $language_wise_data = $this->contentValidate($request,$basic_field_name,"category-update");
            if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

            $language_wise_data = array_map(function($language) {
                return replace_array_key($language,"_edit");
            },$language_wise_data);



            $data['language']  = $language_wise_data;

            if($request->type == 1){
                $slugData = "faq-".Str::slug($data['language']['en']['name']);
            }elseif($request->type == 2){
                $slugData = "event-".Str::slug($data['language']['en']['name']);
            }

            $categoryType = CategoryType::where('slug',$slugData)->first();
            if ($categoryType) {
                return back()->with(['error' => [__('Duplicate entry, enter unique key')]]);
            }

            $validated['name']       = $data['language']['en']['name'];
            $validated['slug']       = $slugData;
            $validated['data']       = $data;
            $validated['type']       = $request->type;
            try{
                $category->fill($validated)->save();
                return back()->with(['success' => [__('Category Updated Successfully!')]]);
            }catch(Exception $e) {

                return back()->with(['error' => [__('Something went wrong! Please try again')]]);
            }



        }



        public function categoryStatusUpdate(Request $request) {
            $validator = Validator::make($request->all(),[
                'status'                    => 'required|boolean',
                'data_target'               => 'required|string',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                $error = ['error' => $validator->errors()];
                return Response::error($error,null,400);
            }
            $validated = $validator->safe()->all();
            $category_id = $validated['data_target'];

            $category = CategoryType::where('id',$category_id)->first();
            if(!$category) {
                $error = ['error' => ['Category record not found in our system.']];
                return Response::error($error,null,404);
            }

            try{
                $category->update([
                    'status' => ($validated['status'] == true) ? false : true,
                ]);
            }catch(Exception $e) {
                $error = ['error' => [__('Something went wrong! Please try again')]];
                return Response::error($error,null,500);
            }

            $success = ['success' => [__('Category status updated successfully!')]];
            return Response::success($success,null,200);
        }
        public function categoryDelete(Request $request) {
            $validator = Validator::make($request->all(),[
                'target'        => 'required|string|exists:category_types,id',
            ]);
            $validated = $validator->validate();
            $category = CategoryType::where("id",$validated['target'])->first();
            if($category->type == 1){
                $type = "FAQ"??"";

            }else{
                $type = "Event"??"";
            }

            try{
                $category->delete();
            }catch(Exception $e) {
                return back()->with(['error' => [__('Something went wrong! Please try again')]]);
            }

            return back()->with(['success' => [ $type.' '.'Category deleted successfully!']]);
        }
        public function categorySearch(Request $request) {
            $validator = Validator::make($request->all(),[
                'text'  => 'required|string',
            ]);

            if($validator->fails()) {
                $error = ['error' => $validator->errors()];
                return Response::error($error,null,400);
            }

            $validated = $validator->validate();
            $allCategory = CategoryType::search($validated['text'])->select()->limit(10)->get();
            return view('admin.components.search.category-search',compact(
                'allCategory',
            ));
        }
    //=======================Category  Section End=======================
    //=======================Faq  Section Start=======================
    public function faqView(){
        $page_title = __("Setup FAQ");
        $allCategory = CategoryType::where('type',1)->orderByDesc('id')->get();
        $allFaq = FaqSection::orderByDesc('id')->get();
        return view('admin.sections.faq.index',compact(
            'page_title',
            'allCategory',
            'allFaq'
        ));
    }
    public function faqStore(Request $request){
        $validator = Validator::make($request->all(),[
            'category_id'      => 'required',
            'question'   => 'required|string|max:255',
            'answer'   => 'required|string',
        ]);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','faq-add');
        }
        $validated = $validator->validate();

        $validated['category_id']       = $request->category_id;
        $validated['question']          = $request->question;
        $validated['answer']            = $request->answer;
        try{
            FaqSection::create($validated);
            return back()->with(['success' => [__('Faq Saved Successfully!')]]);
        }catch(Exception $e) {
            return back()->withErrors($validator)->withInput()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
    }
    public function faqUpdate(Request $request){

        $target = $request->target;
        $faq = FaqSection::where('id',$target)->first();
        $validator = Validator::make($request->all(),[
            'category_id'      => 'required',
            'question'   => 'required|string|max:255',
            'answer'   => 'required|string',
        ]);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','edit-faq');
        }
        $validated = $validator->validate();

        $validated['category_id']       = $request->category_id;
        $validated['question']          = $request->question;
        $validated['answer']            = $request->answer;
        try{
            $faq->fill($validated)->save();
            return back()->with(['success' => ['Faq Updated Successfully!']]);
        }catch(Exception $e) {
            return back()->withErrors($validator)->withInput()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
    }
    public function faqStatusUpdate(Request $request) {
        $validator = Validator::make($request->all(),[
            'status'                    => 'required|boolean',
            'data_target'               => 'required|string',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            $error = ['error' => $validator->errors()];
            return Response::error($error,null,400);
        }
        $validated = $validator->safe()->all();
        $faq_id = $validated['data_target'];

        $faq = FaqSection::where('id',$faq_id)->first();
        if(!$faq) {
            $error = ['error' => ['Faq record not found in our system.']];
            return Response::error($error,null,404);
        }

        try{
            $faq->update([
                'status' => ($validated['status'] == true) ? false : true,
            ]);
        }catch(Exception $e) {
            $error = ['error' => [__('Something went wrong! Please try again')]];
            return Response::error($error,null,500);
        }

        $success = ['success' => [__('Faq status updated successfully!')]];
        return Response::success($success,null,200);
    }
    public function faqDelete(Request $request) {
        $validator = Validator::make($request->all(),[
            'target'        => 'required|string|exists:faq_sections,id',
        ]);
        $validated = $validator->validate();
        $faq = FaqSection::where("id",$validated['target'])->first();

        try{
            $faq->delete();
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Faq deleted successfully!')]]);
    }


//=======================Faq Section End=======================


    /**
     * Method for get languages form record with little modification for using only this class
     * @return array $languages
     */
    public function languages() {
        $languages = Language::whereNot('code',LanguageConst::NOT_REMOVABLE)->select("code","name")->get()->toArray();
        $languages[] = [
            'name'      => LanguageConst::NOT_REMOVABLE_CODE,
            'code'      => LanguageConst::NOT_REMOVABLE,
        ];
        return $languages;
    }

    /**
     * Method for validate request data and re-decorate language wise data
     * @param object $request
     * @param array $basic_field_name
     * @return array $language_wise_data
     */
    public function contentValidate($request,$basic_field_name,$modal = null) {
        $languages = $this->languages();

        $current_local = get_default_language_code();
        $validation_rules = [];
        $language_wise_data = [];
        foreach($request->all() as $input_name => $input_value) {
            foreach($languages as $language) {
                $input_name_check = explode("_",$input_name);
                $input_lang_code = array_shift($input_name_check);
                $input_name_check = implode("_",$input_name_check);
                if($input_lang_code == $language['code']) {
                    if(array_key_exists($input_name_check,$basic_field_name)) {
                        $langCode = $language['code'];
                        if($current_local == $langCode) {
                            $validation_rules[$input_name] = $basic_field_name[$input_name_check];
                        }else {
                            $validation_rules[$input_name] = str_replace("required","nullable",$basic_field_name[$input_name_check]);
                        }
                        $language_wise_data[$langCode][$input_name_check] = $input_value;
                    }
                    break;
                }
            }
        }
        if($modal == null) {
            $validated = Validator::make($request->all(),$validation_rules)->validate();
        }else {
            $validator = Validator::make($request->all(),$validation_rules);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with("modal",$modal);
            }
            $validated = $validator->validate();
        }

        return $language_wise_data;
    }

    /**
     * Method for validate request image if have
     * @param object $request
     * @param string $input_name
     * @param string $old_image
     * @return boolean|string $upload
     */
    public function imageValidate($request,$input_name,$old_image) {
        if($request->hasFile($input_name)) {
            $image_validated = Validator::make($request->only($input_name),[
                $input_name         => "image|mimes:png,jpg,webp,jpeg,svg",
            ])->validate();

            $image = get_files_from_fileholder($request,$input_name);
            $upload = upload_files_from_path_dynamic($image,'site-section',$old_image);
            return $upload;
        }

        return false;
    }

}
