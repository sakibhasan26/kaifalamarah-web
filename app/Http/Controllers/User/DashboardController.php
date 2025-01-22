<?php
namespace App\Http\Controllers\User;

use Exception;
use App\Models\Campaign;
use App\Models\UserWallet;
use App\Models\Admin\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        $page_title = __('Dashboard');

        // Box amounts
        $balance = UserWallet::where('user_id', auth()->user()->id)->first()->balance;

        $donation_amount = Transaction::Auth()->where('type', PaymentGatewayConst::TYPEDONATION)->where('status', 1)->sum('request_amount');
        $add_money_amount = Transaction::Auth()->where('type', PaymentGatewayConst::TYPEADDMONEY)->where('status', 1)->sum('request_amount');
        $last_donation_amount = Transaction::Auth()->where('type', PaymentGatewayConst::TYPEDONATION)->where('status', 1)->orderBy('id', 'desc')->first();
        $all_time_risede = Transaction::where('type', PaymentGatewayConst::TYPEDONATION)->where('status', 1)->sum('request_amount');
        $total_event = Event::toBase()->where('status', 1)->count();
        $total_donate_time = Transaction::Auth()->where('type', PaymentGatewayConst::TYPEDONATION)->where('status', 1)->count();

        $campaign_total = Campaign::toBase()->where('status', 1)->count();

        // Canva js data

        $start = strtotime(date('Y-01-01'));
        $end = strtotime(date('Y-12-31'));

        $donate_chart = [];
        $donate_month = [];

        while ($start < $end) {
            $start_date = date('Y-m', $start).'-01';
            $end_date = date('Y-m', $start).'-31';

            $amount = Transaction::Auth()
                                        ->where('type', PaymentGatewayConst::TYPEDONATION)
                                        ->whereDate('created_at','>=',$start_date)
                                        ->whereDate('created_at','<=',$end_date)
                                        ->where('status', 1)
                                        ->sum('request_amount');

            $donate_chart[] = number_format($amount);
            $donate_month[] = date('Y-m-d', $start);

            $start = strtotime('+1 month',$start);
        }

        //Donation history
        $donation_history = Transaction::with('campaign')->orderBy('id', 'desc')->Auth()->where('type', PaymentGatewayConst::TYPEDONATION)->limit(3)->get();

        $data = [
            'balance'              => $balance,
            'donation_amount'      => $donation_amount,
            'campaign_total'       => $campaign_total,
            'total_donate_time'    => $total_donate_time,
            'last_donation_amount' => $last_donation_amount->request_amount ?? 0,
            'all_time_risede'      => $all_time_risede,
            'total_event'          => $total_event,
            'donate_chart'         => $donate_chart,
            'donate_month'         => $donate_month,
            'donation_history'     => $donation_history,
            'add_money_amount'     => $add_money_amount,
        ];

        return view('user.dashboard',compact("page_title", "data"));
    }

    public function donationHistory(){
        $page_title = __('Donation History');

        $donation_history = Transaction::with('campaign')->orderBy('id', 'desc')->Auth()->where('type', PaymentGatewayConst::TYPEDONATION)->paginate(12);

        return view('user.donation-history',compact("page_title", "donation_history"));
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('user.login')->with(['success' => [__('Logout Successfully')]]);
    }

    public function deleteAccount(Request $request) {
        $validator = Validator::make($request->all(),[
            'target'        => 'required',
        ]);
        $validated = $validator->validate();
        $user = auth()->user();
        try{
            $user->status = 0;
            $user->deleted_at = now();
            $user->save();
            Auth::logout();
            return redirect()->route('index')->with(['success' => [__('Your account deleted successfully!')]]);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
    }

}
