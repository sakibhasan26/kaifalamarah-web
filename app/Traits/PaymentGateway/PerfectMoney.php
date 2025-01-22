<?php

namespace App\Traits\PaymentGateway;

use Exception;
use Carbon\Carbon;
use App\Models\Campaign;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use App\Models\TemporaryData;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use App\Constants\NotificationConst;
use App\Http\Helpers\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use App\Providers\Admin\BasicSettingsProvider;
use App\Notifications\User\AddMoney\ApprovedMail;

trait PerfectMoney {


    private $perfect_money_credentials;
    private $perfect_money_request_credentials;

    public function perfectMoneyInit($output = null)
    {
        if(!$output) $output = $this->output;
        $gateway_credentials = $this->perfectMoneyGatewayCredentials($output['gateway']);
        $request_credentials = $this->perfectMoneyRequestCredentials($gateway_credentials, $output['gateway'], $output['currency']);
        $output['request_credentials'] = $request_credentials;

        if($gateway_credentials->passphrase == "") {
            throw new Exception("You must set Alternate Passphrase under Settings section in your Perfect Money account before starting receiving payment confirmations.");
        }

        // need to insert junk for temporary data
        $temp_record        = $this->perfectMoneyJunkInsert($output, 'web');
        $temp_identifier    = $temp_record->identifier;

        $link_for_redirect_form = route('donation.payment.redirect.form', [PaymentGatewayConst::PERFECT_MONEY, 'token' => $temp_identifier]);

        return redirect()->away($link_for_redirect_form);
    }

    public function perfectMoneyInitApi($output = null)
    {
        if(!$output) $output = $this->output;
        $gateway_credentials = $this->perfectMoneyGatewayCredentials($output['gateway']);
        $request_credentials = $this->perfectMoneyRequestCredentials($gateway_credentials, $output['gateway'], $output['currency']);
        $output['request_credentials'] = $request_credentials;

        if($gateway_credentials->passphrase == "") {
            throw new Exception("You must set Alternate Passphrase under Settings section in your Perfect Money account before starting receiving payment confirmations.");
        }

        // need to insert junk for temporary data
        $temp_record        = $this->perfectMoneyJunkInsert($output, 'api');
        $temp_identifier    = $temp_record->identifier;
        $link_for_redirect_form = route('donation.payment.redirect.form', [PaymentGatewayConst::PERFECT_MONEY, 'token' => $temp_identifier]);

        $this->output['redirection_response']   = [];
        $this->output['redirect_links']         = [];
        $this->output['temp_identifier']        = $temp_identifier;
        $this->output['redirect_url']           = $link_for_redirect_form;
        return $this->get();
    }

    /**
     * Get payment gateway credentials for both sandbox and production
     */
    public function perfectMoneyGatewayCredentials($gateway)
    {

        if(!$gateway) throw new Exception("Oops! Payment Gateway Not Found!");

        $usd_account_sample     = ['usd account','usd','usd wallet','account usd'];
        $eur_account_sample     = ['eur account','eur','eur wallet', 'account eur'];
        $pass_phrase_sample     = ['alternate passphrase' ,'passphrase', 'perfect money alternate passphrase', 'alternate passphrase perfect money' , 'alternate phrase' , 'alternate pass'];

        $usd_account            = PaymentGateway::getValueFromGatewayCredentials($gateway,$usd_account_sample);
        $eur_account            = PaymentGateway::getValueFromGatewayCredentials($gateway,$eur_account_sample);
        $pass_phrase            = PaymentGateway::getValueFromGatewayCredentials($gateway,$pass_phrase_sample);

        $credentials = (object) [
            'usd_account'   => $usd_account,
            'eur_account'   => $eur_account,
            'passphrase'    => $pass_phrase, // alternate passphrase
        ];

        $this->perfect_money_credentials = $credentials;

        return $credentials;
    }

