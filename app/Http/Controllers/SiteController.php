<?php

namespace App\Http\Controllers;


use Exception;
use App\Models\Contact;
use App\Models\Campaign;
use App\Models\FaqSection;
use App\Models\Subscriber;
use App\Models\Admin\Event;
use Illuminate\Support\Str;
use App\Models\CategoryType;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use App\Http\Helpers\Response;
use App\Models\Admin\Language;
use App\Models\Admin\SetupPage;
use App\Constants\LanguageConst;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Helpers\Api\PaymentGatewayApi;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Providers\Admin\BasicSettingsProvider;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Http\Helpers\PaymentGateway as PaymentGatewayHelper;

class SiteController extends Controller
{
    public function home(){

        $basic_settings = BasicSettingsProvider::get();
        $page_title = $basic_settings->site_title ?? "Home";
        $section_slug = Str::slug(SiteSectionConst::HOME_BANNER);
        $homeBanner = SiteSections::getData( $section_slug)->first();
        $campaigns = Campaign::getData(1)->orderBy('id','desc')->take(3)->get();
        $recent_events = Event::with('category:id,name')->where('status', 1)->orderBy('id', 'desc')->limit(2)->get();
        $section_slug = Str::slug(SiteSectionConst::EVENT_SECTION);
        $event_head_data = SiteSections::getData($section_slug)->first();
        $gallety_slug = Str::slug(SiteSectionConst::GALLERY_SCTION);
        $gallery = SiteSections::getData($gallety_slug)->first();
        return view('frontend.index',compact(
            'page_title',
            'homeBanner',
            'campaigns',
            'recent_events',
            'event_head_data',
            'gallery',
        ));

    }
    public function about(){
        $page_title = __('About');
        $sub_page_title = __("About Kaifala Marah");
        $section_slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $about = SiteSections::getData( $section_slug)->first();
        return view('frontend.about',compact('page_title','sub_page_title','about'));
    }

    public function video(){
        $page_title = __('Video');
        $sub_page_title = "Video";
        $section_slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $about = SiteSections::getData( $section_slug)->first();
        return view('frontend.video',compact('page_title','sub_page_title','about'));
    }
    public function donation(){
        $page_title = __('Donation');
        $sub_page_title = "Donation Listing";
        $campaigns = Campaign::getData(1)->orderBy('id', 'desc')->where('status', 1)->get();
        return view('frontend.campaign',compact('page_title','sub_page_title','campaigns'));
    }
    public function campaignDetails($id,$slug){
        $page_title = __('Donation Details');
        $sub_page_title = "Donation";
        $campaign_id = $id;
        $campaign_slug = $slug;

        $campaign = Campaign::findOrFail($id);
        $payment_gateways_currencies = PaymentGatewayCurrency::whereHas('gateway', function ($gateway) {
            $gateway->where('slug', PaymentGatewayConst::add_money_slug());
            $gateway->where('status', 1);
        })->get();

        // dd($payment_gateways_currencies);

        return view('frontend.campaignDeatils',compact(
            'page_title',
            'sub_page_title',
            'campaign',
            'payment_gateways_currencies',
            'campaign_id',
            'campaign_slug',
        ));
    }
    public function gallery(){
        $page_title = __('Gallery');
        $sub_page_title = __("Our Gallery");
        return view('frontend.gallery',compact('page_title','sub_page_title'));
    }
    public function events(){
        $page_title = __('Events');
        $sub_page_title = "Recent Events";
        $recent_events = Event::with('category:id,name')->where('status', 1)->orderBy('id', 'desc')->limit(3)->get();
        $events = Event::with('category')->where('status', 1)->paginate();
        $categories = CategoryType::active()->with('events')->where('type', 2)->orderBy('id','desc')->get();
        return view('frontend.events',compact(
            'page_title',
            'sub_page_title',
            'events',
            'recent_events',
            'categories'
        ));
    }
    public function eventsDetails($id,$slug){
        $page_title = __('Event Details');
        $sub_page_title = "Event";
        $recent_events = Event::with('category:id,name')->where('status', 1)->orderBy('id', 'desc')->limit(3)->get();
        $categories = CategoryType::with('events')->where('type', 2)->orderBy('id','desc')->get();
        $event = Event::with('category')->findOrFail($id);
        return view('frontend.eventsDeatils',compact(
            'page_title',
            'sub_page_title',
            'event',
            'recent_events',
            'categories'
        ));
    }

    public function contact(){
        $page_title = __('Contact');
        $sub_page_title = "Get In Touch";
        return view('frontend.contact',compact('page_title','sub_page_title'));
    }
    public function downloadApp(){
        $page_title = __('Download App');
        $sub_page_title = "Download App";
        return view('frontend.downloadApp',compact('page_title','sub_page_title'));
    }
    public function pageView($slug){
        $defualt = get_default_language_code()??'en';

        $page = SetupPage::where('slug', $slug)->where('status', 1)->first();

        if(empty($page)){
            abort(404);
        }

        $page_title = $page->title->language->$defualt->title;
        $sub_page_title = $page->title->language->$defualt->title;

        return view('frontend.page',compact('page_title','sub_page_title', 'page'));
    }
    public function faq(){
        $page_title = __('FAQ');
        $sub_page_title = "Ask Question";
        $allFaq = FaqSection::where('status',1)->orderBy('id',"DESC")->get();
        $faqCategories = CategoryType::where('type',1)->where('status', 1)->orderBy('id',"ASC")->get();
        // $numberOfFaqByCat = FaqSection::where('status',1)->orderBy('id',"DESC")->get()->map(function($faq){
        //     $categories = CategoryType::where('id',$faq->category_id)->where('type',1)->orderBy('id',"ASC")->get();
        //     return  $categories

        // });


        return view('frontend.faq',compact('page_title','sub_page_title','allFaq','faqCategories'));
    }

