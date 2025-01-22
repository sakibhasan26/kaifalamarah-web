<?php

namespace App\Traits\PaymentGateway;

use Exception;
use App\Models\Campaign;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use App\Models\TemporaryData;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use App\Http\Helpers\Api\Helpers;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use App\Constants\NotificationConst;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Constants\PaymentGatewayConst;


trait CoinGate
{
    private $coinGate_gateway_credentials;
    private $coinGate_access_token;
    private $coinGate_status_paid = "paid";

    public function coingateInit($output = null) {
        if(!$output) $output = $this->output;
        $credentials = $this->getCoinGateCredentials($output);
        $request_credentials = $this->getCoinGateRequestCredentials();
        return $this->coinGateCreateOrder($request_credentials, $output);

    }
    public function getCoinGateCredentials($output) {
        $gateway = $output['gateway'] ?? null;
        if(!$gateway) throw new Exception("Payment gateway not available");

        $production_url_sample = ['live','live url','live env','live environment', 'coin gate live url','coin gate live','production url', 'production link'];
        $production_app_token_sample = ['production token','production app token','production auth token','live token','live app token','live auth token'];
        $sandbox_url_sample = ['sandbox','sandbox url','sandbox env', 'test url', 'test', 'sandbox environment', 'coin gate sandbox url', 'coin gate sandbox' , 'coin gate test'];
        $sandbox_app_token_sample = ['sandbox token','sandbox app token','test app token','test token','test auth token','sandbox auth token'];

        $production_url = $this->getValueFromGatewayCredentials($gateway,$production_url_sample);
        $production_app_token = $this->getValueFromGatewayCredentials($gateway,$production_app_token_sample);
        $sandbox_url = $this->getValueFromGatewayCredentials($gateway,$sandbox_url_sample);
        $sandbox_app_token = $this->getValueFromGatewayCredentials($gateway,$sandbox_app_token_sample);

        $mode = $gateway->env;

        $gateway_register_mode = [
            PaymentGatewayConst::ENV_SANDBOX => "sandbox",
            PaymentGatewayConst::ENV_PRODUCTION => "production",
        ];

        if(array_key_exists($mode,$gateway_register_mode)) {
            $mode = $gateway_register_mode[$mode];
        }else {
            $mode = "sandbox";
        }

        $credentials = (object) [
            'production_url'    => $production_url,
            'production_token'  => $production_app_token,
            'sandbox_url'       => $sandbox_url,
            'sandbox_token'     => $sandbox_app_token,
            'mode'              => $mode,
        ];

        $this->coinGate_gateway_credentials = $credentials;

        return $credentials;
    }
    public function getCoinGateRequestCredentials($output = null) {
        $credentials = $this->coinGate_gateway_credentials;
        if(!$output) $output = $this->output;

        $request_credentials = [];
        if($output['gateway']->env == PaymentGatewayConst::ENV_PRODUCTION) {
            $request_credentials['url']     = $credentials->production_url;
            $request_credentials['token']   = $credentials->production_token;
        }else {
            $request_credentials['url']     = $credentials->sandbox_url;
            $request_credentials['token']   = $credentials->sandbox_token;
        }
        return (object) $request_credentials;
    }
    public function registerCoinGateEndpoints() {
        return [
            'createOrder'       => 'orders',
        ];
    }

    public function getCoinGateEndpoint($name) {
        $endpoints = $this->registerCoinGateEndpoints();
        if(array_key_exists($name,$endpoints)) {
            return $endpoints[$name];
        }
        throw new Exception("Oops! Request endpoint not registered!");
    }

