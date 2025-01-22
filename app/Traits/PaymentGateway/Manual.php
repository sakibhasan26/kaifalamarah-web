<?php

namespace App\Traits\PaymentGateway;

use Exception;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Constants\NotificationConst;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Session;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Http\Helpers\Api\PaymentGatewayApi;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Models\Admin\PaymentGateway as PaymentGatewayModel;

trait Manual
{
    use ControlDynamicInputFields;

    public function manualInit($output = null) {
        if(!$output) $output = $this->output;
        $gatewayAlias = $output['gateway']['alias'];
        $identifier = generate_unique_string("transactions","trx_id",16);
        $this->manualJunkInsert($identifier);
        Session::put('identifier',$identifier);
        Session::put('output',$output);
        if($output['request_data']['payment_type'] == PaymentGatewayConst::TYPEDONATION){
            $redirect_route = [
                'common' => 'campaign.details',
                'id'     => $output['request_data']['campaign_id'],
                'slug'   => $output['request_data']['campaign_slug'],
            ];
            Session::put('redirect_route', $redirect_route);

            return redirect()->route('donation.manual.payment');
        }else{
            return redirect()->route('user.add.money.manual.payment');
        }
    }

    public function manualJunkInsert($response) {

        $output = $this->output;

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
                'user_type'     => $user_type,
            ];
        }else{
            $data = [
                'gateway'   => $output['gateway']->id,
                'payment_type'=> $output['request_data']['payment_type'],
                'currency'  => $output['currency']->id,
                'amount'    => json_decode(json_encode($output['amount']),true),
                'response'  => $response,
                'user_type'     => $user_type,
            ];
        }

        return TemporaryData::create([
            'type'          => PaymentGatewayConst::MANUA_GATEWAY,
            'identifier'    => $response,
            'data'          => $data,
        ]);
    }

    public function manualPaymentConfirmed(Request $request){
        $output = session()->get('output');
        $tempData = Session::get('identifier');
        $hasData = TemporaryData::where('identifier', $tempData)->first();
        $gateway = PaymentGatewayModel::manual()->where('slug',PaymentGatewayConst::add_money_slug())->where('id',$hasData->data->gateway)->first();
        $payment_fields = $gateway->input_fields ?? [];

        $validation_rules = $this->generateValidationRules($payment_fields);
        $payment_field_validate = Validator::make($request->all(),$validation_rules)->validate();
        $get_values = $this->placeValueWithFields($payment_fields,$payment_field_validate);


        try{
            $inserted_id = $this->insertRecordManual($output,$get_values);
            $this->insertChargesManual($output,$inserted_id);
            $this->insertDeviceManual($output,$inserted_id);
            $this->removeTempDataManual($output);

            $redirect_route = Session::get('redirect_route');

            Session::forget(['output','identifier','redirect_route']);

            if($output['request_data']['payment_type'] == PaymentGatewayConst::TYPEDONATION){
                return redirect()->route($redirect_route['common'], [$redirect_route['id'], $redirect_route['slug']])->with(['success' => ['Successfully donation']]);
            }else{
                return redirect()->route("user.add.money.index")->with(['success' => ['Add Money request send to admin successfully']]);
            }
        }catch(Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }



    }


    public function insertRecordManual($output,$get_values) {
        $token = $this->output['tempData']['identifier'] ?? "";
        DB::beginTransaction();
        try{
            if(isset($output['request_data']['api_check'])){
                $api_check = Auth::guard('api')->check();
                if($api_check){
                    $user_id = Auth::guard('api')->user()->id;
                }
            }else{
                $api_check =  Auth::check();
                if($api_check){
                    $user_id = auth()->user()->id;
                }
            }

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
                    'details'                       => json_encode($get_values),
                    'status'                        => 2,
                    'created_at'                    => now(),
                ]);
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
                    'available_balance'             => $output['wallet']->balance,
                    'remark'                        => ucwords(remove_speacial_char($output['request_data']['payment_type']," ")) . " With " . $output['gateway']->name,
                    'details'                       => json_encode($get_values),
                    'status'                        => 2,
                    'created_at'                    => now(),
                ]);
            }
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return $id;
    }


    public function insertChargesManual($output,$id) {
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

            //notification
            $notification_content = [
                'title'         => "Add Money",
                'message'       => "Your Wallet (".$output['wallet']->currency->code.") balance  has been added ".$output['amount']->requested_amount.' '. $output['wallet']->currency->code,
                'time'          => Carbon::now()->diffForHumans(),
                'image'         => files_asset_path('profile-default'),
            ];

            if(isset($output['request_data']['api_check'])){
                $api_check = Auth::guard('api')->check();
                if($api_check){
                    $user_id = Auth::guard('api')->user()->id;
                }
            }else{
                $api_check =  Auth::check();
                if($api_check){
                    $user_id = auth()->user()->id;
                }
            }

            UserNotification::create([
                'type'      => NotificationConst::BALANCE_ADDED,
                'user_id'  =>  $user_id,
                'message'   => $notification_content,
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function insertDeviceManual($output,$id) {
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

    public function removeTempDataManual($output) {
        $token = session()->get('identifier');
        TemporaryData::where("identifier",$token)->delete();
    }


    // ********* For API **********
    public function manualInitApi($output = null) {
        if(!$output) $output = $this->output;
        $gatewayAlias = $output['gateway']['alias'];
        $identifier = generate_unique_string("transactions","trx_id",16);
        $this->manualJunkInsert($identifier);
        $response=[
            'trx' => $identifier,
        ];
        return $response;
    }
    public function manualPaymentConfirmedApi(Request $request){

        $validator = Validator::make($request->all(), [
            'track' => 'required',
        ]);
        if($validator->fails()){
            $error =  ['error'=>$validator->errors()->all()];
            return ApiResponse::validation($error);
        }
        $track = $request->track;
        $hasData = TemporaryData::where('identifier', $track)->first();
        if(!$hasData){
            $error = ['error'=>["Sorry, your payment information is invalid"]];
            return ApiResponse::error($error);
        }
        $gateway = PaymentGatewayModel::manual()->where('slug',PaymentGatewayConst::add_money_slug())->where('id',$hasData->data->gateway)->first();
        $payment_fields = $gateway->input_fields ?? [];


        $validation_rules = $this->generateValidationRules($payment_fields);
        $validator2 = Validator::make($request->all(), $validation_rules);

        if ($validator2->fails()) {
            $message =  ['error' => $validator2->errors()->all()];
            return ApiResponse::error($message);
        }

        $validated = $validator2->validate();

        $payment_gateway_currency = PaymentGatewayCurrency::where('id', $hasData->data->currency)->first();

        if($request->payment_type == 'DONATION'){
            $api_check = true;
        }else{
            $api_check = false;
        }

        if($request->payment_type == PaymentGatewayConst::TYPEDONATION){
            $campaign_id = $hasData->data->campaign_id;
        }else if(isset($hasData->data->campaign_id)){
               $campaign_id = $hasData->data->campaign_id;
        }else{
            $campaign_id =null;
        }

        $gateway_request = [
            'currency' => $payment_gateway_currency->alias,
            'amount'  => $hasData->data->amount->requested_amount,
            'payment_type' => $hasData->data->payment_type,
            'api_check' => $api_check,
            'campaign_id' => $campaign_id,
        ];

        $output = PaymentGatewayApi::init($gateway_request)->gateway()->get();

        $get_values = $this->placeValueWithFields($payment_fields, $validated);

        try{
            $inserted_id = $this->insertRecordManual($output,$get_values);
            $this->insertChargesManual($output,$inserted_id);
            $this->insertDeviceManual($output,$inserted_id);
            $hasData->delete();
            if($request->payment_type == PaymentGatewayConst::TYPEDONATION){
                $message =  ['success'=>['Donation request send to admin successfully']];
            }else{
                $message =  ['success'=>['Add Money request send to admin successfully']];
            }
            return ApiResponse::onlySuccess( $message);
        }catch(Exception $e) {
                $error = ['error'=>[$e->getMessage()]];
                return ApiResponse::error($error);
        }
    }
}
