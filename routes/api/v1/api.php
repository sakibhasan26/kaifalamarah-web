<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\AddMoneyController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\AppSettingsController;
use App\Http\Controllers\Api\V1\Auth\AuthorizationController;
use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\DonationController;

Route::name('api.v1.')->group(function(){
    Route::get('languages', [AppSettingsController::class, "languages"])->withoutMiddleware(['system.maintenance.api']);
    Route::get('basic/settings', [AppSettingsController::class, "basicSettings"]);
    // User
    Route::group(['prefix' => 'user', 'as' => 'user.'], function(){
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::get('dashboard', [DashboardController::class, 'dashboard']);

        // Campaiogns
        Route::get('campaigns', [DashboardController::class, 'campaigns']);
        Route::get('campaign/details', [DashboardController::class, 'campaignDetails']);

        Route::get('about-us', [DashboardController::class, 'aboutUs']);

        // donation
        Route::controller(DonationController::class)->group(function(){
            Route::get('campaign/donation/history',  'donationHistory');
            Route::get('campaign/donation/gateway',  'donationGateway');
            Route::post('campaign/donation/submit',  'donationSubmit');
            // Automatic
            Route::get('donation/success/response/{gateway}','success')->name('donation.payment.success');
            Route::get("donation/cancel/response/{gateway}",'cancel')->name('donation.payment.cancel');

            // Flutterwave
            Route::get('/flutterwave/callback', 'flutterwaveCallback')->name('flutterwave.callback');
            Route::post('flutterwave/donation/confirm','flutterwaveConfirmed')->name('flutterwave.donation.confirmed');
            // Stripe
            Route::get('donation/stripe/payment/success/{trx}','stripePaymentSuccess')->name('donation.stripe.payment.success');
            // Manual
            Route::post('manual/donation/confirmed','manualPaymentConfirmedApi')->name('manual.payment.confirmed');

            // rajor pay
            Route::get('razor/payment/btn/pay/{trx_id}', 'redirectBtnPay')->name('razor.payment.btn.pay');
            Route::post('donation/razor/success/response/{gateway}','razorSuccess')->name('donation.razor.payment.success');
            Route::post("donation/razor/cancel/response/{gateway}",'razorCancel')->name('donation.razor.payment.cancel');

            //sslcommerz
            Route::post('donation/sslcommerz/success','sllCommerzSuccess')->name('donation.ssl.success');
            Route::post('donation/sslcommerz/fail','sllCommerzFails')->name('donation.ssl.fail');
            Route::post('donation/sslcommerz/cancel','sllCommerzCancel')->name('donation.ssl.cancel');

            // Qrpay gateway
            Route::get('donation/qrpay/callback', 'qrpayCallback')->name('donation.qrpay.callback');
            Route::get('donation/qrpay/cancel/{trx_id}', 'qrpayCancel')->name('donation.qrpay.cancel');

            //coingate
            Route::match(['get','post'],'donation/coingate/success/response/{gateway}','coinGateSuccess')->name('donation.coingate.payment.success');
            Route::match(['get','post'],"donation/coingate/cancel/response/{gateway}",'coinGateCancel')->name('donation.coingate.payment.cancel');

            // Donation
            Route::get('razor/callback', 'razorCallback')->name('donation.razor.api.razor.callback');
            Route::get('razor/cancel', 'razorCancel')->name('donation.razor.api.razor.cancel');

            //Perfect Money
            Route::get('perfect/success/response/{gateway}','perfectSuccess')->name('donation.perfect.success');
            Route::get("perfect/cancel/response/{gateway}",'perfectCancel')->name('donation.perfect.cancel');

            Route::prefix('donation/payment')->name('donation.payment.')->group(function() {
                Route::post('crypto/confirm/{trx_id}','cryptoPaymentConfirm')->name('api.crypto.confirm');
            });

            // paystack

            Route::post('donation/paystack/confirm','paystackConfirm')->name('donation/paystack.confirm');
            Route::get('donation/paystack/callback/response','paystackCallback')->name('donation.paystack.callback.response')->withoutMiddleware(['auth:api','auth.api','CheckStatusApiUser','verification.guard.api','user.google.two.factor.api']);

        });

         // Events
         Route::get('events', [DashboardController::class, 'events']);
         Route::get('event/details', [DashboardController::class, 'eventDetails']);

        Route::group(['prefix' => 'forgot/password'], function () {
            Route::post('send/otp', [ForgotPasswordController::class, 'sendCode']);
            Route::post('verify',  [ForgotPasswordController::class, 'verifyCode']);
            Route::post('reset', [ForgotPasswordController::class, 'resetPassword']);
        });

        Route::controller(AddMoneyController::class)->prefix('add-money')->name('add-money.')->group(function(){
            Route::get('success/response/{gateway}','success')->name('payment.success');
            Route::get("cancel/response/{gateway}",'cancel')->name('payment.cancel');
            Route::get('/flutterwave/callback', 'flutterwaveCallback')->name('flutterwave.callback');
            Route::get('stripe/payment/success/{trx}','stripePaymentSuccess')->name('stripe.payment.success');

            //sslcommerz
            Route::post('sslcommerz/success','sllCommerzSuccess')->name('ssl.success');
            Route::post('sslcommerz/fail','sllCommerzFails')->name('ssl.fail');
            Route::post('sslcommerz/cancel','sllCommerzCancel')->name('ssl.cancel');

             // Qrpay gateway
             Route::get('qrpay/callback', 'qrpayCallback')->name('qrpay.callback');
             Route::get('qrpay/cancel/{trx_id}', 'qrpayCancel')->name('qrpay.cancel');

            //coingate
            Route::match(['get','post'],'coingate/success/response/{gateway}','coinGateSuccess')->name('coingate.payment.success');
            Route::match(['get','post'],"coingate/cancel/response/{gateway}",'coinGateCancel')->name('coingate.payment.cancel');

            // rajor pay
            Route::get('razor/payment/btn/pay/{trx_id}', 'redirectBtnPay')->name('razor.payment.btn.pay');
            Route::post('donation/razor/success/response/{gateway}','razorSuccess')->name('razor.payment.success');
            Route::post("donation/razor/cancel/response/{gateway}",'razorCancel')->name('razor.payment.cancel');

            //Perfect Money
            Route::get('perfect/success/response/{gateway}','perfectSuccess')->name('perfect.success');
            Route::get("perfect/cancel/response/{gateway}",'perfectCancel')->name('perfect.cancel');

            Route::prefix('payment')->name('payment.')->group(function() {
                Route::post('crypto/confirm/{trx_id}','cryptoPaymentConfirm')->name('api.crypto.confirm');
            });

            Route::get('paystack/callback/response','paystackCallback')->name('paystack.callback.response')->withoutMiddleware(['auth:api','auth.api','CheckStatusApiUser','verification.guard.api','user.google.two.factor.api']);

        });


        Route::middleware('auth.api')->group(function (){
            Route::get('logout', [AuthorizationController::class, 'logout']);
            Route::post('otp/verify', [AuthorizationController::class,'verifyCode']);
            Route::post('resend/code', [AuthorizationController::class,'resendCode']);

            Route::middleware('checkStatusApiUser')->group(function(){

                Route::controller(AddMoneyController::class)->prefix('add-money')->name('add-money.')->group(function(){
                    Route::get('information','addMoneyInformation');
                    Route::post('submit-data','submitData');
                    // Automatic
                    Route::post('stripe/payment/confirm','paymentConfirmedApi')->name('stripe.payment.confirmed');
                    // Manual
                    Route::post('manual/payment/confirmed','manualPaymentConfirmedApi')->name('manual.payment.confirmed');
                });
                // User Profile
                Route::controller(ProfileController::class)->prefix('profile')->group(function(){
                    Route::get('/', 'profile');
                    Route::post('update', 'profileUpdate')->middleware('app.mode');
                    Route::post('password/update', 'passwordUpdate')->middleware('app.mode');
                    Route::post('delete/account', 'deleteAccount')->middleware('app.mode');
                });
            });
        });

    });
});
