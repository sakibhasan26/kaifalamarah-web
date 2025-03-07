<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Campaign;
use App\Models\UserWallet;
use App\Models\Transaction;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Constants\NotificationConst;
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use App\Traits\PaymentGateway\Manual;
use App\Traits\PaymentGateway\Stripe;
use Illuminate\Auth\Events\Validated;
use App\Constants\PaymentGatewayConst;
use App\Models\Admin\CryptoTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\PaymentGatewayCurrency;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use App\Http\Helpers\PaymentGateway as PaymentGatewayHelper;

class DonationController extends Controller
{
    Use Stripe, Manual;
    /**
     * This method for submit campaign donation
     * @method POST
    * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request
     */
    public function submit(Request $request) {
        if (Auth::guard(get_auth_guard())->check()) {
            $user_type = PaymentGatewayConst::AUTHENTICATED;
        } else {
            $user_type = PaymentGatewayConst::GUEST;
        }

        $campaign = Campaign::findOrFail($request->campaign_id);
        $need_amount = $campaign->to_go;

        if($need_amount == 0){
            return back()->with(['error' => [__('We do not need more donation for this campaign')]]);
        }else if($need_amount < $request->amount){
            return back()->with(['error' => ['We need just '.$need_amount.' '. get_default_currency_code()]]);
        }
        $request->merge([
            'payment_type' => PaymentGatewayConst::TYPEDONATION,
            'user_type' =>$user_type
            ]);
        if($request->currency == 'wallet-usd'){
            return $this->walletPayment($request->all());
        }else{
            // $instance = PaymentGatewayHelper::init($request->all())->type(PaymentGatewayConst::TYPEDONATION)->gateway()->render();

            try{
                $payment_gateway_currency = PaymentGatewayCurrency::where('alias', $request->currency)->first();
                $payment_gateway = PaymentGateway::where('id', $payment_gateway_currency->payment_gateway_id)->first();

                if($payment_gateway->alias == PaymentGatewayConst::FLUTTER_WAVE){
                    if(Auth::check()){
                        $instance = PaymentGatewayHelper::init($request->all())->type(PaymentGatewayConst::TYPEDONATION)->gateway()->render();
                    }else{
                        $hasData = $request->all();
                        Session::forget('hasData');
                        Session::put('hasData', $hasData);
                        return redirect()->route('donation.flutterwave.info');
                    }
                }elseif ($payment_gateway->alias == PaymentGatewayConst::TATUM || $payment_gateway->alias == PaymentGatewayConst::COINGATE) {
                    if(Auth::check()){
                        $instance = PaymentGatewayHelper::init($request->all())->type(PaymentGatewayConst::TYPEDONATION)->gateway()->render();
                    }else{
                        return redirect()->route('user.login')->with(['error' => [__('Unauthenticated User Can Not Donation With').$payment_gateway->name]]);
                    }
                }elseif ($payment_gateway->alias == PaymentGatewayConst::PAYSTACK) {

                    if(Auth::guard(get_auth_guard())->check()){
                        $instance = PaymentGatewayHelper::init($request->all())->type(PaymentGatewayConst::TYPEDONATION)->gateway()->render();
                    }else{
                        $hasData = $request->all();
                        Session::forget('hasData');
                        Session::put('hasData', $hasData);
                        return redirect()->route('donation.paystack.info');
                    }
                }else{

                    $instance = PaymentGatewayHelper::init($request->all())->type(PaymentGatewayConst::TYPEDONATION)->gateway()->render();
                }
            }catch(Exception $e) {
                return back()->with(['error' => [$e->getMessage()]]);
            }
            return $instance;
        }
    }

