<?php

namespace App\Traits\PaymentGateway;

use Exception;
use App\Models\Campaign;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Constants\NotificationConst;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Session;


trait QrpayTrait
{
    public function qrpayInit($output = null)
    {
        if (!$output) $output = $this->output;

        $request_data = $this->request_data;
        $credentials = $this->getQrpayCredetials($output);

        $access = $this->accessTokenQrpay($credentials);
        $identifier = generate_unique_string("transactions", "trx_id", 16);

        $this->QrpayJunkInsert($identifier);

        if($output['request_data']['payment_type'] == PaymentGatewayConst::TYPEDONATION){
            $return_url = route('donation.qrpay.callback');
            $cancel_url = route('donation.qrpay.cancel', $identifier);
        }else{
            $return_url = route('user.add.money.qrpay.callback');
            $cancel_url = route('user.add.money.qrpay.cancel', $identifier);
        }

        $token = $access->data->access_token;
        // Payment Url Request

        $amount = $output['amount']->total_amount ? number_format($output['amount']->total_amount, 2, '.', '') : 0;

        if (PaymentGatewayConst::ENV_SANDBOX == $credentials->mode) {
            $base_url = $credentials->base_url_sandbox;
        } elseif (PaymentGatewayConst::ENV_PRODUCTION == $credentials->mode) {
            $base_url = $credentials->base_url_production;
        }

        $response = Http::withToken($token)->post($base_url . '/payment/create', [
            'amount'     => $amount,
            'currency'   => "USD",
            'return_url' => $return_url,
            'cancel_url' => $cancel_url,
            'custom'   => $identifier,
        ]);


        $statusCode = $response->getStatusCode();
        $content    = json_decode($response->getBody()->getContents());

        if ($content->type == 'error') {
            $errors = implode($content->message->error);
            throw new Exception($errors);
        }

        return redirect()->away($content->data->payment_url);
    }
    // ********* For API **********
    public function qrpayInitApi($output = null)
    {
        if (!$output) $output = $this->output;

        $request_data = $this->request_data;
        $credentials = $this->getQrpayCredetials($output);
        $access = $this->accessTokenQrpay($credentials);
        $identifier = generate_unique_string("transactions", "trx_id", 16);

        $this->QrpayJunkInsert($identifier);


        if($output['request_data']['payment_type'] == PaymentGatewayConst::TYPEDONATION){
            $return_url = route('api.v1.user.donation.qrpay.callback', "r-source=".PaymentGatewayConst::APP);
            $cancel_url = route('api.v1.user.donation.qrpay.cancel', $identifier);
        }else{
            $return_url = route('api.v1.user.add-money.qrpay.callback', "r-source=".PaymentGatewayConst::APP);
            $cancel_url = route('api.v1.user.add-money.qrpay.cancel', $identifier);
        }

        $token = $access->data->access_token;
        // Payment Url Request

        $amount = $output['amount']->total_amount ? number_format($output['amount']->total_amount, 2, '.', '') : 0;

        if (PaymentGatewayConst::ENV_SANDBOX == $credentials->mode) {
            $base_url = $credentials->base_url_sandbox;
        } elseif (PaymentGatewayConst::ENV_PRODUCTION == $credentials->mode) {
            $base_url = $credentials->base_url_production;
        }


        $response = Http::withToken($token)->post($base_url . '/payment/create', [
            'amount'     => $amount,
            'currency'   => "USD",
            'return_url' => $return_url,
            'cancel_url' => $cancel_url,
            'custom'   => $identifier,
        ]);

        $statusCode = $response->getStatusCode();
        $content    = json_decode($response->getBody()->getContents());

        if ($content->type == 'error') {
            $errors = implode($content->message->error);
            throw new Exception($errors);
        }
        $data['link'] = $content->data->payment_url;
        $data['trx'] = $identifier;

        return $data;

    }
    public function getQrpayCredetials($output)
    {
        $gateway = $output['gateway'] ?? null;

        if (!$gateway) throw new Exception("Payment gateway not available");
        $client_id_sample = ['api key', 'api_key', 'client id', 'primary key'];
        $client_secret_sample = ['client_secret', 'client secret', 'secret', 'secret key', 'secret id'];
        $base_url_sandbox = ['base_url', 'base url', 'base-url', 'url', 'base-url-sandbox', 'sandbox', 'sendbox-base-url'];
        $base_url_production = ['base_url', 'base url', 'base-url', 'url', 'base-url-production', 'production'. 'live-base-url', 'live base url'];

        $client_id = '';
        $outer_break = false;
        foreach ($client_id_sample as $item) {
            if ($outer_break == true) {
                break;
            }
            $modify_item = $this->qrpayPlainText($item);
            foreach ($gateway->credentials ?? [] as $gatewayInput) {
                $label = $gatewayInput->label ?? "";
                $label = $this->qrpayPlainText($label);

                if ($label == $modify_item) {
                    $client_id = $gatewayInput->value ?? "";
                    $outer_break = true;
                    break;
                }
            }
        }


        $secret_id = '';
        $outer_break = false;
        foreach ($client_secret_sample as $item) {
            if ($outer_break == true) {
                break;
            }
            $modify_item = $this->qrpayPlainText($item);
            foreach ($gateway->credentials ?? [] as $gatewayInput) {
                $label = $gatewayInput->label ?? "";
                $label = $this->qrpayPlainText($label);

                if ($label == $modify_item) {
                    $secret_id = $gatewayInput->value ?? "";
                    $outer_break = true;
                    break;
                }
            }
        }

        $sandbox_url = '';
        $outer_break = false;
        foreach ($base_url_sandbox as $item) {
            if ($outer_break == true) {
                break;
            }
            $modify_item = $this->qrpayPlainText($item);
            foreach ($gateway->credentials ?? [] as $gatewayInput) {
                $label = $gatewayInput->label ?? "";
                $label = $this->qrpayPlainText($label);

                if ($label == $modify_item) {
                    $sandbox_url = $gatewayInput->value ?? "";
                    $outer_break = true;
                    break;
                }
            }
        }

        $production_url = '';
        $outer_break = false;
        foreach ($base_url_production as $item) {
            if ($outer_break == true) {
                break;
            }
            $modify_item = $this->qrpayPlainText($item);
            foreach ($gateway->credentials ?? [] as $gatewayInput) {
                $label = $gatewayInput->label ?? "";
                $label = $this->qrpayPlainText($label);

                if ($label == $modify_item) {
                    $production_url = $gatewayInput->value ?? "";
                    $outer_break = true;
                    break;
                }
            }
        }


        return (object) [
            'client_id'     => $client_id,
            'client_secret' => $secret_id,
            'base_url_sandbox' => $sandbox_url,
            'base_url_production' => $production_url,
            'mode'          => $gateway->env,

        ];
    }

