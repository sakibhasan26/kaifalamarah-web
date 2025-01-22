<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\Campaign;
use App\Models\UserWallet;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Constants\NotificationConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use App\Traits\PaymentGateway\Manual;
use App\Traits\PaymentGateway\Stripe;
use App\Constants\PaymentGatewayConst;
use App\Models\Admin\CryptoTransaction;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Helpers\Api\PaymentGatewayApi;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class DonationController extends Controller
{
    use Stripe, Manual;


    /**
     * Donation History Data Fetch
     *
     * @method GET
     * @return \Illuminate\Http\Response
    */
    public function donationHistory(Request $request){
        $lang    = $request->language;
        $default = 'en';

        $donation_history = Transaction::with('campaign','currency')
                                        ->orderBy('id', 'desc')
                                        ->where('user_id', Auth::guard('api')
                                        ->user()->id)
                                        ->where('type', PaymentGatewayConst::TYPEDONATION)->get()->map(function($data) use ($lang, $default){
            $title = isset($data->campaign->title->language->$lang) ? $data->campaign->title->language->$lang->title : $data->campaign->title->language->$default->title;
            $desc = isset($data->campaign->title->language->$lang) ? $data->campaign->title->language->$lang->title : $data->campaign->title->language->$default->title;
            $desc_limit = Str::limit($desc, 50, '...');


            return[
                'image'         => $data->campaign->image,
                'title'         => $title,
                'desc'          => $desc_limit,
                'currency'      => get_default_currency_code(),
                'amount'        => get_amount($data->request_amount),
                'status'        => $data->status,
                "confirm"          => $data->confirm??false,
                "dynamic_inputs"   => $data->dynamic_inputs,
                "confirm_url"      => $data->confirmUrlDonation,
                'gateway_alias' => $data->currency->gateway->alias ?? '',
                'created_at'    => $data->created_at,
            ];

        });

        $data = [
            'default_image'      => get_files_public_path('default'),
            'image_path'         => get_files_public_path('campaigns'),
            'donation_histories' => $donation_history
        ];

        $message = ['success'=> [__('Donations History Data Fetch Successful')]];
        return ApiResponse::success($message, $data);

    }

    /**
     * Donation GateWay Data Fetch
     *
     * @method GET
     * @return \Illuminate\Http\Response
    */
    public function donationGateway(){
        $payment_gateways_currencies = PaymentGatewayCurrency::whereHas('gateway', function ($gateway) {
            $gateway->where('slug', PaymentGatewayConst::add_money_slug());
            $gateway->where('status', 1);
        })->get();

        if(Auth::guard(get_auth_guard())->check()){
            $wallet = DB::table('user_wallets')->where('user_id', Auth::guard('api')->id())->first();
            $currency = $payment_gateways_currencies->map(function($data){
                return[
                    'alias'  => $data->alias,
                    'name'   => $data->name,
                    'type'   => $data->gateway->type,
                    'amount' => null,
                ];
            });
            $currency[] = [
                'alias'  => 'wallet-usd',
                'name'   => 'Balance',
                'type'   => 'Balance',
                'amount' => get_amount($wallet->balance),
            ];
        }else{
            $currency = [];
            foreach ($payment_gateways_currencies as $key => $data)  {
                if (!$data->gateway->isManual()){
                    $currency[] = [
                        'alias'  => $data->alias,
                        'name'   => $data->name,
                        'type'   => $data->gateway->type,
                        'amount' => null,
                    ];
                }
            }
        }

        $message =  ['success'=>[__('Donation gateway data fetch')]];
        return ApiResponse::success($message, $currency);

    }

    /**
     * Donation GateWay Data Fetch
     *
     * @method POST
     * Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function donationSubmit(Request $request){

        $validator = Validator::make($request->all(), [
            'currency'     => "required|string",
            'amount'       => "required|numeric",
            'payment_type' => "required|string",
            'campaign_id'  => "required|numeric",
        ]);

        if($validator->fails()){
            $error =  ['error'=>$validator->errors()->all()];
            return ApiResponse::validation($error);
        }

        if (Auth::guard(get_auth_guard())->check()) {
            $user_type = PaymentGatewayConst::AUTHENTICATED;
        } else {
            $user_type = PaymentGatewayConst::GUEST;
        }
        $request->merge([
            'user_type' =>$user_type
        ]);


        $campaign = Campaign::find($request->campaign_id);

        if(!isset($campaign)){
            $error = ['error'=>[__('Campaign not found')]];
            return ApiResponse::error($error);
        }

        $need_amount = $campaign->to_go;

        // Donation Validation
        if($need_amount == 0){
            $error = ['error' => [__('We do not need more donation for this campaign')]];
            return ApiResponse::error($error);
        }else if($need_amount < $request->amount){
            $error = ['error' => ['We need just '.$need_amount.' '. get_default_currency_code()]];
            return ApiResponse::error($error);
        }

        $alias = $request->currency;
        $amount = $request->amount;

        if($alias == 'wallet-usd'){
            if(!Auth::guard('api')->check()){
                $message = ['error'=>[__('Unauthorized user')]];
                return ApiResponse::unauthorized( $message, $data = null);
            }
            return $this->walletPayment($request->all());
        }else{
            $user = Auth::guard(get_auth_guard())->user();
            $payment_gateways_currencies = PaymentGatewayCurrency::where('alias',$alias)->whereHas('gateway', function ($gateway) {
                $gateway->where('slug', PaymentGatewayConst::add_money_slug());
                $gateway->where('status', 1);
            })->first();


            if(!$payment_gateways_currencies){
                 $error = ['error'=>[__('Gateway Information is not available. Please provide payment gateway currency alias')]];
                 return ApiResponse::error($error);
            }

            if($amount < ($payment_gateways_currencies->min_limit/$payment_gateways_currencies->rate) || $amount > ($payment_gateways_currencies->max_limit/$payment_gateways_currencies->rate)) {
                $error = ['error'=>[__('Please follow the transaction limit')]];
                return ApiResponse::error($error);
            }

            try{
                $payment_gateway_currency = PaymentGatewayCurrency::where('alias', $request->currency)->first();
                $payment_gateway = PaymentGateway::where('id', $payment_gateway_currency->payment_gateway_id)->first();
                if($payment_gateway->alias == PaymentGatewayConst::FLUTTER_WAVE){

                    if (Auth::guard(get_auth_guard())->check()) {

                        $instance = PaymentGatewayApi::init($request->all())->gateway()->api()->get();
                        $trx = $instance['response']['id']??$instance['response']['trx'];
                        $temData = TemporaryData::where('identifier',$trx)->first();


                        if(!$temData){
                            $error = ['error'=>[__("Invalid Request")]];
                            return ApiResponse::error($error);
                        }

                        $payment_gateway_currency = PaymentGatewayCurrency::where('id', $temData->data->currency)->first();
                        $payment_gateway = PaymentGateway::where('id', $temData->data->gateway)->first();

                        $payment_informations =[
                            'trx' =>  $temData->identifier,
                            'gateway_currency_name' =>  $payment_gateway_currency->name,
                            'request_amount' => getAmount($temData->data->amount->requested_amount,2).' '.$temData->data->amount->default_currency,
                            'exchange_rate' => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate).' '.$temData->data->amount->sender_cur_code,
                            'total_charge' => getAmount($temData->data->amount->total_charge,2).' '.$temData->data->amount->sender_cur_code,
                            'will_get' => getAmount($temData->data->amount->will_get,2).' '.$temData->data->amount->default_currency,
                            'payable_amount' =>  getAmount($temData->data->amount->total_amount,2).' '.$temData->data->amount->sender_cur_code,
                        ];
                        $data =[
                            'gateway_type' => $payment_gateway->type,
                            'payment_type' => $temData->data->payment_type,
                            'gateway_currency_name' => $payment_gateway_currency->name,
                            'alias' => $payment_gateway_currency->alias,
                            'identify' => $temData->type,
                            'payment_informations' => $payment_informations,
                            'url' => @$temData->data->response->link,
                            'method' => "get",
                        ];
                        $message =  ['success'=>[__('Donation Inserted Successfully')]];
                        return ApiResponse::success($message,$data);
                    }else{
                        $hasData = $request->all();
                        $campaign = Campaign::find($hasData['campaign_id']);
                        if(!$campaign){
                            $message =  ['error'=>[__('Campaign not found')]];
                            return ApiResponse::error($message);
                        }
                        $field = [
                            [
                                'field_name' => "name",
                                'label_name' => "Name",
                            ],
                            [
                                'field_name' => "phone",
                                'label_name' => "Phone",
                            ],
                            [
                                'field_name' => "email",
                                'label_name' => "Email",
                            ],
                            [
                                'field_name' => "campaign_id",
                                'type'       => "hidden",
                            ],
                            [
                                'field_name' => "amount",
                                'type'       => "hidden",
                            ],
                            [
                                'field_name' => "currency",
                                'type'       => "hidden",
                            ],
                            [
                                'field_name' => "payment_type",
                                'type'       => "hidden",
                            ],
                        ];
                        $field_array = (array) $field;
                        $payment_info = [
                            'campaign_id'   => $hasData['campaign_id'],
                            'amount'        => $hasData['amount'],
                            'currency'      => $hasData['currency'],
                            'payment_type'  => $hasData['payment_type'],
                        ];


                        $route = route('api.v1.user.flutterwave.donation.confirmed');


                        $data =[
                            'input_fields'          => $field_array,
                            'payment_info'          => $payment_info,
                            'url'                   => $route,
                            'method'                => "post",
                        ];
                        $message =  ['success'=>[__('Donation Inserted Successfully')]];
                        return ApiResponse::success($message, $data);
                    }
                }elseif ($payment_gateway->alias == PaymentGatewayConst::PAYSTACK) {
                    if (Auth::guard(get_auth_guard())->check()) {

                        $instance = PaymentGatewayApi::init($request->all())->gateway()->api()->get();
                        $trx = $instance['response']['id'] ?? $instance['response']['trx'];
                        $temData = TemporaryData::where('identifier', $trx)->first();
                        $url = $instance['response']['link'];
                        if (!$temData) {
                            $error = ['error' => [__("Invalid Request")]];
                            return ApiResponse::error($error);
                        }

                        $payment_gateway_currency = PaymentGatewayCurrency::where('id', $temData->data->currency)->first();
                        $payment_gateway = PaymentGateway::where('id', $temData->data->gateway)->first();

                        $payment_informations = [
                            'trx' =>  $temData->identifier,
                            'gateway_currency_name' =>  $payment_gateway_currency->name,
                            'request_amount' => getAmount($temData->data->amount->requested_amount, 2) . ' ' . $temData->data->amount->default_currency,
                            'exchange_rate' => "1" . ' ' . $temData->data->amount->default_currency . ' = ' . getAmount($temData->data->amount->sender_cur_rate) . ' ' . $temData->data->amount->sender_cur_code,
                            'total_charge' => getAmount($temData->data->amount->total_charge, 2) . ' ' . $temData->data->amount->sender_cur_code,
                            'will_get' => getAmount($temData->data->amount->will_get, 2) . ' ' . $temData->data->amount->default_currency,
                            'payable_amount' =>  getAmount($temData->data->amount->total_amount, 2) . ' ' . $temData->data->amount->sender_cur_code,
                        ];
                        $data = [
                            'gateway_type' => $payment_gateway->type,
                            'payment_type' => $temData->data->payment_type,
                            'gateway_currency_name' => $payment_gateway_currency->name,
                            'alias' => $payment_gateway_currency->alias,
                            'identify' => $temData->type,
                            'payment_informations' => $payment_informations,
                            'url' => $url,
                            'method' => "get",
                        ];
                        $message =  ['success' => [__('Donation Inserted Successfully')]];
                        return ApiResponse::success($message, $data);
                    }else{
                        $hasData = $request->all();
                        $campaign = Campaign::find($hasData['campaign_id']);
                        if(!$campaign){
                            $message =  ['error'=>[__('Campaign not found')]];
                            return ApiResponse::error($message);
                        }
                        $field = [
                            [
                                'field_name' => "name",
                                'label_name' => "Name",
                            ],
                            [
                                'field_name' => "phone",
                                'label_name' => "Phone",
                            ],
                            [
                                'field_name' => "email",
                                'label_name' => "Email",
                            ],
                            [
                                'field_name' => "campaign_id",
                                'type'       => "hidden",
                            ],
                            [
                                'field_name' => "amount",
                                'type'       => "hidden",
                            ],
                            [
                                'field_name' => "currency",
                                'type'       => "hidden",
                            ],
                            [
                                'field_name' => "payment_type",
                                'type'       => "hidden",
                            ],
                        ];
                        $field_array = (array) $field;
                        $payment_info = [
                            'campaign_id'   => $hasData['campaign_id'],
                            'amount'        => $hasData['amount'],
                            'currency'      => $hasData['currency'],
                            'payment_type'  => $hasData['payment_type'],
                        ];

                        if ($payment_gateway->alias == PaymentGatewayConst::FLUTTER_WAVE) {
                            $route = route('api.v1.user.flutterwave.donation.confirmed');
                        }else{
                            $route = route('api.v1.user.donation/paystack.confirm');
                        }

                        $data =[
                            'input_fields'          => $field_array,
                            'payment_info'          => $payment_info,
                            'url'                   => $route,
                            'method'                => "post",
                        ];
                        $message =  ['success'=>[__('Donation Inserted Successfully')]];
                        return ApiResponse::success($message, $data);
                    }
                }

                else{

                    if($payment_gateway->alias == PaymentGatewayConst::TATUM || $payment_gateway->alias == PaymentGatewayConst::COINGATE){
                        if(!Auth::guard(get_auth_guard())->check()) return ApiResponse::error(['error' => ['Unauthenticated User Can Not Donation With '.$payment_gateway->name]]);
                    }

                    $instance = PaymentGatewayApi::init($request->all())->type(PaymentGatewayConst::TYPEDONATION)->gateway()->api()->get();

                    $trx = $instance['response']['id']?? $instance['response']['trx'] ?? $instance['response']['reference_id']??$instance['response']['order_id']??$instance['response']['tokenValue'] ?? $instance['response']['url']??$instance['response']['temp_identifier'];
                    $temData = TemporaryData::where('identifier',$trx)->first();
                    if(!$temData){
                        $error = ['error'=>["Invalid Request"]];
                        return ApiResponse::error($error);
                    }
                    $payment_gateway_currency = PaymentGatewayCurrency::where('id', $temData->data->currency)->first();
                    $payment_gateway = PaymentGateway::where('id', $temData->data->gateway)->first();
                    if($payment_gateway->type == "AUTOMATIC") {

                        if($temData->type == PaymentGatewayConst::STRIPE) {
                            if(Auth::guard(get_auth_guard())->check()){
                                $url = @$temData->data->response->link."?prefilled_email=".@$user->email;
                            }else{
                                $url = @$temData->data->response->link;
                            }
                            $payment_informations =[
                                'trx' =>  $temData->identifier,
                                'gateway_currency_name' =>  $payment_gateway_currency->name,
                                'request_amount' => getAmount($temData->data->amount->requested_amount,4).' '.$temData->data->amount->default_currency,
                                'exchange_rate' => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate,4).' '.$temData->data->amount->sender_cur_code,
                                'total_charge' => getAmount($temData->data->amount->total_charge,4).' '.$temData->data->amount->sender_cur_code,
                                'will_get' => getAmount($temData->data->amount->will_get,4).' '.$temData->data->amount->default_currency,
                                'payable_amount' =>  getAmount($temData->data->amount->total_amount,4).' '.$temData->data->amount->sender_cur_code,
                            ];
                            $data =[
                                'gateway_type' => $payment_gateway->type,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'alias' => $payment_gateway_currency->alias,
                                'identify' => $temData->type,
                                'payment_informations' => $payment_informations,
                                'url' => $url,
                                'method' => "get",
                            ];
                            return ApiResponse::success(['success'=>[__('Donation Inserted Successfully')]], $data);
                            $message =  ['success'=>[__('Donation Inserted Successfully')]];
                            return ApiResponse::success($message, $data);
                        }elseif($temData->type == PaymentGatewayConst::COINGATE) {
                            $payment_informations =[
                                'trx'                   => $temData->identifier,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'request_amount'        => getAmount($temData->data->amount->requested_amount,4).' '.$temData->data->amount->default_currency,
                                'exchange_rate'         => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate,4).' '.$temData->data->amount->sender_cur_code,
                                'total_charge'          => getAmount($temData->data->amount->total_charge,4).' '.$temData->data->amount->sender_cur_code,
                                'will_get'              => getAmount($temData->data->amount->will_get,4).' '.$temData->data->amount->default_currency,
                                'payable_amount'        => getAmount($temData->data->amount->total_amount,4).' '.$temData->data->amount->sender_cur_code,
                            ];
                            $data =[
                                'gateway_type'          => $payment_gateway->type,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'alias'                 => $payment_gateway_currency->alias,
                                'identify'              => $temData->type,
                                'payment_informations'  => $payment_informations,
                                'url'                   => $instance['response']['link'],
                                'method'                => "get",
                            ];
                            $message =  ['success'=>[__('Donation Inserted Successfully')]];
                            return ApiResponse::success($message,$data);
                        }elseif($temData->type == PaymentGatewayConst::SSLCOMMERZ) {
                            $payment_informations =[
                                'trx' =>  $temData->identifier,
                                'gateway_currency_name' =>  $payment_gateway_currency->name,
                                'request_amount' => getAmount($temData->data->amount->requested_amount,4).' '.$temData->data->amount->default_currency,
                                'exchange_rate' => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate,4).' '.$temData->data->amount->sender_cur_code,
                                'total_charge' => getAmount($temData->data->amount->total_charge,4).' '.$temData->data->amount->sender_cur_code,
                                'will_get' => getAmount($temData->data->amount->will_get,4).' '.$temData->data->amount->default_currency,
                                'payable_amount' =>  getAmount($temData->data->amount->total_amount,4).' '.$temData->data->amount->sender_cur_code,
                            ];
                            $data =[
                                'gateway_type' => $payment_gateway->type,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'alias' => $payment_gateway_currency->alias,
                                'identify' => $temData->type,
                                'payment_informations' => $payment_informations,
                                'url' => $instance['response']['link'],
                                'method' => "get",
                            ];
                            $message =  ['success'=>[__('Donation Inserted Successfully')]];
                            return ApiResponse::success($message,$data);
                        }else if ($temData->type == PaymentGatewayConst::QRPAY) {
                            $payment_informations = [

                                'trx' =>  $temData->identifier,
                                'gateway_currency_name' =>  $payment_gateway_currency->name,
                                'request_amount' => getAmount($temData->data->amount->requested_amount, 4) . ' ' . $temData->data->amount->default_currency,
                                'exchange_rate' => "1" . ' ' . $temData->data->amount->default_currency . ' = ' . getAmount($temData->data->amount->sender_cur_rate, 4) . ' ' . $temData->data->amount->sender_cur_code,
                                'total_charge' => getAmount($temData->data->amount->total_charge, 4) . ' ' . $temData->data->amount->sender_cur_code,
                                'will_get' => getAmount($temData->data->amount->will_get, 4) . ' ' . $temData->data->amount->default_currency,
                                'payable_amount' =>  getAmount($temData->data->amount->total_amount, 4) . ' ' . $temData->data->amount->sender_cur_code,
                            ];
                            $data = [
                                'gateway_type' => $payment_gateway->type,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'alias' => $payment_gateway_currency->alias,
                                'identify' => $temData->type,
                                'payment_informations' => $payment_informations,
                                'url' => @$instance['response']['link'],
                                'method' => "get",
                            ];
                            $message =  ['success' => [__('Donation Inserted Successfully')]];
                            return ApiResponse::success($message, $data);
                        }else if($temData->type == PaymentGatewayConst::RAZORPAY){
                            $payment_informations =[
                                'trx' =>  $temData->identifier,
                                'gateway_currency_name' =>  $payment_gateway_currency->name,
                                'request_amount' => getAmount($temData->data->amount->requested_amount,4).' '.$temData->data->amount->default_currency,
                                'exchange_rate' => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate,4).' '.$temData->data->amount->sender_cur_code,
                                'total_charge' => getAmount($temData->data->amount->total_charge,4).' '.$temData->data->amount->sender_cur_code,
                                'will_get' => getAmount($temData->data->amount->will_get,4).' '.$temData->data->amount->default_currency,
                                'payable_amount' =>  getAmount($temData->data->amount->total_amount,4).' '.$temData->data->amount->sender_cur_code,
                            ];
                            $data =[
                                'gateway_type' => $payment_gateway->type,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'alias' => $payment_gateway_currency->alias,
                                'identify' => $temData->type,
                                'payment_informations' => $payment_informations,
                                'url' => $instance['response']['redirect_url'],
                                'method' => "get",
                            ];
                            $message =  ['success'=>[__('Donation Inserted Successfully')]];
                            return ApiResponse::success($message,$data);
                        }else if($temData->type == PaymentGatewayConst::PAYPAL) {
                            $payment_informations =[
                                'trx'                   => $temData->identifier,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'request_amount'        => getAmount($temData->data->amount->requested_amount,2).' '.$temData->data->amount->default_currency,
                                'exchange_rate'         => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate).' '.$temData->data->amount->sender_cur_code,
                                'total_charge'          => getAmount($temData->data->amount->total_charge,2).' '.$temData->data->amount->sender_cur_code,
                                'will_get'              => getAmount($temData->data->amount->will_get,2).' '.$temData->data->amount->default_currency,
                                'payable_amount'        => getAmount($temData->data->amount->total_amount,2).' '.$temData->data->amount->sender_cur_code,
                            ];
                            $data =[
                                'gateway_type'          => $payment_gateway->type,
                                'payment_type'          => $temData->data->payment_type,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'alias'                 => $payment_gateway_currency->alias,
                                'identify'              => $temData->type,
                                'payment_informations'  => $payment_informations,
                                'url'                   => @$temData->data->response->links,
                                'method'                => "get",
                            ];
                            $message =  ['success'=>[__('Donation Inserted Successfully')]];
                            return ApiResponse::success($message,$data);

                        }else if($temData->type == PaymentGatewayConst::PAGADITO){
                            $payment_informations =[
                                'trx'                   => $temData->identifier,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'request_amount'        => getAmount($temData->data->amount->requested_amount,4).' '.$temData->data->amount->default_currency,
                                'exchange_rate'         => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate,4).' '.$temData->data->amount->sender_cur_code,
                                'total_charge'          => getAmount($temData->data->amount->total_charge,4).' '.$temData->data->amount->sender_cur_code,
                                'will_get'              => getAmount($temData->data->amount->will_get,4).' '.$temData->data->amount->default_currency,
                                'payable_amount'        => getAmount($temData->data->amount->total_amount,4).' '.$temData->data->amount->sender_cur_code,
                            ];
                            $data =[
                                'gateway_type'          => $payment_gateway->type,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'alias'                 => $payment_gateway_currency->alias,
                                'identify'              => $temData->type,
                                'payment_informations'  => $payment_informations,
                                'url'                   => @$temData->data->response->value,
                                'method'                => "get",
                            ];
                            $message =  ['success'=>[__('Donation Inserted Successfully')]];
                            return ApiResponse::success($message,$data);

                        }else if($temData->type == PaymentGatewayConst::TATUM) {
                            $payment_informations =[
                                'trx'                   => $temData->identifier,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'request_amount'        => getAmount($temData->data->amount->requested_amount,4).' '.$temData->data->amount->default_currency,
                                'exchange_rate'         => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate,4).' '.$temData->data->amount->sender_cur_code,
                                'total_charge'          => getAmount($temData->data->amount->total_charge,4).' '.$temData->data->amount->sender_cur_code,
                                'will_get'              => getAmount($temData->data->amount->will_get,4).' '.$temData->data->amount->default_currency,
                                'payable_amount'        => getAmount($temData->data->amount->total_amount,4).' '.$temData->data->amount->sender_cur_code,
                            ];
                            $data =[
                                'gateway_type'          => $payment_gateway->type,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'alias'                 => $payment_gateway_currency->alias,
                                'identify'              => $temData->type,
                                'payment_informations'  => $payment_informations,
                                'redirect_url'          => $instance['response']['redirect_url'],
                                'redirect_links'        => $instance['response']['redirect_links'],
                                'action_type'           => $instance['response']['type']  ?? false,
                                'address_info'          => $instance['response']['address_info'] ?? [],
                            ];
                            $message =  ['success'=>[__('Donation Inserted Successfully')]];
                            return ApiResponse::success($message,$data);

                        }else if($temData->type == PaymentGatewayConst::PERFECT_MONEY){
                            $payment_informations =[
                                'trx' =>  $temData->identifier,
                                'gateway_currency_name' =>  $payment_gateway_currency->name,
                                'request_amount' => getAmount($temData->data->amount->requested_amount,4).' '.$temData->data->amount->default_currency,
                                'exchange_rate' => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate,4).' '.$temData->data->amount->sender_cur_code,
                                'total_charge' => getAmount($temData->data->amount->total_charge,4).' '.$temData->data->amount->sender_cur_code,
                                'will_get' => getAmount($temData->data->amount->will_get,4).' '.$temData->data->amount->default_currency,
                                'payable_amount' =>  getAmount($temData->data->amount->total_amount,4).' '.$temData->data->amount->sender_cur_code,
                            ];
                            $data =[
                                'gateway_type'          => $payment_gateway->type,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'alias'                 => $payment_gateway_currency->alias,
                                'identify'              => $temData->type,
                                'payment_informations'  => $payment_informations,
                                'url'                   => $instance['response']['redirect_url'],
                                'method'                => "get",
                            ];

                            $message =  ['success'=>[__('Donation Inserted Successfully')]];
                            return ApiResponse::success($message,$data);

                        }
                    }elseif($payment_gateway->type == "MANUAL"){

                        if(!Auth::guard(get_auth_guard())->check()){
                            $message = ['error'=>['Unauthorized user']];
                            return ApiResponse::unauthorized( $message, $data = null);
                        }

                            $payment_informations =[
                                'trx'                   => $temData->identifier,
                                'gateway_currency_name' => $payment_gateway_currency->name,
                                'request_amount'        => getAmount($temData->data->amount->requested_amount,2).' '.$temData->data->amount->default_currency,
                                'exchange_rate'         => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate).' '.$temData->data->amount->sender_cur_code,
                                'total_charge'          => getAmount($temData->data->amount->total_charge,2).' '.$temData->data->amount->sender_cur_code,
                                'will_get'              => getAmount($temData->data->amount->will_get,2).' '.$temData->data->amount->default_currency,
                                'payable_amount'        => getAmount($temData->data->amount->total_amount,2).' '.$temData->data->amount->sender_cur_code,
                            ];
                            $data =[
                                    'gateway_type'          => $payment_gateway->type,
                                    'payment_type'          => $temData->data->payment_type,
                                    'gateway_currency_name' => $payment_gateway_currency->name,
                                    'alias'                 => $payment_gateway_currency->alias,
                                    'identify'              => $temData->type,
                                    'details'               => $payment_gateway->desc??null,
                                    'input_fields'          => $payment_gateway->input_fields ?? null,
                                    'payment_informations'  => $payment_informations,
                                    'url'                   => route('api.v1.user.add-money.'),
                                    'method'                => "post",
                                ];
                                $message =  ['success'=>[__('Donation Inserted Successfully')]];
                                return ApiResponse::success($message, $data);
                    }else{
                        $error = ['error'=>["Something is wrong"]];
                        return ApiResponse::error($error);
                    }
                }
            }catch(Exception $e) {

                $error = ['error'=>[$e->getMessage()]];
                return ApiResponse::error($error);
            }
        }



    }

    public function flutterwaveConfirmed(Request $request){
        $validator = Validator::make($request->all(), [
            'currency'      => "required|string",
            'amount'        => "required|numeric",
            'payment_type'  => "required|string",
            'campaign_id'   => "required|numeric",
            'name'          => 'nullable',
            'phone'         => 'nullable',
            'email'         => 'required|email',
        ]);


        if($validator->fails()){
            $error =  ['error'=>$validator->errors()->all()];
            return ApiResponse::validation($error);
        }

        $campaign = Campaign::find($request->campaign_id);

        if(!isset($campaign)){
            $error = ['error'=>['Campaign not found']];
            return ApiResponse::error($error);
        }

        $need_amount = $campaign->to_go;

        // Donation Validation
        if($need_amount == 0){
            $error = ['error' => ['We do not need more donation for this campaign']];
            return ApiResponse::error($error);
        }else if($need_amount < $request->amount){
            $error = ['error' => ['We need just '.$need_amount.' '. get_default_currency_code()]];
            return ApiResponse::error($error);
        }

        $alias = $request->currency;
        $amount = $request->amount;
        $payment_gateways_currencies = PaymentGatewayCurrency::where('alias',$alias)->whereHas('gateway', function ($gateway) {
            $gateway->where('slug', PaymentGatewayConst::add_money_slug());
            $gateway->where('status', 1);
        })->first();

        if(!$payment_gateways_currencies){
             $error = ['error'=>['Gateway Information is not available. Please provide payment gateway currency alias']];
             return ApiResponse::error($error);
        }

        if($amount < ($payment_gateways_currencies->min_limit/$payment_gateways_currencies->rate) || $amount > ($payment_gateways_currencies->max_limit/$payment_gateways_currencies->rate)) {
            $error = ['error'=>['Please follow the transaction limit']];
            return ApiResponse::error($error);
        }

        try{
            $instance = PaymentGatewayApi::init($request->all())->gateway()->api()->get();
            $trx = $instance['response']['id']??$instance['response']['trx'];
            $temData = TemporaryData::where('identifier',$trx)->first();

            if(!$temData){
                $error = ['error'=>["Invalid Request"]];
                return ApiResponse::error($error);
            }

            $payment_gateway_currency = PaymentGatewayCurrency::where('id', $temData->data->currency)->first();
            $payment_gateway = PaymentGateway::where('id', $temData->data->gateway)->first();

            $payment_informations =[
                'trx' =>  $temData->identifier,
                'gateway_currency_name' =>  $payment_gateway_currency->name,
                'request_amount' => getAmount($temData->data->amount->requested_amount,2).' '.$temData->data->amount->default_currency,
                'exchange_rate' => "1".' '.$temData->data->amount->default_currency.' = '.getAmount($temData->data->amount->sender_cur_rate).' '.$temData->data->amount->sender_cur_code,
                'total_charge' => getAmount($temData->data->amount->total_charge,2).' '.$temData->data->amount->sender_cur_code,
                'will_get' => getAmount($temData->data->amount->will_get,2).' '.$temData->data->amount->default_currency,
                'payable_amount' =>  getAmount($temData->data->amount->total_amount,2).' '.$temData->data->amount->sender_cur_code,
            ];
            $data =[
                'gateway_type' => $payment_gateway->type,
                'payment_type' => $temData->data->payment_type,
                'gateway_currency_name' => $payment_gateway_currency->name,
                'alias' => $payment_gateway_currency->alias,
                'identify' => $temData->type,
                'payment_informations' => $payment_informations,
                'url' => @$temData->data->response->link,
                'method' => "get",
            ];

            $message =  ['success'=>['Donatnion Inserted Successfully']];
            return ApiResponse::success($message,$data);


        }catch(Exception $e) {
            $error = ['error'=>[$e->getMessage()]];
            return ApiResponse::error($error);
        }
    }


    public function success(Request $request, $gateway)
    {
        $requestData = $request->all();
        $token = $requestData['token'] ?? "";
        $checkTempData = TemporaryData::where("type", $gateway)->where("identifier", $token)->first();
        if (!$checkTempData){
            $message = ['error' => ["Transaction failed. Record didn\'t saved properly. Please try again."]];
            return ApiResponse::error($message);
        }
        //    return 'no';
        $checkTempData = $checkTempData->toArray();
        try {
            PaymentGatewayApi::init($checkTempData)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive();
        } catch (Exception $e) {
            $message = ['error' => [$e->getMessage()]];
            return ApiResponse::error($message);
        }

        $message = ['success' => ["Donation successful"]];
        return ApiResponse::onlySuccess($message);
    }

   public function cancel(Request $request, $gateway)
   {
       return redirect()->route('index');
   }
    /**
     * This method for wallet payment
     * @param $request
     */
    public function walletPayment($request){

        $validator = Validator::make($request, [
            'amount'      => 'sometimes|required|numeric',
            'currency'    => 'required',
            'campaign_id' => 'required',
        ]);

        if($validator->fails()){
            $error =  ['error'=>$validator->errors()->all()];
            return ApiResponse::validation($error);
        }

        $validated = $validator->validated();

        $wallet    = UserWallet::where('user_id', Auth::guard('api')->user()->id)->first();

        if($wallet->balance > $validated['amount']){

        DB::beginTransaction();
        try{
            // Add money
            $trx_id = generateTrxString("transactions","trx_id",'D',9);
            $id = DB::table("transactions")->insertGetId([
                'user_id'                       => Auth::guard('api')->user()->id,
                'user_wallet_id'                => $wallet->id,
                'campaign_id'                   => $validated['campaign_id'],
                'type'                          => PaymentGatewayConst::DONATION,
                'trx_id'                        => $trx_id,
                'request_amount'                => $validated['amount'],
                'payable'                       => $validated['amount'],
                'available_balance'             => $wallet->balance - $validated['amount'],
                'remark'                        => ucwords(remove_speacial_char(PaymentGatewayConst::DONATION," ")) . " With " . $validated['currency'],
                'details'                       => "wallet payment successfull",
                'status'                        => true,
                'created_at'                    => now(),
            ]);

            // Wallet update
            $wallet->update([
                'balance' => $wallet->balance - $validated['amount']
            ]);

            // Campaign update
            $campaign = Campaign::findOrFail($validated['campaign_id']);

            $campaign->update([
                'raised' => $campaign->raised + $validated['amount'],
                'to_go'  => $campaign->to_go - $validated['amount'],
            ]);

            // Insert device wallet
            $this->insertDeviceWallet($id);
            $this->insertChargesWallet($validated, $id);

            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
            $success =  ['success'=>['Successfully donation']];
            return ApiResponse::onlySuccess($success);
        }else{
            $error =  ['error'=>['Insufficient your wallet balance!']];
            return ApiResponse::error($error);
        }
    }


    public function insertDeviceWallet($id) {
        $client_ip = request()->ip() ?? false;
        $location = geoip()->getLocation($client_ip);
        $agent = new Agent();
        $mac = "";

        DB::beginTransaction();
        try{
            DB::table("transaction_devices")->insert([
                'transaction_id'=> $id,
                'ip'            => $client_ip,
                'mac'           => $mac,
                'city'          => $location['city'] ?? "",
                'country'       => $location['country'] ?? "",
                'longitude'     => $location['lon'] ?? "",
                'latitude'      => $location['lat'] ?? "",
                'timezone'      => $location['timezone'] ?? "",
                'browser'       => $agent->browser() ?? "",
                'os'            => $agent->platform() ?? "",
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function insertChargesWallet($validated,$id) {
        DB::beginTransaction();
        try{
            DB::table('transaction_charges')->insert([
                'transaction_id'    => $id,
                'percent_charge'    => 0,
                'fixed_charge'      => 0,
                'total_charge'      => 0,
                'created_at'        => now(),
            ]);
            DB::commit();

             $notification_content = [
                'title'         => "Add Money",
                'message'       => "Your Wallet (".get_default_currency_code().") balance  has been added ".$validated['amount'].' '. get_default_currency_code(),
                'time'          => Carbon::now()->diffForHumans(),
                'image'         => files_asset_path('profile-default'),
            ];

            UserNotification::create([
                'type'    => NotificationConst::BALANCE_ADDED,
                'user_id' => Auth::guard('api')->user()->id,
                'message' => $notification_content,
            ]);

            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }


    public function flutterwaveCallback()
    {

        $status = request()->status;

        if ($status ==  'successful') {

            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);

            $requestData = request()->tx_ref;

            $token = $requestData;

            $checkTempData = TemporaryData::where("type",'flutterwave')->where("identifier",$token)->first();

            $message = ['error' => ['Transaction faild. Record didn\'t saved properly. Please try again.']];

            if(!$checkTempData) return ApiResponse::error($message);

            $checkTempData = $checkTempData->toArray();
            try{
                 PaymentGatewayApi::init($checkTempData)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('flutterWave');
            }catch(Exception $e) {
                 $message = ['error' => [$e->getMessage()]];
                 ApiResponse::error($message);
            }
             $message = ['success' => ["Payment successful"]];
             return ApiResponse::onlySuccess($message);
        }
        elseif ($status ==  'cancelled'){
             $message = ['error' => ['Payment Cancelled']];
             ApiResponse::error($message);
        }
        else{
             $message = ['error' => ['Someting failed']];
             ApiResponse::error($message);
        }
    }

    //stripe success
    public function stripePaymentSuccess($trx){
        $token = $trx;
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::STRIPE)->where("identifier",$token)->first();
        if(!$checkTempData) return redirect()->route('user.add.money.index')->with(['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]]);
        $checkTempData = $checkTempData->toArray();

        try{
            PaymentGatewayApi::init($checkTempData)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('stripe');
        }catch(Exception $e) {
            return ApiResponse::error(['error' => [__("Something Is Wrong")]]);
        }

        $message = ['success' => ["Donation successful"]];
        return ApiResponse::onlySuccess($message);
    }


    //sslcommerz success
    public function sllCommerzSuccess(Request $request){
        $data = $request->all();
        $token = $data['tran_id'];
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::SSLCOMMERZ)->where("identifier",$token)->first();
        $message = ['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]];
        if(!$checkTempData) return ApiResponse::error($message);
        $checkTempData = $checkTempData->toArray();

        if( $data['status'] != "VALID"){
            $message = ['error' => [__("Added Money Failed")]];
            return ApiResponse::error($message);
        }
        try{
            PaymentGatewayApi::init($checkTempData)->type(PaymentGatewayConst::TYPEADDMONEY)->responseReceive('sslcommerz');
        }catch(Exception $e) {
            $message = ['error' => ["Something Is Wrong..."]];
            return ApiResponse::error($message);
        }
        $message = ['success' => [__("Payment Successful, Please Go Back Your App")]];
        return ApiResponse::onlySuccess($message);
    }
    //sslCommerz fails
    public function sllCommerzFails(Request $request){
        $data = $request->all();

        $token = $data['tran_id'];
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::SSLCOMMERZ)->where("identifier",$token)->first();
        $message = ['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]];
        if(!$checkTempData) return ApiResponse::error($message);
        $checkTempData = $checkTempData->toArray();

        if( $data['status'] == "FAILED"){
            TemporaryData::destroy($checkTempData['id']);
            $message = ['error' => ["Donation Failed"]];
            return ApiResponse::error($message);
        }

    }
    //sslCommerz canceled
    public function sllCommerzCancel(Request $request){
        $data = $request->all();
        $token = $data['tran_id'];
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::SSLCOMMERZ)->where("identifier",$token)->first();
        $message = ['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]];
        if(!$checkTempData) return ApiResponse::error($message);
        $checkTempData = $checkTempData->toArray();

        if( $data['status'] != "VALID"){
            TemporaryData::destroy($checkTempData['id']);
            $message = ['error' => ["Donation Canceled"]];
            return ApiResponse::error($message);
        }
    }

    public function redirectBtnPay(Request $request, $gateway)
    {
        try{
            return PaymentGatewayApi::init([])->handleBtnPay($gateway, $request->all());
        }catch(Exception $e) {
            $message = ['error' => [$e->getMessage()]];
            return ApiResponse::error($message);
        }
    }

    public function razorSuccess(Request $request, $gateway)
        {

            try{
                $token = PaymentGatewayApi::getToken($request->all(),$gateway);

                $temp_data = TemporaryData::where("type",PaymentGatewayConst::RAZORPAY)->where("identifier",$token)->first();

                if(!$temp_data) {
                    if(Transaction::where('callback_ref',$token)->exists()) {
                        $message = ['error' => [__("Transaction request sended successfully")]];
                        return ApiResponse::onlySuccess($message);
                    }else {
                        $message = ['error' => [__('Transaction failed. Record didn\'t saved properly. Please try again')]];
                        return ApiResponse::error($message);
                    }
                }

                $update_temp_data = json_decode(json_encode($temp_data->data),true);
                $update_temp_data['callback_data']  = $request->all();
                $temp_data->update([
                    'data'  => $update_temp_data,
                ]);
                $temp_data = $temp_data->toArray();
                $instance = PaymentGatewayApi::init($temp_data)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive(PaymentGatewayConst::RAZORPAY);
            }catch(Exception $e) {
                return ApiResponse::error(['error' => [$e->getMessage()]]);
            }
            $message = ['success' => [__('Successfully Donation')]];
            return ApiResponse::onlySuccess($message);
        }

        public function razorCancel($trx_id){
            $token = $trx_id;
            if( $token){
                TemporaryData::where("identifier",$token)->delete();
            }
            return ApiResponse::error(['error' => [__('Donation Canceled Successfully')]]);
        }

    public function razorCallback()
    {
        $request_data = request()->all();
        //if payment is successful
        if (isset($request_data['razorpay_order_id'])) {
            $token = $request_data['razorpay_order_id'];

            $checkTempData = TemporaryData::where("type",PaymentGatewayConst::RAZORPAY)->where("identifier",$token)->first();
            if(!$checkTempData) {
                $message = ['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]];
                return ApiResponse::error($message);
            }
            $checkTempData = $checkTempData->toArray();
            $creator_table = $checkTempData['data']->creator_table ?? null;
            $creator_id = $checkTempData['data']->creator_id ?? null;
            $creator_guard = $checkTempData['data']->creator_guard ?? null;
            $api_authenticated_guards = PaymentGatewayConst::apiAuthenticateGuard();

            if($creator_table != null && $creator_id != null && $creator_guard != null) {
                if(!array_key_exists($creator_guard,$api_authenticated_guards)) throw new Exception('Request user doesn\'t save properly. Please try again');
                $creator = DB::table($creator_table)->where("id",$creator_id)->first();
                if(!$creator) throw new Exception(__("Request user doesn\'t save properly. Please try again"));
                $api_user_login_guard = $api_authenticated_guards[$creator_guard];
                Auth::guard($api_user_login_guard)->loginUsingId($creator->id);
            }
            try{
                PaymentGatewayApi::init($checkTempData)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('razorpay');
            }catch(Exception $e) {
                $message = ['error' => [$e->getMessage()]];
                return ApiResponse::error($message);
            }
            $message = ['success' => [__("Payment Successful, Please Go Back Your App")]];
            return ApiResponse::onlySuccess($message);

        }
        else{
            $message = ['error' => [__('Payment Failed')]];
            return ApiResponse::error($message);
        }
    }




    public function qrpayCallback(Request $request)
    {
        if ($request->type ==  'success') {

            $requestData = $request->all();
            $checkTempData = TemporaryData::where("type", 'qrpay')->where("identifier", $requestData['data']['custom'])->first();
            $message = ['error' => ['Transaction Failed. Record didn\'t saved properly. Please try again.']];
            if (!$checkTempData) return ApiResponse::error($message);
            $checkTempData = $checkTempData->toArray();
            try {
                PaymentGatewayApi::init($checkTempData)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('qrpay');
            } catch (Exception $e) {
                $message = ['error' => [$e->getMessage()]];
                return ApiResponse::error($message);
            }
            $message = ['success' => [__("Payment Successful, Please Go Back Your App")]];
            return ApiResponse::onlySuccess($message);
        } else {
            $message = ['error' => [__('Payment Failed')]];
            return ApiResponse::error($message);
        }
    }

    public function qrpayCancel(Request $request, $trx_id)
    {
        $checkTempData = TemporaryData::where("identifier", $trx_id)->delete();
        $message = ['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]];
        return ApiResponse::error($message);
    }

    public function cryptoPaymentConfirm(Request $request, $trx_id)
    {
        $transaction = Transaction::where('trx_id',$trx_id)->where('status', PaymentGatewayConst::STATUSWAITING)->firstOrFail();

        $dy_input_fields = $transaction->details->payment_info->requirements ?? [];
        $validation_rules = $this->generateValidationRules($dy_input_fields);

        $validated = [];
        if(count($validation_rules) > 0) {
            $validated = Validator::make($request->all(), $validation_rules)->validate();
        }

        if(!isset($validated['txn_hash'])) return ApiResponse::error(['error' => [__('Transaction hash is required for verify')]]);

        $receiver_address = $transaction->details->payment_info->receiver_address ?? "";


        // check hash is valid or not
        $crypto_transaction = CryptoTransaction::where('txn_hash', $validated['txn_hash'])
                                                ->where('receiver_address', $receiver_address)
                                                ->where('asset',$transaction->gateway_currency->currency_code)
                                                ->where(function($query) {
                                                    return $query->where('transaction_type',"Native")
                                                                ->orWhere('transaction_type', "native");
                                                })
                                                ->where('status',PaymentGatewayConst::NOT_USED)
                                                ->first();

        if(!$crypto_transaction) return ApiResponse::error(['error' => [__('Transaction hash is not valid! Please input a valid hash')]]);

        if($crypto_transaction->amount >= $transaction->total_payable == false) {
            if(!$crypto_transaction) return ApiResponse::error(['error' => [__('Insufficient amount added. Please contact with system administrator')]]);
        }

        DB::beginTransaction();
        try{

            $campaign_id = $transaction->campaign_id;
            $campaign = Campaign::findOrFail($campaign_id);
            $campaign->update([
                'raised' => $campaign->raised + $transaction->request_amount,
                'to_go' => $campaign->to_go - $transaction->request_amount,
            ]);

            // update crypto transaction as used
            DB::table($crypto_transaction->getTable())->where('id', $crypto_transaction->id)->update([
                'status'        => PaymentGatewayConst::USED,
            ]);

            // update transaction status
            $transaction_details = json_decode(json_encode($transaction->details), true);
            $transaction_details['payment_info']['txn_hash'] = $validated['txn_hash'];

            DB::table($transaction->getTable())->where('id', $transaction->id)->update([
                'details'       => json_encode($transaction_details),
                'status'        => PaymentGatewayConst::STATUSSUCCESS,
            ]);

            DB::commit();

        }catch(Exception $e) {
            DB::rollback();
            return ApiResponse::error(['error' => [__('Something went wrong! Please try again')]]);
        }

        return ApiResponse::onlySuccess(['success' => [__('Successfully Donation')]]);
    }

    public function coinGateSuccess(Request $request, $gateway){
        try{
            $token = $request->token;
            $checkTempData = TemporaryData::where("type",PaymentGatewayConst::COINGATE)->where("identifier",$token)->first();

            if(Transaction::where('callback_ref', $token)->exists()) {
                if(!$checkTempData){
                    $message = ['error' => [__("Transaction request sended successfully")]];
                    return ApiResponse::error($message);
                }
            }else {
                if(!$checkTempData){
                    $message = ['error' => [__("Transaction failed. Record didn\'t saved properly. Please try again")]];
                    return ApiResponse::error($message);
                }
            }
            $update_temp_data = json_decode(json_encode($checkTempData->data),true);
            $update_temp_data['callback_data']  = $request->all();
            $checkTempData->update([
                'data'  => $update_temp_data,
            ]);
            $temp_data = $checkTempData->toArray();
            PaymentGatewayApi::init($temp_data)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('coingate');
        }catch(Exception $e) {
            $message = ['error' => [$e->getMessage()]];
            return ApiResponse::error($message);
        }
        $message = ['success' => [__("Donation Successful, Please Go Back Your App")]];
        return ApiResponse::onlySuccess($message);
    }
    public function coinGateCancel(Request $request, $gateway){
        if($request->has('token')) {
            $identifier = $request->token;
            if($temp_data = TemporaryData::where('identifier', $identifier)->first()) {
                $temp_data->delete();
            }
        }
        $message = ['success' => [__("Donation Canceled Successfully")]];
        return ApiResponse::onlySuccess($message);
    }


    public function perfectSuccess(Request $request, $gateway){

        try{
            $token = PaymentGatewayApi::getToken($request->all(),$gateway);
            $temp_data = TemporaryData::where("type",PaymentGatewayConst::PERFECT_MONEY)->where("identifier",$token)->first();

            if(!$temp_data) {
                if(Transaction::where('callback_ref',$token)->exists()) {
                    $message = ['error' => [__("Transaction request sended successfully")]];
                    return ApiResponse::onlySuccess($message);
                }else {
                    $message = ['error' => [__('Transaction failed. Record didn\'t saved properly. Please try again')]];
                    return ApiResponse::error($message);
                }
            }

            $update_temp_data = json_decode(json_encode($temp_data->data),true);
            $update_temp_data['callback_data']  = $request->all();
            $temp_data->update([
                'data'  => $update_temp_data,
            ]);
            $temp_data = $temp_data->toArray();
            $instance = PaymentGatewayApi::init($temp_data)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('perfect-money');
        }catch(Exception $e) {
            return ApiResponse::error(['error' => [$e->getMessage()]]);
        }
        $message = ['success' => [__('Successfully Donation')]];
        return ApiResponse::onlySuccess($message);
    }

    public function perfectCancel(Request $request,$gateway) {
        $token = PaymentGatewayApi::getToken($request->all(),$gateway);
        $temp_data = TemporaryData::where("type",PaymentGatewayConst::TYPEDONATION)->where("identifier",$token)->first();
        try{
            if($temp_data != null) {
                $temp_data->delete();
            }
        }catch(Exception $e) {
            // Handel error
        }
        $message = ['success' => [__('Donation Canceled Successfully')]];
        return ApiResponse::onlySuccess($message);

    }



    public function paystackConfirm(Request $request){

        $validator = Validator::make($request->all(), [
            'currency'      => "required|string",
            'amount'        => "required|numeric",
            'payment_type'  => "required|string",
            'campaign_id'   => "required|numeric",
            'name'          => 'nullable',
            'phone'         => 'nullable',
            'email'         => 'required|email',
        ]);


        if($validator->fails()){
            $error =  ['error'=>$validator->errors()->all()];
            return ApiResponse::validation($error);
        }

        $campaign = Campaign::find($request->campaign_id);

        if(!isset($campaign)){
            $error = ['error'=>['Campaign not found']];
            return ApiResponse::error($error);
        }

        $need_amount = $campaign->to_go;

        // Donation Validation
        if($need_amount == 0){
            $error = ['error' => ['We do not need more donation for this campaign']];
            return ApiResponse::error($error);
        }else if($need_amount < $request->amount){
            $error = ['error' => ['We need just '.$need_amount.' '. get_default_currency_code()]];
            return ApiResponse::error($error);
        }

        $alias = $request->currency;
        $amount = $request->amount;
        $payment_gateways_currencies = PaymentGatewayCurrency::where('alias',$alias)->whereHas('gateway', function ($gateway) {
            $gateway->where('slug', PaymentGatewayConst::add_money_slug());
            $gateway->where('status', 1);
        })->first();

        if(!$payment_gateways_currencies){
             $error = ['error'=>['Gateway Information is not available. Please provide payment gateway currency alias']];
             return ApiResponse::error($error);
        }

        if($amount < ($payment_gateways_currencies->min_limit/$payment_gateways_currencies->rate) || $amount > ($payment_gateways_currencies->max_limit/$payment_gateways_currencies->rate)) {
            $error = ['error'=>['Please follow the transaction limit']];
            return ApiResponse::error($error);
        }

        try{
            $instance = PaymentGatewayApi::init($request->all())->gateway()->api()->get();
            $trx = $instance['response']['id'] ?? $instance['response']['trx'];
            $temData = TemporaryData::where('identifier', $trx)->first();
            $url = $instance['response']['link'];
            if (!$temData) {
                $error = ['error' => [__("Invalid Request")]];
                return ApiResponse::error($error);
            }

            $payment_gateway_currency = PaymentGatewayCurrency::where('id', $temData->data->currency)->first();
            $payment_gateway = PaymentGateway::where('id', $temData->data->gateway)->first();

            $payment_informations = [
                'trx' =>  $temData->identifier,
                'gateway_currency_name' =>  $payment_gateway_currency->name,
                'request_amount' => getAmount($temData->data->amount->requested_amount, 2) . ' ' . $temData->data->amount->default_currency,
                'exchange_rate' => "1" . ' ' . $temData->data->amount->default_currency . ' = ' . getAmount($temData->data->amount->sender_cur_rate) . ' ' . $temData->data->amount->sender_cur_code,
                'total_charge' => getAmount($temData->data->amount->total_charge, 2) . ' ' . $temData->data->amount->sender_cur_code,
                'will_get' => getAmount($temData->data->amount->will_get, 2) . ' ' . $temData->data->amount->default_currency,
                'payable_amount' =>  getAmount($temData->data->amount->total_amount, 2) . ' ' . $temData->data->amount->sender_cur_code,
            ];
            $data = [
                'gateway_type' => $payment_gateway->type,
                'payment_type' => $temData->data->payment_type,
                'gateway_currency_name' => $payment_gateway_currency->name,
                'alias' => $payment_gateway_currency->alias,
                'identify' => $temData->type,
                'payment_informations' => $payment_informations,
                'url' => $url,
                'method' => "get",
            ];
            $message =  ['success' => [__('Donation Inserted Successfully')]];
            return ApiResponse::success($message, $data);


        }catch(Exception $e) {
            $error = ['error'=>[$e->getMessage()]];
            return ApiResponse::error($error);
        }
    }


    public function paystackCallback(Request $request){
        $request_data = $request->all();
        $reference =  $request_data['reference'];
        $temp_data = TemporaryData::where("identifier",$reference)->first();
        if(!$temp_data){
            $message = ['error' => [__("Transaction Failed. The record didn't save properly. Please try again")]];
            return ApiResponse::error($message);
        }

        $secret_key = $temp_data->data->credentials->secret_key??"";
        $gateway = PaymentGateway::where('id',$temp_data->data->gateway)->first();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$reference,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer $secret_key",
            "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response   = json_decode($response,true);
        return $this->successPaystack($request,$response,$gateway);
    }
    public function successPaystack(Request $request,$response,$gateway){

        try{
            $token =  PaymentGatewayApi::getToken($request->all(),$gateway->alias);
            $temp_data = TemporaryData::where("identifier",$token)->first();

            if(!$temp_data) {
                if(Transaction::where('callback_ref',$token)->exists()) {
                    $message = ['error' => [__('Transaction request sended successfully!')]];
                    return ApiResponse::error($message);
                }else {
                    $message = ['error' => [__("Transaction Failed. The record didn't save properly. Please try again")]];
                    return ApiResponse::error($message);
                }
            }

            $update_temp_data = json_decode(json_encode($temp_data->data),true);
            $update_temp_data['callback_data']  = $response;
            $temp_data->update([
                'data'  => $update_temp_data,
            ]);
            $temp_data = $temp_data->toArray();
            $creator_id = $temp_data['data']->creator_id ?? null;
            $creator_guard = $temp_data['data']->creator_guard ?? null;
            // $user = Auth::guard($creator_guard)->loginUsingId($creator_id);

            $instance = PaymentGatewayApi::init($temp_data)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('paystack');

        }catch(Exception $e) {
            dd($e);
            $message = ['error' => [$e->getMessage()]];
            return ApiResponse::error($message);
        }
        $message = ['success' => [__('Successfully Donation')]];
        return ApiResponse::onlysuccess($message);
    }


}
