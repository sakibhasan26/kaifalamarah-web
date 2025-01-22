<?php

namespace App\Http\Controllers\User;

use Exception;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{

    public function slugValue($slug){
        $values = [
            'add-money' => PaymentGatewayConst::TYPEADDMONEY,
        ];

        if(!array_key_exists($slug, $values)) abort(404);

        return $values[$slug];
    }

    /**
     * This method for show the transaction index page
     * @method GET
     * @return Illuminate\Http\Request
     */

    public function index($slug = null){
        if($slug != null){
            $transactions = Transaction::auth()->where('type',$this->slugValue($slug))->orderBy('id', 'desc')->paginate(12);
            $page_title = ucwords(remove_speacial_char($slug," ")) . " Log";
        }else{
            $transactions = Transaction::auth()->orderByDesc("id")->paginate(12);
            $page_title = __('Transaction Log');
        }
        // return $transactions;

        return view('user.sections.transactions.index', compact('transactions', 'page_title'));
    }

    /**
     * This method for show the transaction index page
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request
     */

    public function search(Request $request) {
        // return print_r($request->all());
        $validator = Validator::make($request->all(),[
            'text'  => 'required|string',
        ]);

        if($validator->fails()) {
            return Response::error($validator->errors(),null,400);
        }

        $validated = $validator->validate();

        try{
            $transactions = Transaction::auth()->search($validated['text'])->take(10)->get();
        }catch(Exception $e){
            $error = ['error' => [__('Something went wrong! Please try again')]];
            return Response::error($error,null,500);
        }

        return view('user.components.search.transaction-log',compact('transactions'));
    }
}
