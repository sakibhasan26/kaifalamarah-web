<?php

namespace App\Traits\PaymentGateway;

use Exception;
use App\Models\Campaign;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe as StripePackage;
use App\Constants\NotificationConst;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use App\Providers\Admin\BasicSettingsProvider;
use App\Http\Helpers\Api\Helpers as ApiResponse;

trait Stripe
{

    public function stripeInit($output = null) {
        $basic_settings = BasicSettingsProvider::get();
        if(!$output) $output = $this->output;
        $credentials = $this->getStripeCredetials($output);
        $reference = generateTransactionReference();
        $amount = $output['amount']->total_amount ? number_format($output['amount']->total_amount,2,'.','') : 0;
        $currency = $output['currency']['currency_code']??"USD";

        if($output['request_data']['payment_type'] == PaymentGatewayConst::TYPEDONATION){
            $return_url = route('donation.stripe.payment.success', $reference);
        }else{
            $return_url = route('user.add.money.stripe.payment.success', $reference);
        }

        if(auth()->guard(get_auth_guard())->check()){
            $user = auth()->guard(get_auth_guard())->user();
            $user_email = $user->email;
            $user_phone = $user->full_mobile ?? '';
            $user_name  = $user->firstname.' '.$user->lastname ?? '';
        }else{
            $user_email = '';
            $user_phone = '';
            $user_name  = '';
        }

        $data = [
            'payment_options' => 'card',
            'amount'          => $amount,
            'email'           => $user_email,
            'tx_ref'          => $reference,
            'currency'        =>  $currency,
            'redirect_url'    => $return_url,
            'customer'        => [
                'email'        => $user_email,
                "phone_number" => $user_phone,
                "name"         => $user_name
            ],
            "customizations" => [
                "title"       => "Add Money",
                "description" => dateFormat('d M Y', Carbon::now()),
            ]
        ];

        //start stripe pay link
        $stripe = new \Stripe\StripeClient($credentials->secret_key);

        //create product for Product Id
        try{
            $product_id = $stripe->products->create([
                'name' => 'Add Money( '.$basic_settings->site_name.' )',
            ]);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
        //create price for Price Id
        try{
            $price_id =$stripe->prices->create([
                'currency' =>  $currency,
                'unit_amount' => $amount * 100,
                'product' => $product_id->id??""
            ]);
        }catch(Exception $e){
            throw new Exception("Something Is Wrong, Please Contact With Owner");
        }

        //create payment live links
        try{
            $payment_link = $stripe->paymentLinks->create([
                'line_items' => [
                    [
                        'price' => $price_id->id,
                    'quantity' => 1,
                ],
                ],
                'after_completion' => [
                    'type' => 'redirect',
                    'redirect' => ['url' => $return_url],
                ],
            ]);
        }catch(Exception $e){
            throw new Exception("Something Is Wrong, Please Contact With Owner");
        }

        $this->stripeJunkInsert($data);

        return redirect($payment_link->url."?prefilled_email=".@$user->email);
    }

    public function getStripeCredetials($output) {
        $gateway = $output['gateway'] ?? null;
        if(!$gateway) throw new Exception("Payment gateway not available");
        $client_id_sample = ['publishable_key','publishable key','publishable-key'];
        $client_secret_sample = ['secret id','secret-id','secret_id', 'secret-key', 'secret key'];

        $client_id = '';
        $outer_break = false;
        foreach($client_id_sample as $item) {
            if($outer_break == true) {
                break;
            }
            $modify_item = $this->stripePlainText($item);
            foreach($gateway->credentials ?? [] as $gatewayInput) {
                $label = $gatewayInput->label ?? "";
                $label = $this->stripePlainText($label);

                if($label == $modify_item) {
                    $client_id = $gatewayInput->value ?? "";
                    $outer_break = true;
                    break;
                }
            }
        }


        $secret_id = '';
        $outer_break = false;
        foreach($client_secret_sample as $item) {
            if($outer_break == true) {
                break;
            }
            $modify_item = $this->stripePlainText($item);
            foreach($gateway->credentials ?? [] as $gatewayInput) {
                $label = $gatewayInput->label ?? "";
                $label = $this->stripePlainText($label);

                if($label == $modify_item) {
                    $secret_id = $gatewayInput->value ?? "";
                    $outer_break = true;
                    break;
                }
            }
        }

        return (object) [
            'publish_key'     => $client_id,
            'secret_key' => $secret_id,

        ];

    }

    public function stripePlainText($string) {
        $string = Str::lower($string);
        return preg_replace("/[^A-Za-z0-9]/","",$string);
    }

    public function stripeJunkInsert($response) {

        $output = $this->output;

        $user = auth()->guard(get_auth_guard())->user();
        $creator_table = $creator_id = $wallet_table = $wallet_id = null;
        if($user != null) {
            $creator_table = auth()->guard(get_auth_guard())->user()->getTable();
            $creator_id = auth()->guard(get_auth_guard())->user()->id;
            $wallet_table = $output['wallet']->getTable();
            $wallet_id = $output['wallet']->id;
        }

        if (Auth::guard(get_auth_guard())->check()) {
            $user_type = PaymentGatewayConst::AUTHENTICATED;
        } else {
            $user_type = PaymentGatewayConst::GUEST;
        }

        if($output['request_data']['payment_type'] == PaymentGatewayConst::TYPEDONATION){
            $data = [
                'gateway'     => $output['gateway']->id,
                'payment_type'=> $output['request_data']['payment_type'],
                'currency'    => $output['currency']->id,
                'amount'      => json_decode(json_encode($output['amount']),true),
                'response'    => $response,
                'campaign_id' => $output['request_data']['campaign_id'],
                'wallet_table'  => $wallet_table,
                'wallet_id'     => $wallet_id,
                'creator_table' => $creator_table,
                'creator_id'    => $creator_id,
                'user_type'     => $user_type,
                'creator_guard' => get_auth_guard(),
            ];
        }else{
            $data = [
                'gateway'     => $output['gateway']->id,
                'payment_type'=> $output['request_data']['payment_type'],
                'currency'    => $output['currency']->id,
                'amount'      => json_decode(json_encode($output['amount']),true),
                'response'    => $response,
                'wallet_table'  => $wallet_table,
                'wallet_id'     => $wallet_id,
                'creator_table' => $creator_table,
                'creator_id'    => $creator_id,
                'user_type'     => $user_type,
                'creator_guard' => get_auth_guard(),
            ];
        }
        return TemporaryData::create([
            'type'          => PaymentGatewayConst::STRIPE,
            'identifier'    => $response['tx_ref'],
            'data'          => $data,
        ]);
    }
    public function stripeSuccess($output = null) {
        if(!$output) $output = $this->output;
        $token = $this->output['tempData']['identifier'] ?? "";
        if(empty($token)) throw new Exception('Transaction failed. Record didn\'t saved properly. Please try again.');
        $trx_id = generateTrxString('transactions', 'trx_id', 'AM', 8);
        return $this->createTransactionStripe($output, $trx_id);
    }

    public function createTransactionStripe($output, $trx = NULL) {
        $inserted_id = $this->insertRecordStripe($output,$trx);
        $this->insertChargesStripe($output,$inserted_id);
        $this->insertDeviceStripe($output,$inserted_id);
        $this->removeTempDataStripe($output);
    }

    public function insertRecordStripe($output, $trx) {
        $token = $this->output['tempData']['identifier'] ?? "";
        DB::beginTransaction();
        try{
            if(Auth::guard(get_auth_guard())->check()){
                $user_id = Auth::guard(get_auth_guard())->user()->id;
                if($output['request_data']['payment_type'] == PaymentGatewayConst::TYPEDONATION){
                    // Campaign donation
                    $trx_id = generateTrxString("transactions","trx_id",'D',9);
                    $id = DB::table("transactions")->insertGetId([
                        'user_id'                       => $user_id,
                        'user_wallet_id'                => $output['wallet']->id,
                        'payment_gateway_currency_id'   => $output['currency']->id,
                        'campaign_id'                   => $output['request_data']['campaign_id'],
                        'type'                          => $output['request_data']['payment_type'],
                        'trx_id'                        => $trx_id,
                        'request_amount'                => $output['amount']->requested_amount,
                        'payable'                       => $output['amount']->total_amount,
                        'available_balance'             => $output['wallet']->balance,
                        'remark'                        => ucwords(remove_speacial_char($output['request_data']['payment_type']," ")) . " With " . $output['gateway']->name,
                        'details'                       => "strip payment successfull",
                        'status'                        => true,
                        'created_at'                    => now(),
                    ]);
                    $this->updateStripeCampaign($output);
                }else{
                    // Add money
                    $trx_id = generateTrxString("transactions","trx_id",'AM',8);
                    $id = DB::table("transactions")->insertGetId([
                        'user_id'                       => $user_id,
                        'user_wallet_id'                => $output['wallet']->id,
                        'payment_gateway_currency_id'   => $output['currency']->id,
                        'type'                          => $output['request_data']['payment_type'],
                        'trx_id'                        => $trx_id,
                        'request_amount'                => $output['amount']->requested_amount,
                        'payable'                       => $output['amount']->total_amount,
                        'available_balance'             => $output['wallet']->balance + $output['amount']->requested_amount,
                        'remark'                        => ucwords(remove_speacial_char($output['request_data']['payment_type']," ")) . " With " . $output['gateway']->name,
                        'details'                       => "strip payment successfull",
                        'status'                        => true,
                        'created_at'                    => now(),
                    ]);
                    $this->updateWalletBalanceStripe($output);
                }
            }else{
                // Campaign donation

                $trx_id = generateTrxString("transactions","trx_id",'D',9);
                $id = DB::table("transactions")->insertGetId([
                    'payment_gateway_currency_id'   => $output['currency']->id,
                    'campaign_id'                   => $output['request_data']['campaign_id'],
                    'type'                          => $output['request_data']['payment_type'],
                    'trx_id'                        => $trx_id,
                    'request_amount'                => $output['amount']->requested_amount,
                    'payable'                       => $output['amount']->total_amount,
                    'remark'                        => ucwords(remove_speacial_char($output['request_data']['payment_type']," ")) . " With " . $output['gateway']->name,
                    'details'                       => "strip payment successfull",
                    'status'                        => true,
                    'created_at'                    => now(),
                ]);
                $this->updateStripeCampaign($output);
            }
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return $id;
    }

    // Update campaign
    public function updateStripeCampaign($output){
        $id = $output['request_data']['campaign_id'];
        $campaign = Campaign::findOrFail($id);
        $campaign->update([
            'raised' => $campaign->raised + $output['amount']->requested_amount,
            'to_go' => $campaign->to_go - $output['amount']->requested_amount,
        ]);
    }

    public function updateWalletBalanceStripe($output) {
        $update_amount = $output['wallet']->balance + $output['amount']->requested_amount;

        $output['wallet']->update([
            'balance'   => $update_amount,
        ]);
    }

    public function insertChargesStripe($output,$id) {
        DB::beginTransaction();
        try{
            DB::table('transaction_charges')->insert([
                'transaction_id'    => $id,
                'percent_charge'    => $output['amount']->percent_charge,
                'fixed_charge'      => $output['amount']->fixed_charge,
                'total_charge'      => $output['amount']->total_charge,
                'created_at'        => now(),
            ]);
            DB::commit();



           if(Auth::guard(get_auth_guard())->check()){
             //notification
                $notification_content = [
                    'title'         => "Add Money",
                    'message'       => "Your Wallet (".$output['wallet']->currency->code.") balance  has been added ".$output['amount']->requested_amount.' '. $output['wallet']->currency->code,
                    'time'          => Carbon::now()->diffForHumans(),
                    'image'         => files_asset_path('profile-default'),
                ];

                UserNotification::create([
                    'type'    => NotificationConst::BALANCE_ADDED,
                    'user_id' => Auth::guard(get_auth_guard())->user()->id,
                    'message' => $notification_content,
                ]);
           }
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function insertDeviceStripe($output,$id) {
        $client_ip = request()->ip() ?? false;
        $location = geoip()->getLocation($client_ip);
        $agent = new Agent();

        // $mac = exec('getmac');
        // $mac = explode(" ",$mac);
        // $mac = array_shift($mac);
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

    public function removeTempDataStripe($output) {
        $token = session()->get('identifier');
        TemporaryData::where("identifier",$token)->delete();
    }

    // **************** For API *****************

    public function stripeInitApi($output = null) {
        $basic_settings = BasicSettingsProvider::get();
        if(!$output) $output = $this->output;
        $credentials = $this->getStripeCredetials($output);
        $reference = generateTransactionReference();
        $amount = $output['amount']->total_amount ? number_format($output['amount']->total_amount,2,'.','') : 0;
        $currency = $output['currency']['currency_code']??"USD";

        if($output['request_data']['payment_type'] == PaymentGatewayConst::TYPEDONATION){
            $return_url = route('api.v1.user.donation.stripe.payment.success', $reference);
        }else{
            $return_url = route('api.v1.user.add-money.stripe.payment.success', $reference."?r-source=".PaymentGatewayConst::APP);
        }

        if(auth()->guard(get_auth_guard())->check()){
            $user = auth()->guard(get_auth_guard())->user();
            $user_email = $user->email;
            $user_phone = $user->full_mobile ?? '';
            $user_name  = $user->firstname.' '.$user->lastname ?? '';
        }else{
            $user_email = '';
            $user_phone = '';
            $user_name  = '';
        }

        $data = [
            'payment_options' => 'card',
            'amount'          => $amount,
            'email'           => $user_email,
            'tx_ref'          => $reference,
            'currency'        =>  $currency,
            'redirect_url'    => $return_url,
            'customer'        => [
                'email'        => $user_email,
                "phone_number" => $user_phone,
                "name"         => $user_name
            ],
            "customizations" => [
                "title"       => "Add Money",
                "description" => dateFormat('d M Y', Carbon::now()),
            ]
        ];

       //start stripe pay link
       $stripe = new \Stripe\StripeClient($credentials->secret_key);

       //create product for Product Id
       try{
            $product_id = $stripe->products->create([
                'name' => 'Add Money( '.$basic_settings->site_name.' )',
            ]);
       }catch(Exception $e){
            $error = ['error'=>[$e->getMessage()]];
            return ApiResponse::error($error);
       }
       //create price for Price Id
       try{
            $price_id =$stripe->prices->create([
                'currency' =>  $currency,
                'unit_amount' => $amount*100,
                'product' => $product_id->id??""
              ]);
       }catch(Exception $e){
            $error = ['error'=>["Something Is Wrong, Please Contact With Owner"]];
            return ApiResponse::error($error);
       }
       //create payment live links
       try{
            $payment_link = $stripe->paymentLinks->create([
                'line_items' => [
                [
                    'price' => $price_id->id,
                    'quantity' => 1,
                ],
                ],
                'after_completion' => [
                'type' => 'redirect',
                'redirect' => ['url' => $return_url],
                ],
            ]);
        }catch(Exception $e){
            $error = ['error'=>["Something Is Wrong, Please Contact With Owner"]];
            return ApiResponse::error($error);
        }
        $data['link'] =  $payment_link->url;
        $data['trx'] =  $reference;

        $this->stripeJunkInsert($data);
        return $data;

    }

}