    /**
     * This method for store subscriber
     * @method POST
     * @return Illuminate\Http\Request Response
     * @param Illuminate\Http\Request $request
     */
    public function subscriber(Request $request){
        if($request->ajax()){
            $validator = Validator::make($request->all(),[
                'email' => 'email|unique:subscribers,email'
            ]);

            if($validator->stopOnFirstFailure()->fails()){
                $error = ['errors' => $validator->errors()];
                return Response::error($error, null, 404);
            }

            $validated = $validator->safe()->all();

            try{
                Subscriber::create($validated);
            }catch(Exception $e) {
                $error = ['error' => [__('Something went wrong! Please try again')]];
                return Response::error($error,null,500);
            }

            $success = ['success' => [__('Your email added to our newsletter')]];
            return Response::success($success,null,200);
        }
    }

    /**
     * This method for store subscriber
     * @method POST
     * @return Illuminate\Http\Request Response
     * @param Illuminate\Http\Request $request
     */
    public function contactStore(Request $request){
        if($request->ajax()){
            $validator = Validator::make($request->all(), [
                'name'    => 'required|string',
                'email'   => 'required|email',
                'mobile'  => 'required',
                'subject' => 'required|string',
                'message' => 'required|string',
            ]);

            if($validator->stopOnFirstFailure()->fails()){
                $error = ['errors' => $validator->errors()];
                return Response::error($error, null, 500);
            }

            $validated = $validator->safe()->all();

            try {
                Contact::create($validated);
            } catch (\Exception $th) {
                $error = ['error' => __('Something went wrong! Please try again')];
                return Response::error($error, null, 500);
            }

            $success = ['success' => [__('Your message submitted')]];
            return Response::success($success,null,200);
        }
    }

    public function cookieAccept(){
        session()->put('cookie_accepted',true);
        return response()->json(__('Cookie allow successfully'));
    }
    public function cookieDecline(){
        session()->put('cookie_decline',true);
        return response()->json(__('Cookie decline successfully'));
    }

    public function languageSwitch(Request $request) {
        $code = $request->target;
        $language = Language::where("code",$code);

        if(!$language->exists()) {
            return back()->with(['error' => [__('Oops! Language not found')]]);
        }

        Session::put('local',$code);

        return back()->with(['success' => [__('Language switch successfully')]]);
    }


    public function pagaditoSuccess(){
        $request_data = request()->all();
        //if payment is successful
        $token = $request_data['param1'];
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::PAGADITO)->where("identifier",$token)->first();
        if($checkTempData->data->env_type == 'web'){
            if($checkTempData->data->payment_type == PaymentGatewayConst::TYPEADDMONEY){
                if(!$checkTempData) return redirect()->route('user.add.money.index')->with(['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]]);
                $checkTempData = $checkTempData->toArray();
                try{
                    PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEADDMONEY)->responseReceive('pagadito');
                }catch(Exception $e) {
                    return back()->with(['error' => [$e->getMessage()]]);
                }
                return redirect()->route("user.add.money.index")->with(['success' => [__('Successfully added money')]]);
            }else{
                if(!$checkTempData) return redirect()->route('campaign')->with(['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]]);
                $checkTempData = $checkTempData->toArray();
                try{
                    PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('pagadito');
                }catch(Exception $e) {
                    return back()->with(['error' => [$e->getMessage()]]);
                }

                $campaign = Campaign::find($checkTempData['data']->campaign_id);
                return redirect()->route('campaign.details', [$campaign->id, $campaign->slug])->with(['success' => [__('Successfully donation')]]);
            }

        }elseif($checkTempData->data->env_type == 'api'){
            if(!$checkTempData) {
                $message = ['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]];
                return ApiResponse::error($message);
            }
            $checkTempData = $checkTempData->toArray();
            if($checkTempData['data']->payment_type == PaymentGatewayConst::TYPEADDMONEY){
                try{
                    PaymentGatewayApi::init($checkTempData)->type(PaymentGatewayConst::TYPEADDMONEY)->responseReceive('pagadito');
                }catch(Exception $e) {
                    $message = ['error' => [$e->getMessage()]];
                    ApiResponse::error($message);
                }
            }else{
                try{
                    PaymentGatewayApi::init($checkTempData)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('pagadito');
                }catch(Exception $e) {
                    $message = ['error' => [$e->getMessage()]];
                    ApiResponse::error($message);
                }
            }
            $message = ['success' => [__("Payment Successful, Please Go Back Your App")]];
            return ApiResponse::onlySuccess($message);
        }else{
            $message = ['error' => [__('Payment Failed,Please Contact With Owner')]];
            ApiResponse::error($message);
        }
    }

}