    public function qrpayPlainText($string)
    {
        $string = Str::lower($string);
        return preg_replace("/[^A-Za-z0-9]/", "", $string);
    }

    public function accessTokenQrpay($credentials)
    {

        if (PaymentGatewayConst::ENV_SANDBOX == $credentials->mode) {
            $base_url = $credentials->base_url_sandbox;
        } elseif (PaymentGatewayConst::ENV_PRODUCTION == $credentials->mode) {
            $base_url = $credentials->base_url_production;
        }

        $response = Http::post($base_url . '/authentication/token', [
            'client_id' => $credentials->client_id,
            'secret_id' => $credentials->client_secret,
        ]);


        $statusCode = $response->getStatusCode();
        $content = $response->getBody()->getContents();

        if ($statusCode != 200) {
            throw new Exception("Access token capture failed");
        }

        return json_decode($content);
    }

    public function qrpayJunkInsert($response)
    {
        $output = $this->output;

        $user = auth()->guard(get_auth_guard())->user();
        $creator_table = $creator_id = $wallet_table = $wallet_id = null;

        if ($user != null) {
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
                'gateway'       => $output['gateway']->id,
                'payment_type'  => $output['request_data']['payment_type'],
                'currency'      => $output['currency']->id,
                'amount'        => json_decode(json_encode($output['amount']),true),
                'response'      => $response,
                'campaign_id'   => $output['request_data']['campaign_id'],
                'wallet_table'  => $wallet_table,
                'wallet_id'     => $wallet_id,
                'creator_table' => $creator_table,
                'creator_id'    => $creator_id,
                'user_type'     => $user_type,
                'creator_guard' => get_auth_guard(),
            ];
        }else{
            $data = [
                'gateway'       => $output['gateway']->id,
                'payment_type'  => $output['request_data']['payment_type'],
                'currency'      => $output['currency']->id,
                'amount'        => json_decode(json_encode($output['amount']),true),
                'response'      => $response,
                'wallet_table'  => $wallet_table,
                'wallet_id'     => $wallet_id,
                'creator_table' => $creator_table,
                'creator_id'    => $creator_id,
                'user_type'     => $user_type,
                'creator_guard' => get_auth_guard(),
            ];
        }

