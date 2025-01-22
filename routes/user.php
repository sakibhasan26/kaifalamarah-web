<?php

use App\Http\Controllers\GlobalController;
use App\Http\Controllers\User\AddMoneyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SupportTicketController;
use App\Http\Controllers\User\TransactionController;

Route::prefix("user")->name("user.")->group(function(){
    Route::controller(DashboardController::class)->group(function(){
        Route::get('dashboard','index')->name('dashboard');
        Route::get('donation-history','donationHistory')->name('donation.history');
        Route::post('logout','logout')->name('logout');
        Route::delete('delete/account','deleteAccount')->name('delete.account')->middleware('app.mode');
    });

    //razorpay callback
    Route::post("razorpay/callback/response/{gateway}",[GlobalController::class,'callback'])->name('razorpay.payment.callback')->withoutMiddleware(['web','auth','verification.guard','user.google.two.factor']);

    // Add Money
    Route::controller(AddMoneyController::class)->prefix('add-money')->name('add.money.')->group(function(){
        Route::get('/', 'index')->name('index');
        Route::post('submit', 'submit')->name('submit');
        Route::get('success/response/{gateway}','success')->name('payment.success');
        Route::get("cancel/response/{gateway}",'cancel')->name('payment.cancel');
        Route::get('payment/{gateway}','payment')->name('payment');
        Route::post('stripe/payment/confirm','paymentConfirmed')->name('stripe.payment.confirmed');
        // FlutterWave Gateway
        Route::get('/flutterwave/callback', 'flutterwaveCallback')->name('flutterwave.callback');

        Route::post("callback/response/{gateway}",'callback')->name('payment.callback')->withoutMiddleware(['web','auth','verification.guard','user.google.two.factor']);
        // Razor

        Route::get('razor/payment/btn/pay/{trx_id}', 'redirectBtnPay')->name('razor.payment.btn.pay');
        Route::post('razor-pay/success/{trx_id}','razorSuccess')->name('razor.success');
        Route::post('razor-pay/cancel/{trx_id}', 'razorCancel')->name('razor.cancel');
        Route::post('razor/callback', 'razorCallback')->name('razor.callback');
        // Stripe
        Route::get('stripe/payment/success/{trx}','stripePaymentSuccess')->name('stripe.payment.success');

        // Qrpay gateway
        Route::get('qrpay/callback', 'qrpayCallback')->name('qrpay.callback');
        Route::get('qrpay/cancel/{trx_id}', 'qrpayCancel')->name('qrpay.cancel');

        // Manual
        Route::get('manual/payment','manualPayment')->name('manual.payment');
        Route::post('manual/payment/confirmed','manualPaymentConfirmed')->name('manual.payment.confirmed');

        //coingate
        Route::match(['get','post'],'coingate/success/response/{gateway}','coinGateSuccess')->name('coingate.payment.success');
        Route::match(['get','post'],"coingate/cancel/response/{gateway}",'coinGateCancel')->name('coingate.payment.cancel');

        // Perfect Money
        Route::get('redirect/form/{gateway}', 'redirectUsingHTMLForm')->name('payment.redirect.form')->withoutMiddleware(['auth','verification.guard','user.google.two.factor']);
        Route::get('perfect.success/response/{gateway}','perfectSuccess')->name('perfect.payment.success');
        Route::get("perfect.cancel/response/{gateway}",'perfectCancel')->name('perfect.payment.cancel');

        // Tatum Payment Gateway
        Route::prefix('payment')->name('payment.')->group(function() {
            Route::get('crypto/address/{trx_id}','cryptoPaymentAddress')->name('crypto.address');
            Route::post('crypto/confirm/{trx_id}','cryptoPaymentConfirm')->name('crypto.confirm');
        });

        // paystack

        Route::get('paystack/callback/response','paystackCallback')->name('paystack.callback.response');

        // Route::post('paystack/webhook','handleWebhook')->name('paystack.webhook');

    });
    // Transaction
    Route::controller(TransactionController::class)->prefix('transactions')->name('transactions.')->group(function(){
        Route::get('/{slug}', 'index')->name('index')->whereIn('slug', ['add-money']);
        Route::post('search', 'search')->name('search');
    });

    Route::controller(ProfileController::class)->prefix("profile")->name("profile.")->group(function(){
        Route::get('/','index')->name('index');
        Route::put('update','update')->name('update')->middleware('app.mode');
        Route::get('change/password','changePassword')->name('change.password')->middleware('app.mode');
        Route::put('password/update','passwordUpdate')->name('password.update')->middleware('app.mode');
    });

    Route::controller(SupportTicketController::class)->prefix("prefix")->name("support.ticket.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('conversation/{encrypt_id}','conversation')->name('conversation');
        Route::post('message/send','messageSend')->name('messaage.send');
    });

});