    /**
     * This method for success alert of PayPal
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request
     */
    public function success(Request $request, $gateway){
        $requestData = $request->all();
        $token = $requestData['token'] ?? "";
        $redirect_route = Session::get('redirect_route');
        $checkTempData = TemporaryData::where("type",$gateway)->where("identifier",$token)->first();
        if(!$checkTempData) return redirect()->route($redirect_route['common'], [$redirect_route['id'], $redirect_route['slug']])->with(['error' => ['Transaction failed. Record didn\'t saved properly. Please try again']]);
        $checkTempData = $checkTempData->toArray();

        try{
            PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive();
        }catch(Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }
        Session::forget('redirect_route');
        return redirect()->route($redirect_route['common'], [$redirect_route['id'], $redirect_route['slug']])->with(['success' => [__('Thanks for donating')]]);
    }

    /**
     * This method for cancel alert of PayPal
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request
     */
    public function cancel(Request $request, $gateway) {
        $token = session()->get('identifier');
        $redirect_route = Session::get('redirect_route');
        if( $token){
            TemporaryData::where("identifier",$token)->delete();
        }
        return redirect()->route($redirect_route['common'], [$redirect_route['id'], $redirect_route['slug']]);
    }

    /**
     * This method for stripe payment
     * @method GET
     * @param $gateway
     */
    public function payment($gateway){
        $page_title = __('Stripe Payment');
        $redirect_route = Session::get('redirect_route');
        $tempData = Session::get('identifier');
        $hasData = TemporaryData::where('identifier', $tempData)->where('type',$gateway)->first();
        if(!$hasData){
            return redirect()->route($redirect_route['common'], [$redirect_route['id'], $redirect_route['slug']]);
        }
        return view('frontend.donation.automatic.'.$gateway,compact("page_title","hasData"));
    }