    public function coinGateCreateOrder($credentials, $output) {
        $request_base_url       = $credentials->url;
        $request_access_token   = $credentials->token;

        $temp_record_token = generate_unique_string('temporary_datas','identifier',60);
        $this->setUrlParams("token=" . $temp_record_token); // set Parameter to URL for identifying when return success/cancel

        if($output['type'] == PaymentGatewayConst::TYPEADDMONEY){
            $redirection  = [
                "return_url"   => "user.add.money.coingate.payment.success",
                "cancel_url"   => "user.add.money.coingate.payment.cancel",
                "callback_url" => "add.money.payment.callback"
            ];
        }else{
            $redirection  = [
                "return_url"   => "donation.coingate.payment.success",
                "cancel_url"   => "donation.coingate.payment.cancel",
                "callback_url" => "donation.payment.callback"
            ];
        }

        $url_parameter = $this->getUrlParams();
        $endpoint = $request_base_url . "/" . $this->getCoinGateEndpoint('createOrder');

        $response = Http::withToken($request_access_token)->post($endpoint,[
            'order_id'          => Str::uuid(),
            'price_amount'      => $this->output['amount']->total_amount,
            'price_currency'    => $this->output['amount']->sender_cur_code,
            'receive_currency'  => $this->output['amount']->default_currency,
            'callback_url'      => $this->setGatewayRoute($redirection['callback_url'],PaymentGatewayConst::COINGATE,$url_parameter),
            'cancel_url'        => $this->setGatewayRoute($redirection['cancel_url'],PaymentGatewayConst::COINGATE,$url_parameter),
            'success_url'       => $this->setGatewayRoute($redirection['return_url'],PaymentGatewayConst::COINGATE,$url_parameter),
        ]);

        if($response->failed()) {
            $message = json_decode($response->body(),true);
            throw new Exception($message['message']);
        }
        if($response->successful()) {
            $response_array = json_decode($response->body(),true);

            if(isset($response_array['payment_url'])) {
                // create junk transaction
                $this->coinGateJunkInsert($this->output,$response_array,$temp_record_token);

                if(request()->expectsJson()) {
                    $this->output['redirection_response']   = $response_array;
                    $this->output['redirect_links']         = [];
                    $this->output['redirect_url']           = $response_array['payment_url'];
                    return $this->get();
                }
                return redirect()->away($response_array['payment_url']);
            }
        }

        throw new Exception("Something went wrong! Please try again");

    }
    public function coinGateJunkInsert($output,$response, $temp_identifier) {

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
                'gateway'         => $output['gateway']->id,
                'payment_type'    => $output['request_data']['payment_type'],
                'currency'        => $output['currency']->id,
                'amount'          => json_decode(json_encode($output['amount']),true),
                'response'        => $response,
                'temp_identifier' => $temp_identifier,
                'campaign_id'     => $output['request_data']['campaign_id'],
                'wallet_table'    => $wallet_table,
                'wallet_id'       => $wallet_id,
                'creator_table'   => $creator_table,
                'creator_id'      => $creator_id,
                'user_type'     => $user_type,
                'creator_guard'   => get_auth_guard(),
            ];
        }else{
            $data = [
                'gateway'         => $output['gateway']->id,
                'payment_type'    => $output['request_data']['payment_type'],
                'currency'        => $output['currency']->id,
                'amount'          => json_decode(json_encode($output['amount']),true),
                'response'        => $response,
                'temp_identifier' => $temp_identifier,
                'wallet_table'    => $wallet_table,
                'wallet_id'       => $wallet_id,
                'creator_table'   => $creator_table,
                'creator_id'      => $creator_id,
                'user_type'     => $user_type,
                'creator_guard'   => get_auth_guard(),
            ];
        }


        return TemporaryData::create([
            'type'          => PaymentGatewayConst::COINGATE,
            'identifier'    => $temp_identifier,
            'data'          => $data,
        ]);
    }
    public function coingateSuccess($output = null) {
        $reference = $output['tempData']['identifier'];

        $output['capture']      = $output['tempData']['data']->response ?? "";
        $output['callback_ref'] = $reference;

        // Search data on transaction table
        if(!$this->searchWithReferenceInTransaction($reference)) {

            // need to insert new transaction in database
            try{
                $this->createTransactionCoinGate($output, PaymentGatewayConst::STATUSPENDING);
            }catch(Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
        // if(!$this->predefined_user) {
        //     $this->removeTempDataCoinGate($output);
        // }
    }
    public function createTransactionCoinGate($output,$status = PaymentGatewayConst::STATUSSUCCESS) {
        $basic_setting = BasicSettings::first();
        if($this->predefined_user) {
            $user = $this->predefined_user;
        }else {
            $user = auth()->guard(get_auth_guard())->user();
        }
        $trx_id = generateTrxString('transactions', 'trx_id', 'AM', 8);
        $inserted_id = $this->insertRecordCoinGate($output,$trx_id,$status);
        $this->insertChargescoinGate($output,$inserted_id);
        $this->insertDeviceCoinGate($output,$inserted_id);

        if($this->requestIsApiUser()) {
            // logout user
            $api_user_login_guard = $this->output['api_login_guard'] ?? null;
            if($api_user_login_guard != null) {
                auth()->guard($api_user_login_guard)->logout();
            }
        }
    }
    public function insertRecordCoinGate($output,$trx_id,$status = PaymentGatewayConst::STATUSSUCCESS) {
        DB::beginTransaction();
        try{
            if($this->predefined_user) {
                $user = $this->predefined_user;
            }else {
                $user = auth()->guard(get_auth_guard())->user();
            }
             if($user){
                $user_id = $user->id;
                if($output['type'] == PaymentGatewayConst::TYPEDONATION){
                    // Campaign donation
                    $trx_id = generateTrxString("transactions","trx_id",'D',9);
                    $id = DB::table("transactions")->insertGetId([
                        'user_id'                       => $user_id,
                        'user_wallet_id'                => $output['wallet']->id,
                        'payment_gateway_currency_id'   => $output['currency']->id,
                        'campaign_id'                   => $output['request_data']['campaign_id'],
                        'type'                          => $output['type'],
                        'trx_id'                        => $trx_id,
                        'request_amount'                => $output['amount']->requested_amount,
                        'payable'                       => $output['amount']->total_amount,
                        'available_balance'             => $output['wallet']->balance,
                        'callback_ref'                  => $output['callback_ref'] ?? null,
                        'remark'                        => ucwords(remove_speacial_char($output['type']," ")) . " With " . $output['gateway']->name,
                        'details'                       => json_encode($output),
                        'status'                        => $status,
                        'created_at'                    => now(),
                    ]);
                    if($status === PaymentGatewayConst::STATUSSUCCESS) {
                        $this->updateCoinGateCampaign($output);
                    }
                }else{
                    if($status === PaymentGatewayConst::STATUSSUCCESS) {
                        $available_balance = $output['wallet']->balance + $output['amount']->requested_amount;
                    }else{
                        $available_balance = $output['wallet']->balance;
                    }
                    // Add money
                    $trx_id = generateTrxString("transactions","trx_id",'AM',8);
                    $id = DB::table("transactions")->insertGetId([
                        'user_id'                       => $user_id,
                        'user_wallet_id'                => $output['wallet']->id,
                        'payment_gateway_currency_id'   => $output['currency']->id,
                        'type'                          => $output['type'],
                        'trx_id'                        => $trx_id,
                        'request_amount'                => $output['amount']->requested_amount,
                        'payable'                       => $output['amount']->total_amount,
                        'available_balance'             => $available_balance,
                        'callback_ref'                  => $output['callback_ref'] ?? null,
                        'remark'                        => ucwords(remove_speacial_char($output['type']," ")) . " With " . $output['gateway']->name,
                        'details'                       => json_encode($output),
                        'status'                        => $status,
                        'created_at'                    => now(),
                    ]);
                    if($status === PaymentGatewayConst::STATUSSUCCESS) {
                        $this->updateWalletBalanceCoinGate($output);
                    }
                }
            }else{
                // Campaign donation
                $trx_id = generateTrxString("transactions","trx_id",'D',9);
                $id = DB::table("transactions")->insertGetId([
                    'payment_gateway_currency_id'   => $output['currency']->id,
                    'campaign_id'                   => $output['request_data']['campaign_id'],
                    'type'                          => $output['type'],
                    'trx_id'                        => $trx_id,
                    'request_amount'                => $output['amount']->requested_amount,
                    'payable'                       => $output['amount']->total_amount,
                    'callback_ref'                  => $output['callback_ref'] ?? null,
                    'remark'                        => ucwords(remove_speacial_char($output['type']," ")) . " With " . $output['gateway']->name,
                    'details'                       => json_encode($output),
                    'status'                        => $status,
                    'created_at'                    => now(),
                ]);
                if($status === PaymentGatewayConst::STATUSSUCCESS) {
                    $this->updateCoinGateCampaign($output);
                }
            }
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return $id;
    }
    // Update campaign
    public function updateCoinGateCampaign($output){
        $id = $output['request_data']['campaign_id'] ?? $output['transaction']->campaign_id;
        $campaign = Campaign::findOrFail($id);
        $campaign->update([
            'raised' => $campaign->raised + $output['amount']->requested_amount,
            'to_go' => $campaign->to_go - $output['amount']->requested_amount,
        ]);
    }
    public function updateWalletBalanceCoinGate($output) {
        $update_amount = $output['wallet']->balance + $output['amount']->requested_amount;
        $output['wallet']->update([
            'balance'   => $update_amount,
        ]);
    }
    public function insertChargesCoinGate($output,$id) {
        if($this->predefined_user) {
            $user = $this->predefined_user;
        }else {
            $user = auth()->guard(get_auth_guard())->user();
        }
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
    public function insertDeviceCoinGate($output,$id) {
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
    public function removeTempDataCoinGate($output) {
        TemporaryData::where("identifier",$output['tempData']['identifier'])->delete();
    }
    public static function isCoinGate($gateway) {
        $search_keyword = ['coingate','coinGate','coingate gateway','coingate crypto gateway','crypto gateway coingate'];
        $gateway_name = $gateway->name;

        $search_text = Str::lower($gateway_name);
        $search_text = preg_replace("/[^A-Za-z0-9]/","",$search_text);
        foreach($search_keyword as $keyword) {
            $keyword = Str::lower($keyword);
            $keyword = preg_replace("/[^A-Za-z0-9]/","",$keyword);
            if($keyword == $search_text) {
                return true;
                break;
            }
        }
        return false;
    }
    public function coingateCallbackResponse($reference,$callback_data, $output = null) {

        if(!$output) $output = $this->output;
        $callback_status = $callback_data['status'] ?? "";

        if(isset($output['transaction']) && $output['transaction'] != null && $output['transaction']->status != PaymentGatewayConst::STATUSSUCCESS) { // if transaction already created & status is not success
            // Just update transaction status and update user wallet if needed
            if($callback_status == $this->coinGate_status_paid) {

                $transaction_details                        = json_decode(json_encode($output['transaction']->details),true) ?? [];
                $transaction_details['gateway_response']    = $callback_data;

                // update transaction status
                DB::beginTransaction();

                try{
                    DB::table($output['transaction']->getTable())->where('id',$output['transaction']->id)->update([
                        'status'            => PaymentGatewayConst::STATUSSUCCESS,
                        'available_balance' => $output['wallet']->balance + $output['amount']->requested_amount,
                        'details'           => json_encode($transaction_details),
                        'callback_ref'      => $reference,
                    ]);

                    if($output['type'] == PaymentGatewayConst::TYPEADDMONEY){
                        $this->updateWalletBalanceCoinGate($output);
                    }else{
                        $this->updateCoinGateCampaign($output);
                    }

                    DB::commit();

                }catch(Exception $e) {
                    DB::rollBack();
                    logger($e->getMessage());
                    throw new Exception($e);
                }
            }
        }else { // need to create transaction and update status if needed

            $status = PaymentGatewayConst::STATUSPENDING;

            if($callback_status == $this->coinGate_status_paid) {
                $status = PaymentGatewayConst::STATUSSUCCESS;
            }


            $this->createTransactionCoinGate($output, $status);
        }

        logger("Transaction Created Successfully ::" . $callback_data['status']);
    }
    //for api
    public function coingateInitApi($output = null) {
        if(!$output) $output = $this->output;
        $credentials = $this->getCoinGateCredentials($output);
        $request_credentials = $this->getCoinGateRequestCredentials();
        return $this->coinGateCreateOrderApi($request_credentials, $output);

    }
    public function coinGateCreateOrderApi($credentials, $output) {
        $request_base_url       = $credentials->url;
        $request_access_token   = $credentials->token;

        $temp_record_token = generate_unique_string('temporary_datas','identifier',60);
        $this->setUrlParams("token=" . $temp_record_token); // set Parameter to URL for identifying when return success/cancel


        if($output['type'] == PaymentGatewayConst::TYPEADDMONEY){
            $redirection  = [
                "return_url"   => "api.v1.user.add-money.coingate.payment.success",
                "cancel_url"   => "api.v1.user.add-money.coingate.payment.cancel",
                "callback_url" => "add.money.payment.callback",
            ];
        }else{
            $redirection  = [
                "return_url"   => "api.v1.user.donation.coingate.payment.success",
                "cancel_url"   => "api.v1.user.donation.coingate.payment.cancel",
                "callback_url" => "donation.payment.callback"
            ];
        }

        $url_parameter = $this->getUrlParams();
        $endpoint = $request_base_url . "/" . $this->getCoinGateEndpoint('createOrder');

        $response = Http::withToken($request_access_token)->post($endpoint,[
            'order_id'          => Str::uuid(),
            'price_amount'      => $this->output['amount']->total_amount,
            'price_currency'    => $this->output['amount']->sender_cur_code,
            'receive_currency'  => $this->output['amount']->default_currency,
            'callback_url'      => $this->setGatewayRoute($redirection['callback_url'],PaymentGatewayConst::COINGATE,$url_parameter."&r-source=".PaymentGatewayConst::APP),
            'cancel_url'        => $this->setGatewayRoute($redirection['cancel_url'],PaymentGatewayConst::COINGATE,$url_parameter."&r-source=".PaymentGatewayConst::APP),
            'success_url'       => $this->setGatewayRoute($redirection['return_url'],PaymentGatewayConst::COINGATE,$url_parameter."&r-source=".PaymentGatewayConst::APP),
        ]);

        if($response->failed()) {
            $message = json_decode($response->body(),true);
            $error = ['error'=>[$message['message']]];
            return Helpers::error($error);
        }
        if($response->successful()) {
            $response_array = json_decode($response->body(),true);

            if(isset($response_array['payment_url'])) {

                // create junk transaction
                $this->coinGateJunkInsert($this->output,$response_array,$temp_record_token);
                if(request()->expectsJson()) {
                    $this->output['redirection_response']   = $response_array;
                    $this->output['redirect_links']         = [];
                    $this->output['redirect_url']           = $response_array['payment_url'];
                    // return $this->get();
                }
                $data['link'] =  $response_array['payment_url'];
                $data['trx'] =  $temp_record_token;

                return $data;
            }
        }
        $error = ['error'=>["Something went wrong! Please try again"]];
        return Helpers::error($error);


    }

}