        Session::put('identifier', $response);
        Session::put('output', $output);

        return TemporaryData::create([
            'type'          => PaymentGatewayConst::QRPAY,
            'identifier'    => $response,
            'data'          => $data,
        ]);
    }

    public function qrpaySuccess($output = null)
    {


        if (!$output) $output = $this->output;
        $token = $this->output['tempData']['identifier'] ?? "";
        if (empty($token)) throw new Exception('Transaction faild. Record didn\'t saved properly. Please try again.');
        return $this->createTransactionQrpay($output);
    }

    public function createTransactionQrpay($output)
    {

        $inserted_id = $this->insertRecordQrpay($output);
        $this->insertChargesQrpay($output, $inserted_id);
        $this->insertDeviceQrpay($output, $inserted_id);
        $this->removeTempDataQrpay($output);

        if ($this->requestIsApiUser()) {
            // logout user
            $api_user_login_guard = $this->output['api_login_guard'] ?? null;
            if ($api_user_login_guard != null) {
                auth()->guard($api_user_login_guard)->logout();
            }
        }
    }

    public function updateWalletBalanceQrpay($output)
    {
        $update_amount = $output['wallet']->balance + $output['amount']->requested_amount;
        $output['wallet']->update([
            'balance'   => $update_amount,
        ]);
    }

    public function insertRecordQrpay($output)
    {


        $token = $this->output['tempData']['identifier'] ?? "";
        DB::beginTransaction();
        try {
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
                        'details'                       => json_encode($output),
                        'status'                        => true,
                        'created_at'                    => now(),
                    ]);
                    $this->updateQrpayCampaign($output);
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
                        'details'                       => json_encode($output),
                        'status'                        => true,
                        'created_at'                    => now(),
                    ]);
                    $this->updateWalletBalanceQrpay($output);
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
                    'details'                       => json_encode($output),
                    'status'                        => true,
                    'created_at'                    => now(),
                ]);
                $this->updateQrpayCampaign($output);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return $id;
    }

    // Update campaign
    public function updateQrpayCampaign($output){
        $id = $output['request_data']['campaign_id'];
        $campaign = Campaign::findOrFail($id);
        $campaign->update([
            'raised' => $campaign->raised + $output['amount']->requested_amount,
            'to_go' => $campaign->to_go - $output['amount']->requested_amount,
        ]);
    }

    public function insertChargesQrpay($output, $id)
    {
        DB::beginTransaction();
        try {
            DB::table('transaction_charges')->insert([
                'transaction_id'    => $id,
                'percent_charge'    => $output['amount']->percent_charge,
                'fixed_charge'      => $output['amount']->fixed_charge,
                'total_charge'      => $output['amount']->total_charge,
                'created_at'        => now(),
            ]);
            DB::commit();

            if (Auth::guard(get_auth_guard())->check()) {
                $user_id = auth()->guard(get_auth_guard())->user()->id;
            } else {
                $user_id = null;
            }

            if (Auth::guard(get_auth_guard())->check()) {
                $notification_content = [
                    'title'         => "Add Money",
                    'message'       => "Your Wallet (" . $output['wallet']->currency->code . ") balance  has been added " . $output['amount']->requested_amount . ' ' . $output['wallet']->currency->code,
                    'time'          => Carbon::now()->diffForHumans(),
                    'image'         => files_asset_path('profile-default'),
                ];
                UserNotification::create([
                    'type'      => NotificationConst::BALANCE_ADDED,
                    'user_id'  =>  $user_id,
                    'message'   => $notification_content,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function insertDeviceQrpay($output, $id)
    {
        $client_ip = request()->ip() ?? false;
        $location = geoip()->getLocation($client_ip);
        $agent = new Agent();

        $mac = "";

        DB::beginTransaction();
        try {
            DB::table("transaction_devices")->insert([
                'transaction_id' => $id,
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
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function removeTempDataQrpay($output)
    {
        TemporaryData::where("identifier", $output['tempData']['identifier'])->delete();
    }
}