    /**
     * This method for manual payment
     * @method GET
     */
    public function manualPayment(){
        $tempData = Session::get('identifier');
        $redirect_route = Session::get('redirect_route');
        $hasData = TemporaryData::where('identifier', $tempData)->first();
        $gateway = PaymentGateway::manual()->where('slug',PaymentGatewayConst::add_money_slug())->where('id',$hasData->data->gateway)->first();
        $page_title = __('Manual Payment').' ( '.$gateway->name.' )';
        if(!$hasData){
            return redirect()->route($redirect_route['common'], [$redirect_route['id'], $redirect_route['slug']]);
        }
        return view('frontend.donation.manual.payment_confirmation',compact("page_title","hasData",'gateway'));
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

        $validated = $validator->validated();
        $wallet = UserWallet::auth()->first();
        if($wallet->balance > $validated['amount']){

        DB::beginTransaction();
        try{
            // Add money
            $trx_id = generateTrxString("transactions","trx_id",'D',9);
            $id = DB::table("transactions")->insertGetId([
                'user_id'                       => auth()->user()->id,
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
                'to_go' => $campaign->to_go - $validated['amount'],
            ]);

            // Insert device wallet
            $this->insertDeviceWallet($id);
            $this->insertChargesWallet($validated, $id);


            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }

            return back()->with(['success' => [__('Thanks for donating')]]);
        }else{
            return back()->with(['error' => [__('Your wallet balance is insufficient')]]);
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
                'title'         => __("Add Money"),
                'message'       => "Your Wallet (".get_default_currency_code().") balance  has been added ".$validated['amount'].' '. get_default_currency_code(),
                'time'          => Carbon::now()->diffForHumans(),
                'image'         => files_asset_path('profile-default'),
            ];

            UserNotification::create([
                'type'      => NotificationConst::BALANCE_ADDED,
                'user_id'  =>  auth()->user()->id,
                'message'   => $notification_content,
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
        $redirect_route = Session::get('redirect_route');

        if ($status ==  'successful') {
            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);

            $requestData = request()->tx_ref;
            $token = $requestData;

            $checkTempData = TemporaryData::where("type",'flutterwave')->where("identifier",$token)->first();

            if(!$checkTempData) return redirect()->route($redirect_route['common'], [$redirect_route['id'], $redirect_route['slug']])->with(['error' => ["Transaction failed. Record didn\'t saved properly. Please try again"]]);

            $checkTempData = $checkTempData->toArray();

            try{
                PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('flutterWave');
            }catch(Exception $e) {
                return back()->with(['error' => [$e->getMessage()]]);
            }
            return redirect()->route($redirect_route['common'], [$redirect_route['id'], $redirect_route['slug']])->with(['success' => [__('Thanks for donating')]]);

        }
        elseif ($status ==  'cancelled'){
            return redirect()->route($redirect_route['common'], [$redirect_route['id'], $redirect_route['slug']])->with(['error' => [__('Donation cancelled')]]);
        }
        else{
            return redirect()->route($redirect_route['common'], [$redirect_route['id'], $redirect_route['slug']])->with(['error' => [__('Transaction failed')]]);
        }
    }

    public function flutterwaveInfo()
    {
        $hasData = Session::get('hasData');
        $page_title = __('Payment Information');
        return view('frontend.donation.flutterwave.user_info', compact('page_title', 'hasData'));
    }

    public function flutterwaveConfirm(Request $request)
    {
        $campaign = Campaign::findOrFail($request->campaign_id);
        $need_amount = $campaign->to_go;
        if($need_amount == 0){
            return back()->with(['error' => [__('We do not need more donation for this campaign')]]);
        }else if($need_amount < $request->amount){
            return back()->with(['error' => [__('We need just').$need_amount.' '. get_default_currency_code()]]);
        }
        try{
            $instance = PaymentGatewayHelper::init($request->all())->gateway()->render();
        }catch(Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }
        return $instance;
    }

    //stripe success
    public function stripePaymentSuccess($trx){
        $token = $trx;
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::STRIPE)->where("identifier",$token)->first();
        if(!$checkTempData) return redirect()->route('donation')->with(['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]]);
        $checkTempData = $checkTempData->toArray();
        try{
            PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('stripe');
        }catch(Exception $e) {

            return back()->with(['error' => ["Something Is Wrong..."]]);
        }
        $campaign = Campaign::find($checkTempData['data']->campaign_id);
        return redirect()->route('campaign.details', [$campaign->id, $campaign->slug])->with(['success' => [__('Thanks for donating')]]);
    }


    //sslcommerz success
    public function sllCommerzSuccess(Request $request){
        $data = $request->all();
        $token = $data['tran_id'];
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::SSLCOMMERZ)->where("identifier",$token)->first();
        if(!$checkTempData) return redirect()->route('donation')->with(['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]]);
        $checkTempData = $checkTempData->toArray();
        $creator_id = $checkTempData['data']->creator_id ?? null;
        $creator_guard = $checkTempData['data']->creator_guard ?? null;
        if($creator_id){
            Auth::guard($creator_guard)->loginUsingId($creator_id);
        }
        if( $data['status'] != "VALID"){
            return redirect()->route('donation')->with(['error' => ['Added Money Failed']]);
        }
        try{
            PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('sslcommerz');
        }catch(Exception $e) {
            return back()->with(['error' => ["Something Is Wrong..."]]);
        }

        $campaign = Campaign::find($checkTempData['data']->campaign_id);
        return redirect()->route('campaign.details', [$campaign->id, $campaign->slug])->with(['success' => [__('Thanks for donating')]]);
    }
    //sslCommerz fails
    public function sllCommerzFails(Request $request){
        $data = $request->all();
        $token = $data['tran_id'];
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::SSLCOMMERZ)->where("identifier",$token)->first();
        if(!$checkTempData) return redirect()->route('donationdonation')->with(['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]]);
        $checkTempData = $checkTempData->toArray();
        $creator_id = $checkTempData['data']->creator_id ?? null;
        $creator_guard = $checkTempData['data']->creator_guard ?? null;
        $user = Auth::guard($creator_guard)->loginUsingId($creator_id);
        if( $data['status'] == "FAILED"){
            TemporaryData::destroy($checkTempData['id']);
            return redirect()->route('donation')->with(['error' => [__('Donation Failed')]]);
        }

    }
    //sslCommerz canceled
    public function sllCommerzCancel(Request $request){
        $data = $request->all();
        $token = $data['tran_id'];
        $checkTempData = TemporaryData::where("type",PaymentGatewayConst::SSLCOMMERZ)->where("identifier",$token)->first();
        if(!$checkTempData) return redirect()->route('donation')->with(['error' => [__('Transaction Failed. Record didn\'t saved properly. Please try again')]]);
        $checkTempData = $checkTempData->toArray();
        $creator_id = $checkTempData['data']->creator_id ?? null;
        $creator_guard = $checkTempData['data']->creator_guard ?? null;
        $user = Auth::guard($creator_guard)->loginUsingId($creator_id);
        if( $data['status'] != "VALID"){
            TemporaryData::destroy($checkTempData['id']);
            return redirect()->route('donation')->with(['error' => [__('Donation Canceled')]]);
        }
    }

    public function redirectBtnPay(Request $request, $gateway)
    {
        try{
            return PaymentGatewayHelper::init([])->handleBtnPay($gateway, $request->all());
        }catch(Exception $e) {
            return redirect()->route('index')->with(['error' => [$e->getMessage()]]);
        }
    }

    public function razorSuccess(Request $request, $gateway){
        try{
            $token = PaymentGatewayHelper::getToken($request->all(),$gateway);

            $temp_data = TemporaryData::where("identifier",$token)->first();
            if(Transaction::where('callback_ref', $token)->exists()) {
                if(!$temp_data) return redirect()->route('index')->with(['success' => [__('Transaction request sended successfully')]]);
            }else {
                if(!$temp_data) return redirect()->route('index')->with(['error' => [__('Transaction failed. Record didn\'t saved properly. Please try again')]]);
            }

            $update_temp_data = json_decode(json_encode($temp_data->data),true);
            $update_temp_data['callback_data']  = $request->all();
            $temp_data->update([
                'data'  => $update_temp_data,
            ]);
            $temp_data = $temp_data->toArray();
            $instance = PaymentGatewayHelper::init($temp_data)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive($temp_data['type']);
            if($instance instanceof RedirectResponse) return $instance;
        }catch(Exception $e) {

            return back()->with(['error' => [$e->getMessage()]]);
        }
        return redirect()->route("index")->with(['success' => [__('Thanks for donating')]]);
    }

    public function razorCancel(Request $request, $gateway) {
        $token = PaymentGatewayHelper::getToken($request->all(),$gateway);
        if($temp_data = TemporaryData::where("identifier",$token)->first()) {
            $temp_data->delete();
        }
        return redirect()->route('campaign.details');
    }



      // Qrpay Call Back
    public function qrpayCallback(Request $request)
    {
        if ($request->type ==  'success') {

            $requestData = $request->all();

            $checkTempData = TemporaryData::where("type", 'qrpay')->where("identifier", $requestData['data']['custom'])->first();

            if (!$checkTempData) return redirect()->route('donation')->with(['error' => [__('Transaction failed. Record didn\'t saved properly. Please try again')]]);

            $checkTempData = $checkTempData->toArray();

            try {
                PaymentGatewayHelper::init($checkTempData)->type(PaymentGatewayConst::TYPEADDMONEY)->responseReceive('qrpay');
            } catch (Exception $e) {
                return back()->with(['error' => [$e->getMessage()]]);
            }

            $campaign = Campaign::find($checkTempData['data']->campaign_id);
            return redirect()->route('campaign.details', [$campaign->id, $campaign->slug])->with(['success' => [__('Thanks for donating')]]);
        } else {
            return redirect()->route('donation')->with(['error' => [__('Transaction failed')]]);
        }
    }

    // QrPay Cancel
    public function qrpayCancel(Request $request, $trx_id)
    {
        TemporaryData::where("identifier", $trx_id)->delete();
        return redirect()->route('donation')->with(['error' => [__('Payment Canceled')]]);
    }

    public function cryptoPaymentAddress(Request $request, $trx_id) {

        $page_title = "Crypto Payment Address";
        $transaction = Transaction::where('trx_id', $trx_id)->first();

        if($transaction->gateway_currency->gateway->isCrypto() && $transaction->details?->payment_info?->receiver_address ?? false) {
            return view('frontend.donation.payment.crypto.address', compact(
                'transaction',
                'page_title',
            ));
        }

        return abort(404);
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

        if(!isset($validated['txn_hash'])) return back()->with(['error' => [__('Transaction hash is required for verify')]]);

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

        if(!$crypto_transaction) return back()->with(['error' => [__('Transaction hash is not valid! Please input a valid hash')]]);

        if($crypto_transaction->amount >= $transaction->total_payable == false) {
            if(!$crypto_transaction) return back()->with(['error' => [__('Insufficient amount added. Please contact with system administrator')]]);
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
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return redirect()->route('campaign.details', [$campaign->id, $campaign->slug])->with(['success' => [__('Thanks for donating')]]);
    }

    //coingate response start
    public function coinGateSuccess(Request $request, $gateway){
        try{
            $token = $request->token;
            $checkTempData = TemporaryData::where("type",PaymentGatewayConst::COINGATE)->where("identifier",$token)->first();
            if(!$checkTempData) return redirect()->route('donation')->with(['error' => [__('Transaction failed. Record didn\'t saved properly. Please try again')]]);

            if(Transaction::where('callback_ref', $token)->exists()) {
                if(!$checkTempData) return redirect()->route('donation')->with(['success' => [__('Transaction request sended successfully')]]);
            }else {
                if(!$checkTempData) return redirect()->route('donation')->with(['error' => [__('Transaction failed. Record didn\'t saved properly. Please try again')]]);
            }
            $update_temp_data = json_decode(json_encode($checkTempData->data),true);
            $update_temp_data['callback_data']  = $request->all();
            $checkTempData->update([
                'data'  => $update_temp_data,
            ]);
            $temp_data = $checkTempData->toArray();
            PaymentGatewayHelper::init($temp_data)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('coingate');
        }catch(Exception $e) {
            return redirect()->route('donation')->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return redirect()->route('donation')->with(['success' => [__('Thanks for donating')]]);
    }

    public function coinGateCancel(Request $request, $gateway){
        if($request->has('token')) {
            $identifier = $request->token;
            if($temp_data = TemporaryData::where('identifier', $identifier)->first()) {
                $temp_data->delete();
            }
        }
        return redirect()->route('donation')->with(['error' => [__('Donation cancelled')]]);
    }


    // Perfect Money
    public function redirectUsingHTMLForm(Request $request, $gateway)
    {
        $temp_data = TemporaryData::where('identifier', $request->token)->first();
        if(!$temp_data || $temp_data->data->action_type != PaymentGatewayConst::REDIRECT_USING_HTML_FORM) return back()->with(['error' => ['Request token is invalid!']]);
        $redirect_form_data = $temp_data->data->redirect_form_data;
        $action_url         = $temp_data->data->action_url;
        $form_method        = $temp_data->data->form_method;

        return view('user.sections.add-money.automatic.redirect-form', compact('redirect_form_data', 'action_url', 'form_method'));
    }

    public function perfectSuccess(Request $request, $gateway){

        try{
            $token = PaymentGatewayHelper::getToken($request->all(),$gateway);
            $temp_data = TemporaryData::where("type",PaymentGatewayConst::PERFECT_MONEY)->where("identifier",$token)->first();


            if(Transaction::where('callback_ref', $token)->exists()) {
                if(!$temp_data) return redirect()->route('donation')->with(['success' => [__('Transaction request sended successfully')]]);
            }else {
                if(!$temp_data) return redirect()->route('donation')->with(['error' => [__('Transaction failed. Record didn\'t saved properly. Please try again')]]);
            }

            $update_temp_data = json_decode(json_encode($temp_data->data),true);
            $update_temp_data['callback_data']  = $request->all();

            $temp_data->update([
                'data'  => $update_temp_data,
            ]);

            $temp_data = $temp_data->toArray();
            $instance = PaymentGatewayHelper::init($temp_data)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('perfect-money');
            if($instance instanceof RedirectResponse) return $instance;
        }catch(Exception $e) {

            return back()->with(['error' => [$e->getMessage()]]);
        }
        return redirect()->route("donation")->with(['success' => [__('Successfully Added Money')]]);
    }
    public function perfectCancel(Request $request, $gateway) {

        if($request->has('token')) {
            $identifier = $request->token;
            if($temp_data = TemporaryData::where('identifier', $identifier)->first()) {
                $temp_data->delete();
            }
        }

        return redirect()->route("donation")->with(['error' => [__('Added Money Canceled Successfully')]]);
    }
    public function perfectCallback(Request $request,$gateway) {

        $callback_token = $request->get('token');
        $callback_data = $request->all();
        try{
            PaymentGatewayHelper::init([])->type(PaymentGatewayConst::TYPEDONATION)->handleCallback($callback_token,$callback_data,$gateway);
        }catch(Exception $e) {
            // handle Error
            logger($e);
        }
    }


    public function callback(Request $request,$gateway){
        $callback_token = $request->get('token');
        $callback_data = $request->all();

        try{
            PaymentGatewayHelper::init([])->type(PaymentGatewayConst::TYPEDONATION)->handleCallback($callback_token,$callback_data,$gateway);
        }catch(Exception $e) {
            logger($e);
        }
    }





    public function paystackInfo()
    {
        $hasData = Session::get('hasData');
        $page_title = __('Payment Information');
        return view('frontend.donation.paystack.user_info', compact('page_title', 'hasData'));
    }

    public function paystackConfirm(Request $request)
    {
        if (Auth::guard(get_auth_guard())->check()) {
            $user_type = PaymentGatewayConst::AUTHENTICATED;
        } else {
            $user_type = PaymentGatewayConst::GUEST;
        }

        $request->merge([
            'payment_type' => PaymentGatewayConst::TYPEDONATION,
            'user_type' =>$user_type
            ]);

        $campaign = Campaign::findOrFail($request->campaign_id);
        $need_amount = $campaign->to_go;
        if($need_amount == 0){
            return back()->with(['error' => [__('We do not need more donation for this Donation')]]);
        }else if($need_amount < $request->amount){
            return back()->with(['error' => [__('We need just').$need_amount.' '. get_default_currency_code()]]);
        }
        try{
            $instance = PaymentGatewayHelper::init($request->all())->gateway()->render();
        }catch(Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }
        return $instance;
    }


    public function paystackCallback(Request $request){
        $request_data = $request->all();

        $reference =  $request_data['reference'];
        $temp_data = TemporaryData::where("identifier",$reference)->first();
        if(!$temp_data) return redirect()->route('donation')->with(['error' => [__('Transaction failed. Record didn\'t saved properly. Please try again.')]]);

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
            $token =  PaymentGatewayHelper::getToken($request->all(),$gateway->alias);
            $temp_data = TemporaryData::where("identifier",$token)->first();

            if (!$temp_data) return redirect()->route('donation')->with(['error' => [__("Transaction Failed. Record didn't saved properly. Please try again.")]]);
            $update_temp_data = json_decode(json_encode($temp_data->data),true);
            $update_temp_data['callback_data']  = $response;
            $temp_data->update([
                'data'  => $update_temp_data,
            ]);
            $temp_data = $temp_data->toArray();
            $creator_id = $temp_data['data']->creator_id ?? null;

            $creator_guard = $temp_data['data']->creator_guard ?? null;
            $user = Auth::guard($creator_guard)->loginUsingId($creator_id);

            $instance = PaymentGatewayHelper::init($temp_data)->type(PaymentGatewayConst::TYPEDONATION)->responseReceive('paystack');
            if($instance instanceof RedirectResponse) return $instance;
        }catch(Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }
        return redirect()->route('index')->with(['success' => [__('Successfully Donation')]]);
    }



}
