<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Support\Facades\Validator;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __('All Logs');
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
        )->where('type', 'donation')->orderBy('id', 'desc')->paginate(20);
        return view('admin.sections.donation.index', compact(
            'page_title',
            'transactions'
        ));
    }


    /**
     * Pending Add Money Logs View.
     * @return view $pending-add-money-logs
     */
    public function pending()
    {
        $page_title = __('Pending Logs');
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
        )->where('type', 'donation')->where('status', 2)->paginate(20);
        return view('admin.sections.donation.index', compact(
            'page_title',
            'transactions'
        ));
    }


    /**
     * Complete Add Money Logs View.
     * @return view $complete-add-money-logs
     */
    public function complete()
    {
        $page_title = __("Complete Logs");
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
        )->where('type', 'donation')->where('status', 1)->paginate(20);
        return view('admin.sections.donation.index', compact(
            'page_title',
            'transactions'
        ));
    }

    /**
     * Canceled Add Money Logs View.
     * @return view $canceled-add-money-logs
     */
    public function canceled()
    {
        $page_title = __("Canceled Logs");
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
        )->where('type', 'donation')->where('status', 4)->paginate(20);
        return view('admin.sections.donation.index', compact(
            'page_title',
            'transactions'
        ));
    }

    /**
     * This method for show details of add money
     * @return view $details-add-money-logs
     */
    public function dontaionDetails($id){
        $data = Transaction::where('id',$id)->with(
            'user:id,firstname,email,username,full_mobile',
            'currency:id,name,alias,payment_gateway_id,currency_code,rate',
        )->where('type', 'donation')->first();
        $page_title = __('Add money details for')."".'  '.$data->trx_id;
        return view('admin.sections.donation.details', compact(
            'page_title',
            'data'
        ));
    }

    /**
     * This method for approved add money
     * @method PUT
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request Response
     */
    public function approved(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $transaction = Transaction::where('id',$request->id)->where('status',2)->where('type', 'donation')->first();
        $campaign = Campaign::where('id', $transaction->campaign_id)->first();
        try{
            // Campaign update
            $campaign->update([
                'raised' => $campaign->raised + $transaction->request_amount,
                'to_go'  => $campaign->to_go - $transaction->request_amount
            ]);
            //update transaction
            $transaction->status = 1;
            $transaction->save();

            return redirect()->back()->with(['success' => [__('Donation request approved successfully')]]);
        }catch(Exception $e){
            return back()->with(['error' => [$e->getMessage()]]);
        }
    }

    /**
     * This method for reject add money
     * @method PUT
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request Response
     */
    public function rejected(Request $request){
        $validator = Validator::make($request->all(),[
            'id' => 'required|integer',
            'reject_reason' => 'required|string',
        ]);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $data = Transaction::where('id',$request->id)->where('status',2)->where('type', 'donation')->first();
        $reject['status'] = 4;
        $reject['reject_reason'] = $request->reject_reason;
        try{
            $data->fill($reject)->save();
            return redirect()->back()->with(['success' => [__('Donation request rejected successfully')]]);
        }catch(Exception $e){
            return back()->with(['error' => [$e->getMessage()]]);
        }
    }
}