    /**
     * Get payment gateway credentials for making api request
     */
    public function perfectMoneyRequestCredentials($gateway_credentials, $payment_gateway, $gateway_currency)
    {

        if($gateway_currency->currency_code == "EUR") {
            $request_credentials = [
                'account'   => $gateway_credentials->eur_account
            ];
        }else if($gateway_currency->currency_code == "USD") {
            $request_credentials = [
                'account'   => $gateway_credentials->usd_account
            ];
        }

        $request_credentials = (object) $request_credentials;

        $this->perfect_money_request_credentials = $request_credentials;

        return $request_credentials;
    }

    public function perfectMoneyJunkInsert($output, $guard)
    {

        $action_type = PaymentGatewayConst::REDIRECT_USING_HTML_FORM;

        $payment_id = Str::uuid() . '-' . time();
        $this->setUrlParams("token=" . $payment_id); // set Parameter to URL for identifying when return success/cancel

        $redirect_form_data = $this->makingPerfectMoneyRedirectFormData($output, $payment_id,$guard);

        $form_action_url    = "https://perfectmoney.com/api/step1.asp";
        $form_method        = "POST";

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
                'gateway'            => $output['gateway']->id,
                'currency'           => $output['currency']->id,
                'campaign_id'        => $output['request_data']['campaign_id'],
                'amount'             => json_decode(json_encode($output['amount']),true),
                'creator_guard'      => get_auth_guard(),
                'action_type'        => $action_type,
                'redirect_form_data' => $redirect_form_data,
                'action_url'         => $form_action_url,
                'form_method'        => $form_method,
                'payment_type'       => $output['request_data']['payment_type'],
                'wallet_table'       => $wallet_table,
                'wallet_id'          => $wallet_id,
                'creator_table'      => $creator_table,
                'creator_id'         => $creator_id,
                'user_type'          => $user_type,
                'authenticated'      => auth()->guard(get_auth_guard())->check(),
            ];
        }else {
            $data = [
                'action_type'        => $action_type,
                'redirect_form_data' => $redirect_form_data,
                'action_url'         => $form_action_url,
                'form_method'        => $form_method,
                'gateway'            => $output['gateway']->id,
                'payment_type'       => $output['request_data']['payment_type'],
                'currency'           => $output['currency']->id,
                'amount'             => json_decode(json_encode($output['amount']),true),
                'wallet_table'       => $wallet_table,
                'wallet_id'          => $wallet_id,
                'creator_table'      => $creator_table,
                'creator_id'         => $creator_id,
                'user_type'          => $user_type,
                'creator_guard'      => get_auth_guard(),
                'authenticated'      => auth()->guard(get_auth_guard())->check(),
            ];
        }



        return TemporaryData::create([
            'user_id'       => Auth::id(),
            'type'          => PaymentGatewayConst::PERFECT_MONEY,
            'identifier'    => $payment_id,
            'data'          => $data,
        ]);
    }

    public function makingPerfectMoneyRedirectFormData($output, $payment_id, $guard)
    {

        $basic_settings = BasicSettingsProvider::get();

        $url_parameter = $this->getUrlParams();

        $total_amount = number_format($output['amount']->total_amount, 2, '.', '');

        if($guard == 'web'){
            if($output['type'] == PaymentGatewayConst::TYPEDONATION){
                $redirection = [
                    'callback' => 'donation.payment.callback',
                    'success'  => 'donation.perfect.payment.success',
                    'cancel'   => 'donation.perfect.payment.cancel',
                ];
            }else {
                $redirection = [
                    'callback' => 'user.add.money.payment.callback',
                    'success'  => 'user.add.money.perfect.payment.success',
                    'cancel'   => 'user.add.money.perfect.payment.cancel',
                ];
            }
        }else{
            if ($output['type'] == PaymentGatewayConst::TYPEDONATION) {
                $redirection = [
                    'callback' => 'donation.payment.callback',
                    'success'  => 'api.v1.user.donation.perfect.success',
                    'cancel'   => 'api.v1.user.donation.perfect.cancel',
                ];
            }else{
                $redirection = [
                    'callback' => 'add.money.payment.callback',
                    'success'  => 'api.v1.user.add-money.perfect.success',
                    'cancel'   => 'api.v1.user.add-money.perfect.cancel',
                ];
            }
        }

        return [
            [
                'name'  => 'PAYEE_ACCOUNT',
                'value' => $output['request_credentials']->account,
            ],
            [
                'name'  => 'PAYEE_NAME',
                'value' => $basic_settings->site_name,
            ],
            [
                'name'  => 'PAYMENT_AMOUNT',
                'value' => $total_amount,
            ],
            [
                'name'  => 'PAYMENT_UNITS',
                'value' => $output['currency']->currency_code,
            ],
            [
                'name'  => 'PAYMENT_ID',
                'value' => $payment_id,
            ],
            [
                'name'  => 'STATUS_URL',
                'value' => $this->setGatewayRoute($redirection['callback'],PaymentGatewayConst::PERFECT_MONEY,$url_parameter),
            ],
            [
                'name'  => 'PAYMENT_URL',
                'value' => $this->setGatewayRoute($redirection['success'],PaymentGatewayConst::PERFECT_MONEY,$url_parameter),
            ],
            [
                'name'  => 'PAYMENT_URL_METHOD',
                'value' => 'GET',
            ],
            [
                'name'  => 'NOPAYMENT_URL',
                'value' => $this->setGatewayRoute($redirection['cancel'],PaymentGatewayConst::PERFECT_MONEY,$url_parameter),
            ],
            [
                'name'  => 'NOPAYMENT_URL_METHOD',
                'value' => 'GET',
            ],
            [
                'name'  => 'BAGGAGE_FIELDS',
                'value' => '',
            ],
            [
                'name'  => 'INTERFACE_LANGUAGE',
                'value' => 'en_US',
            ],
            [
                'name'  => 'r-source',
                'value' => 'APP',
            ],
        ];
    }

    public function isPerfectMoney($gateway)
    {
        $search_keyword = ['perfectmoney','perfect money','perfect-money','perfect money gateway', 'perfect money payment gateway'];
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

    public function getPerfectMoneyAlternatePassphrase($gateway)
    {
        $gateway_credentials = $this->perfectMoneyGatewayCredentials($gateway);
        return $gateway_credentials->passphrase;
    }

    public function perfectmoneySuccess($output) {
      
        $reference              = $output['tempData']['identifier'];
        $output['capture']      = $output['tempData']['data']->callback_data ?? "";
        $output['callback_ref'] = $reference;

        $pass_phrase = strtoupper(md5($this->getPerfectMoneyAlternatePassphrase($output['gateway'])));

        if($output['capture'] != "") {

            $concat_string = $output['capture']->PAYMENT_ID . ":" . $output['capture']->PAYEE_ACCOUNT . ":" . $output['capture']->PAYMENT_AMOUNT . ":" . $output['capture']->PAYMENT_UNITS . ":" . $output['capture']->PAYMENT_BATCH_NUM . ":" . $output['capture']->PAYER_ACCOUNT . ":" . $pass_phrase . ":" . $output['capture']->TIMESTAMPGMT;

            $md5_string = strtoupper(md5($concat_string));

            $v2_hash = $output['capture']->V2_HASH;

            if($md5_string == $v2_hash) {
                // this transaction is success
                if(!$this->searchWithReferenceInTransaction($reference)) {
                    // need to insert new transaction in database
                    try{
                        $this->createTransactionPerfect($output,PaymentGatewayConst::STATUSPENDING);
                    }catch(Exception $e) {
                        throw new Exception($e->getMessage());
                    }
                }
            }
        }


    }

    public function createTransactionPerfect($output,$status = PaymentGatewayConst::STATUSSUCCESS){

        $basic_setting = BasicSettings::first();
        if($this->predefined_user) {
            $user = $this->predefined_user;
        }else {
            $user = auth()->guard(get_auth_guard())->user();
        }
        $trx_id ='D'.getTrxNum();
        $inserted_id = $this->insertRecordPerfect($output,$trx_id,$status);
        $this->insertChargesPerfectMoney($output,$inserted_id);
        $this->insertDevicePerfectMoney($output,$inserted_id);

        if($this->requestIsApiUser()) {
            // logout user
            $api_user_login_guard = $this->output['api_login_guard'] ?? null;
            if($api_user_login_guard != null) {
                auth()->guard($api_user_login_guard)->logout();
            }
        }

        // if($basic_setting->email_notification == true){
        //     $user->notify(new ApprovedMail($user,$output,$trx_id));
        // }
    }

    public function insertRecordPerfect($output,$trx_id,$status = PaymentGatewayConst::STATUSSUCCESS) {
        try{

            $auth_check = $output['tempData']['data']->user_type;
            if ($auth_check == "authenticated") {
                if($this->predefined_user) {
                    $user = $this->predefined_user;
                }else {
                    $user = auth()->guard(get_auth_guard())->user();
                }
            }else {
                $user= null;
            }

                if($output['request_data']['payment_type'] == PaymentGatewayConst::TYPEDONATION){
                    if($auth_check == "authenticated"){

                        // Campaign donation for authenticate
                        $trx_id = generateTrxString("transactions","trx_id",'D',9);
                        $id = DB::table("transactions")->insertGetId([
                            'user_id'                       => $user->id??null,
                            'user_wallet_id'                => $output['wallet']->id,
                            'payment_gateway_currency_id'   => $output['currency']->id,
                            'campaign_id'                   => $output['tempData']['data']->campaign_id,
                            'type'                          => $output['type'],
                            'trx_id'                        => $trx_id,
                            'request_amount'                => $output['amount']->requested_amount,
                            'payable'                       => $output['amount']->total_amount,
                            'available_balance'             => $output['wallet']->balance,
                            'remark'                        => ucwords(remove_speacial_char($output['type']," ")) . " With " . $output['gateway']->name,
                            'details'                       => json_encode($output['capture']),
                            'status'                        => $status,
                            'callback_ref'                  => $output['callback_ref'],
                            'created_at'                    => now(),
                        ]);

                        if($status === PaymentGatewayConst::STATUSSUCCESS) {
                            $this->updateCampaignPerfect($output);
                        }

                    }else{
                        // Campaign donation Unauthenticate

                        $trx_id = generateTrxString("transactions","trx_id",'D',9);
                        $id = DB::table("transactions")->insertGetId([
                            'payment_gateway_currency_id'   => $output['currency']->id,
                            'type'                          => $output['type'],
                            'campaign_id'                   => $output['tempData']['data']->campaign_id,
                            'trx_id'                        => $trx_id,
                            'request_amount'                => $output['amount']->requested_amount,
                            'payable'                       => $output['amount']->total_amount,
                            'remark'                        => ucwords(remove_speacial_char($output['type']," ")) . " With " . $output['gateway']->name,
                            'details'                       => json_encode($output['capture']),
                            'status'                        => $status,
                            'callback_ref'                  => $output['callback_ref'],
                            'created_at'                    => now(),
                        ]);
                        if($status === PaymentGatewayConst::STATUSSUCCESS) {
                            $this->updateCampaignPerfect($output);
                        }
                    }
                }else{

                    // Add money
                    $trx_id = generateTrxString("transactions","trx_id",'AM',8);
                    $id = DB::table("transactions")->insertGetId([
                        'user_id'                       => $user->id??null,
                        'user_wallet_id'                => $output['wallet']->id,
                        'payment_gateway_currency_id'   => $output['currency']->id,
                        'type'                          => $output['type'],
                        'trx_id'                        => $trx_id,
                        'request_amount'                => $output['amount']->requested_amount,
                        'payable'                       => $output['amount']->total_amount,
                        'available_balance'             => $output['wallet']->balance + $output['amount']->requested_amount,
                        'remark'                        => ucwords(remove_speacial_char($output['type']," ")) . " With " . $output['gateway']->name,
                        'details'                       => json_encode($output['capture']),
                        'status'                        => $status,
                        'callback_ref'                  => $output['callback_ref'],
                        'created_at'                    => now(),
                    ]);
                    if($status === PaymentGatewayConst::STATUSSUCCESS) {
                        $this->updateWalletBalancePerfect($output);
                    }
                }
            DB::commit();
        }catch(Exception $e) {

            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return $id;
    }

    public function updateCampaignPerfect($output) {
        $id = $output['request_data']['campaign_id'] ?? $output['transaction']->campaign_id;
        $campaign = Campaign::findOrFail($id);
        $campaign->update([
            'raised' => $campaign->raised + $output['amount']->requested_amount,
            'to_go' => $campaign->to_go - $output['amount']->requested_amount,
        ]);
    }

    public function updateWalletBalancePerfect($output) {

        $update_amount = $output['wallet']->balance + $output['amount']->requested_amount;
        $output['wallet']->update([
            'balance'   => $update_amount,
        ]);
    }

    public function insertChargesPerfectMoney($output,$id) {
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
                       'user_id' => $user->id,
                       'message' => $notification_content,
                   ]);
              }
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception(__('Something went wrong! Please try again'));
        }
    }
    public function insertDevicePerfectMoney($output,$id) {
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
            throw new Exception(__('Something went wrong! Please try again'));
        }
    }

    public function removeTempDataPerfect($output) {
        TemporaryData::where("identifier",$output['tempData']['identifier'])->delete();
    }


    public function perfectmoneyCallbackResponse($reference,$callback_data, $output = null) {

        if(!$output) $output = $this->output;
        $pass_phrase = strtoupper(md5($this->getPerfectMoneyAlternatePassphrase($output['gateway'])));

        if(is_array($callback_data) && count($callback_data) > 0) {
            $concat_string = $callback_data['PAYMENT_ID'] . ":" . $callback_data['PAYEE_ACCOUNT'] . ":" . $callback_data['PAYMENT_AMOUNT'] . ":" . $callback_data['PAYMENT_UNITS'] . ":" . $callback_data['PAYMENT_BATCH_NUM'] . ":" . $callback_data['PAYER_ACCOUNT'] . ":" . $pass_phrase . ":" . $callback_data['TIMESTAMPGMT'];

            $md5_string = strtoupper(md5($concat_string));
            $v2_hash = $callback_data['V2_HASH'];

            if($md5_string != $v2_hash) {
                return false;
                logger("Transaction hash did not match. ref: $reference", [$callback_data]);
            }
        }else {
            return false;
            logger("Invalid callback data. ref: $reference", [$callback_data]);
        }


        if(isset($output['transaction']) && $output['transaction'] != null && $output['transaction']->status != PaymentGatewayConst::STATUSSUCCESS) { // if transaction already created & status is not success
            // Just update transaction status and update user wallet if needed
            $transaction_details                        = json_decode(json_encode($output['transaction']->details),true) ?? [];
            $transaction_details['gateway_response']    = $callback_data;

            // update transaction status
            DB::beginTransaction();

            try{
                DB::table($output['transaction']->getTable())->where('id',$output['transaction']->id)->update([
                    'status'        => PaymentGatewayConst::STATUSSUCCESS,
                    'details'       => json_encode($transaction_details),
                    'callback_ref'  => $reference,
                ]);


                if($output['type'] == PaymentGatewayConst::TYPEADDMONEY){
                    $this->updateWalletBalancePerfect($output);
                }else{
                    $this->updateCampaignPerfect($output);
                }

                DB::commit();

            }catch(Exception $e) {
                DB::rollBack();

                throw new Exception($e);
            }
        }else { // need to create transaction and update status if needed

            $status = PaymentGatewayConst::STATUSSUCCESS;

            $this->createTransactionPerfect($output, $status);
        }

        logger("Transaction Created Successfully! ref: " . $reference);
    }
}
