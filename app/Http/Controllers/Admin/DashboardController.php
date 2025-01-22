<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Providers\Admin\BasicSettingsProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\PushNotifications\PushNotifications;
use App\Models\Admin\AdminNotification;
use App\Constants\NotificationConst;
use App\Constants\PaymentGatewayConst;
use App\Http\Helpers\Response;
use App\Models\Admin\Event;
use App\Models\Campaign;
use App\Models\Subscriber;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __('Dashboard');

        $last_month_start =  date('Y-m-01', strtotime('-1 month', strtotime(date('Y-m-d'))));
        $last_month_end =  date('Y-m-31', strtotime('-1 month', strtotime(date('Y-m-d'))));
        $this_month_start = date('Y-m-01');
        $this_month_end = date('Y-m-d');
        $this_weak = date('Y-m-d', strtotime('-1 week', strtotime(date('Y-m-d'))));
        $this_month = date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d'))));
        $this_year = date('Y-m-d', strtotime('-1 year', strtotime(date('Y-m-d'))));

        // Dashboard box data
        // Add Money
        $add_money_balance = Transaction::toBase()->where('type', PaymentGatewayConst::TYPEADDMONEY)->where('status', 1)->sum('request_amount');
        $add_money_total_balance = Transaction::toBase()->where('type', PaymentGatewayConst::TYPEADDMONEY)->sum('request_amount');
        $today_add_money =  Transaction::toBase()
                            ->where('type', PaymentGatewayConst::TYPEADDMONEY)
                            ->where('status', 1)
                            ->whereDate('created_at','>=',$this_month_start)
                            ->whereDate('created_at','<=',$this_month_end)
                            ->sum('request_amount');
        $last_month_add_money =  Transaction::toBase()->where('status', 1)
                                            ->where('type', PaymentGatewayConst::TYPEADDMONEY)
                                            ->whereDate('created_at','>=',$last_month_start)
                                            ->whereDate('created_at','<=',$last_month_end)
                                            ->sum('request_amount');

        $last_month_add_money_p = $last_month_add_money == 0 ? 1 : $last_month_add_money;
        $add_money_percent = (($today_add_money * 100) / $last_month_add_money_p);

        if($add_money_percent > 100){
            $add_money_percent = 100;
        }

        // Pending Add Money
        $pending_add_money_balance = Transaction::toBase()->where('type', PaymentGatewayConst::TYPEADDMONEY)->where('status', 2)->sum('request_amount');
        $today_pending_add_money =  Transaction::toBase()
                                    ->where('type', PaymentGatewayConst::TYPEADDMONEY)
                                    ->where('status', 2)
                                    ->whereDate('created_at','>=',$this_month_start)
                                    ->whereDate('created_at','<=',$this_month_end)
                                    ->sum('request_amount');
        $last_month_pending_add_money =  Transaction::toBase()->where('status', 2)
                                            ->where('type', PaymentGatewayConst::TYPEADDMONEY)
                                            ->whereDate('created_at','>=',$last_month_start)
                                            ->whereDate('created_at','<=',$last_month_end)
                                            ->sum('request_amount');

        if($last_month_pending_add_money == 0){
            $pending_add_money_percent = 100;
        }else{
            $pending_add_money_percent = (($pending_add_money_balance * 100) / $last_month_pending_add_money);
        }


        // Donation
        $donation_balance = Transaction::toBase()->where('type', PaymentGatewayConst::TYPEDONATION)->where('status', 1)->sum('request_amount');
        $today_donation_balance = Transaction::toBase()
                                    ->where('type', PaymentGatewayConst::TYPEDONATION)
                                    ->whereDate('created_at', $this_month_end)
                                    ->where('status', 1)
                                    ->sum('request_amount');
        $this_week_donation_balance = Transaction::toBase()
                                    ->where('type', PaymentGatewayConst::TYPEDONATION)
                                    ->whereDate('created_at', '>=', $this_weak)
                                    ->where('status', 1)
                                    ->sum('request_amount');
        $this_month_donation_balance = Transaction::toBase()
                                    ->where('type', PaymentGatewayConst::TYPEDONATION)
                                    ->whereDate('created_at', '>=', $this_month)
                                    ->where('status', 1)
                                    ->sum('request_amount');
        $this_year_donation_balance = Transaction::toBase()
                                    ->where('type', PaymentGatewayConst::TYPEDONATION)
                                    ->whereDate('created_at', '>=', $this_year)
                                    ->where('status', 1)
                                    ->sum('request_amount');

        $donation_total_balance = Transaction::toBase()->where('type', PaymentGatewayConst::TYPEDONATION)->sum('request_amount');
        $today_donation =  Transaction::toBase()
                            ->where('type', PaymentGatewayConst::TYPEDONATION)
                            ->where('status', 1)
                            ->whereDate('created_at','>=',$this_month_start)
                            ->whereDate('created_at','<=',$this_month_end)
                            ->sum('request_amount');
        $last_month_donation =  Transaction::toBase()->where('status', 1)
                                            ->where('type', PaymentGatewayConst::TYPEDONATION)
                                            ->whereDate('created_at','>=',$last_month_start)
                                            ->whereDate('created_at','<=',$last_month_end)
                                            ->sum('request_amount');

        $last_month_donation_p = $last_month_donation == 0 ? 1 : $last_month_donation;
        $donation_percent = (($today_donation * 100) / $last_month_donation_p);

        if($donation_percent > 100){
            $donation_percent = 100;
        }

        // Pending Donation
        $pending_donation_balance = Transaction::toBase()->where('type', PaymentGatewayConst::TYPEDONATION)->where('status', 2)->sum('request_amount');
        $today_pending_donation =  Transaction::toBase()
                                    ->where('type', PaymentGatewayConst::TYPEDONATION)
                                    ->where('status', 2)
                                    ->whereDate('created_at','>=',$this_month_start)
                                    ->whereDate('created_at','<=',$this_month_end)
                                    ->sum('request_amount');
        $last_month_pending_donation =  Transaction::toBase()->where('status', 2)
                                            ->where('type', PaymentGatewayConst::TYPEDONATION)
                                            ->whereDate('created_at','>=',$last_month_start)
                                            ->whereDate('created_at','<=',$last_month_end)
                                            ->sum('request_amount');

        $donation_total_balance_p = $donation_total_balance == 0 ? 1 : $donation_total_balance;
        $pending_donation_percent = (($pending_donation_balance * 100) / $donation_total_balance_p);

        if($pending_donation_percent > 100){
            $pending_donation_percent = 100;
        }

        // Charges
        // $total_charge = Transaction::withSum('charge', 'total_charge')->where('status', 1)->get()->sum('charge_sum_total_charge');
        // $all_charge = Transaction::withSum('charge', 'total_charge')->get()->sum('charge_sum_total_charge');
        // $today_charge = Transaction::withSum('charge', 'total_charge')->where('status', 1)->whereDate('created_at', $this_day)->get()->sum('charge_sum_total_charge');
        // $last_month_charge = Transaction::withSum('charge', 'total_charge')
        //                                 ->where('status', 1)
        //                                 ->whereDate('created_at','>=',$last_month_start)
        //                                 ->whereDate('created_at','<=',$last_month_end)
        //                                 ->get()
        //                                 ->sum('charge_sum_total_charge');
        // $charge_percent = (($total_charge * 100) / $all_charge);

        //User
        $total_user = User::toBase()->count();
        $unverified_user = User::toBase()->where('email_verified', 0)->count();
        $active_user = User::toBase()->where('status', 1)->count();
        $banned_user = User::toBase()->where('status', 0)->count();
        $total_user_p = $total_user == 0 ? 1 : $total_user;
        $user_percent =(($active_user * 100) / $total_user_p);

        if($user_percent > 100){
            $user_percent = 100;
        }

        // Subscriber
        $total_subscriber = Subscriber::toBase()->count();
        $today_subscriber = Subscriber::toBase()
                            ->whereDate('created_at','>=',$this_month_start)
                            ->whereDate('created_at','<=',$this_month_end)
                            ->count();
        $last_month_subscriber = Subscriber::toBase()
                                        ->whereDate('created_at','>=',$last_month_start)
                                        ->whereDate('created_at','<=',$last_month_end)
                                         ->count();
        $last_month_subscriber_p = $last_month_subscriber == 0 ? 1 : $last_month_subscriber;
        $subscriber_percent =(($today_subscriber * 100) / $last_month_subscriber_p);

        if($subscriber_percent > 100){
            $subscriber_percent = 100;
        }

        // Monthly Add Money
        $start = strtotime(date('Y-m-01'));
        $end = strtotime(date('Y-m-31'));

        // Add Money
        $pending_data  = [];
        $success_data  = [];
        $canceled_data = [];
        $hold_data     = [];
        // Donation
        $donation_pending_data  = [];
        $donation_success_data  = [];
        $donation_canceled_data = [];
        $donation_hold_data     = [];
        // Event,Campaign,Gellary
        $campaign_data = [];
        $event_data    = [];
        $all_data    = [];

        $month_day  = [];

        while ($start <= $end) {
            $start_date = date('Y-m-d', $start);

            // Monthley add money
            $pending = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                                        ->whereDate('created_at',$start_date)
                                        ->where('status', 2)
                                        ->count();
            $success = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                                        ->whereDate('created_at',$start_date)
                                        ->where('status', 1)
                                        ->count();
            $canceled = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                                        ->whereDate('created_at',$start_date)
                                        ->where('status', 4)
                                        ->count();
            $hold = Transaction::where('type', PaymentGatewayConst::TYPEADDMONEY)
                                        ->whereDate('created_at',$start_date)
                                        ->where('status', 3)
                                        ->count();
            $pending_data[]  = $pending;
            $success_data[]  = $success;
            $canceled_data[] = $canceled;
            $hold_data[]     = $hold;

            //Monthley Donation
            $donation_pending = Transaction::where('type', PaymentGatewayConst::TYPEDONATION)
                        ->whereDate('created_at',$start_date)
                        ->where('status', 2)
                        ->count();
            $donation_success = Transaction::where('type', PaymentGatewayConst::TYPEDONATION)
                        ->whereDate('created_at',$start_date)
                        ->where('status', 1)
                        ->count();
            $donation_canceled = Transaction::where('type', PaymentGatewayConst::TYPEDONATION)
                        ->whereDate('created_at',$start_date)
                        ->where('status', 4)
                        ->count();
            $donatnion_hold = Transaction::where('type', PaymentGatewayConst::TYPEDONATION)
                        ->whereDate('created_at',$start_date)
                        ->where('status', 3)
                        ->count();

            $donation_pending_data[]  = $donation_pending;
            $donation_success_data[]  = $donation_success;
            $donation_canceled_data[] = $donation_canceled;
            $donatnion_hold_data[]    = $donatnion_hold;

            // Event,Campaign,Gallery
            $campaign = Campaign::where('status', 1)
            ->whereDate('created_at',$start_date)
            ->count();
            $event = Event::where('status', 1)
            ->whereDate('created_at',$start_date)
            ->count();

            $campaign_data[] = $campaign;
            $event_data[]    = $event;
            $all_data[]      = $event + $campaign;


            $month_day[] = date('Y-m-d', $start);
            $start = strtotime('+1 day',$start);
        }

        // Chart one
        $chart_one_data = [
            'pending_data'  => $pending_data,
            'success_data'  => $success_data,
            'canceled_data' => $canceled_data,
            'hold_data'     => $hold_data,
        ];
        // Chart two
        $chart_two_data = [
            'pending_data'  => $donation_pending_data,
            'success_data'  => $donation_success_data,
            'canceled_data' => $donation_canceled_data,
            'hold_data'     => $donatnion_hold_data,
        ];
        // Chart three
        $chart_three_data = [
            'campaign_data' => $campaign_data,
            'event'         => $event_data,
            'all_data'      => $all_data,
        ];
        // Chart four | User analysis

        $chart_four = [$active_user, $banned_user,$unverified_user,$total_user];

        // Chart for Donation groth
        $chart_five = [round($today_donation_balance), round($this_week_donation_balance),round($this_month_donation_balance),round($this_year_donation_balance)];

        // Latest transaction
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
        )->where('type', 'donation')->orderBy('id', 'desc')->limit(3)->get();


        $data = [
            'add_money_balance'    => $add_money_balance,
            'today_add_money'      => $today_add_money,
            'last_month_add_money' => $last_month_add_money,
            'add_money_percent'    => $add_money_percent,

            'donation_balance'    => $donation_balance,
            'today_donation'      => $today_donation,
            'last_month_donation' => $last_month_donation,
            'donation_percent'    => $donation_percent,

            'pending_donation_balance'    => $pending_donation_balance,
            'today_pending_donation'      => $today_pending_donation,
            'last_month_pending_donation' => $last_month_pending_donation,
            'pending_donation_percent'    => $pending_donation_percent,

            // 'total_charge'      => $total_charge,
            // 'today_charge'      => $today_charge,
            // 'last_month_charge' => $last_month_charge,
            // 'charge_percent'    => $charge_percent,

            'total_user'      => $total_user,
            'unverified_user' => $unverified_user,
            'active_user'     => $active_user,
            'user_percent'    => $user_percent,

            'total_subscriber'      => $total_subscriber,
            'today_subscriber'      => $today_subscriber,
            'last_month_subscriber' => $last_month_subscriber,
            'subscriber_percent'    => $subscriber_percent,

            'pending_add_money_balance'    => $pending_add_money_balance,
            'today_pending_add_money'      => $today_pending_add_money,
            'last_month_pending_add_money' => $last_month_pending_add_money,
            'pending_add_money_percent'    => $pending_add_money_percent,

            'chart_one_data'   => $chart_one_data,
            'chart_two_data'   => $chart_two_data,
            'chart_three_data' => $chart_three_data,
            'chart_four_data'  => $chart_four,
            'chart_five_data'  => $chart_five,
            'month_day'        => $month_day,

            'transactions'        => $transactions
        ];

        return view('admin.sections.dashboard.index',compact(
            'page_title',
            'data'
        ));
    }


    /**
     * Logout Admin From Dashboard
     * @return view
     */
    public function logout(Request $request) {

        $push_notification_setting = BasicSettingsProvider::get()->push_notification_config;

        if($push_notification_setting) {
            $method = $push_notification_setting->method ?? false;

            if($method == "pusher") {
                $instant_id     = $push_notification_setting->instance_id ?? false;
                $primary_key    = $push_notification_setting->primary_key ?? false;

                if($instant_id && $primary_key) {
                    $pusher_instance = new PushNotifications([
                        "instanceId"    => $instant_id,
                        "secretKey"     => $primary_key,
                    ]);

                    $pusher_instance->deleteUser("".Auth::user()->id."");
                }
            }

        }

        $admin = auth()->user();
        try{
            $admin->update([
                'last_logged_out'   => now(),
                'login_status'      => false,
            ]);
        }catch(Exception $e) {
            // Handle Error
        }

        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }


    /**
     * Function for clear admin notification
     */
    public function notificationsClear() {
        $admin = auth()->user();

        if(!$admin) {
            return false;
        }

        try{
            $admin->update([
                'notification_clear_at'     => now(),
            ]);
        }catch(Exception $e) {
            $error = ['error' => [__('Something went wrong! Please try again')]];
            return Response::error($error,null,404);
        }

        $success = ['success' => [__('Notifications clear successfully!')]];
        return Response::success($success,null,200);
    }
}
