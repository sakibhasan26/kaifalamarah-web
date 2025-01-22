<?php
namespace App\Constants;
use App\Models\UserWallet;
use Illuminate\Support\Str;

class PaymentGatewayConst {

    const AUTOMATIC = "AUTOMATIC";
    const MANUAL    = "MANUAL";
    const ADDMONEY  = "Add Money";
    const DONATION  = "Donation";
    const MONEYOUT  = "Money Out";
    const ACTIVE    =  true;

    const NOT_USED  = "NOT-USED";
    const USED      = "USED";
    const SENT      = "SENT";
    const TYPEADDMONEY      = "ADD-MONEY";
    const TYPEDONATION      = "DONATION";
    const TYPEMONEYOUT      = "MONEY-OUT";
    const TYPEWITHDRAW      = "WITHDRAW";
    const TYPECOMMISSION    = "COMMISSION";
    const TYPEBONUS         = "BONUS";
    const TYPETRANSFERMONEY = "TRANSFER-MONEY";
    const TYPEMONEYEXCHANGE = "MONEY-EXCHANGE";
    const TYPEADDSUBTRACTBALANCE = "ADD-SUBTRACT-BALANCE";

    const GUEST = "guest";
    const AUTHENTICATED = "authenticated";

    const FIAT                      = "FIAT";
    const CRYPTO                    = "CRYPTO";
    const CRYPTO_NATIVE             = "CRYPTO_NATIVE";

    const STATUSSUCCESS     = 1;
    const STATUSPENDING     = 2;
    const STATUSHOLD        = 3;
    const STATUSREJECTED    = 4;
    const STATUSWAITING     = 5;

    const ENV_SANDBOX       = "SANDBOX";
    const ENV_PRODUCTION    = "PRODUCTION";

    const PAYPAL        = 'paypal';
    const TATUM         = 'tatum';
    const STRIPE        = 'stripe';
    const MANUA_GATEWAY = 'manual';
    const FLUTTER_WAVE  = 'flutterwave';
    const RAZORPAY      = 'razorpay';
    const SSLCOMMERZ    = 'sslcommerz';
    const QRPAY         = 'qrpay';
    const PAGADITO      = 'pagadito';
    const COINGATE      = 'coingate';
    const PERFECT_MONEY = 'perfect-money';
    const PAYSTACK      = "paystack";

    const SEND = "SEND";
    const RECEIVED = "RECEIVED";

    const ASSET_TYPE_WALLET         = "WALLET";
    const CALLBACK_HANDLE_INTERNAL  = "CALLBACK_HANDLE_INTERNAL";

    public static function add_money_slug() {
        return Str::slug(self::ADDMONEY);
    }
    public static function donation_slug() {
        return Str::slug(self::DONATION);
    }

    public static function money_out_slug() {
        return Str::slug(self::MONEYOUT);
    }

    const REDIRECT_USING_HTML_FORM = "REDIRECT_USING_HTML_FORM";

    public static function register($alias = null) {
        $gateway_alias  = [
            self::PAYPAL => "paypalInit",
            self::STRIPE => "stripeInit",
            self::MANUA_GATEWAY => "manualInit",
            self::FLUTTER_WAVE => 'flutterwaveInit',
            self::RAZORPAY => 'razorInit',
            self::SSLCOMMERZ => 'sslcommerzInit',
            self::QRPAY => "qrpayInit",
            self::TATUM         => 'tatumInit',
            self::COINGATE         => 'coingateInit',
            self::PAGADITO => 'pagaditoInit',
            self::PERFECT_MONEY => 'perfectMoneyInit',
            self::PAYSTACK      => 'paystackInit'
        ];

        if($alias == null) {
            return $gateway_alias;
        }

        if(array_key_exists($alias,$gateway_alias)) {
            return $gateway_alias[$alias];
        }
        return "init";
    }

    const APP       = "APP";
    public static function apiAuthenticateGuard() {
        return [
            'api'   => 'web',
        ];
    }

    public static function registerWallet() {
        return [
            'web'       => UserWallet::class,
            'api'       => UserWallet::class,
        ];
    }

    public static function registerGatewayRecognization() {
        return [
            'isTatum'        => self::TATUM,
            'isCoinGate'     => self::COINGATE,
            'isPerfectMoney' => self::PERFECT_MONEY,
            'isRazorpay'     => self::RAZORPAY,
            'isPayStack'     => self::PAYSTACK,

        ];
    }

    public static function registerRedirection() {
        $donation =[
            'web'    => [
                'address_url'   => 'user.add.money.payment.crypto.address',
                'btn_pay'       => 'donation.razor.payment.btn.pay',
                'return_url'    => 'donation.razor.success',
                'cancel_url'    => 'donation.razor.cancel',
                'callback_url'  => 'razorpay.payment.callback',
            ],
            'api'  =>[
                'btn_pay'      =>  'api.v1.user.razor.payment.btn.pay',
                'return_url'    => 'api.v1.user.donation.razor.payment.success',
                'cancel_url'    => 'api.v1.user.donation.razor.payment.cancel',
                'callback_url'  => 'razorpay.payment.callback',
            ],
        ];

        $addmoney =[
            'web'    => [
                'address_url'   => 'user.add.money.payment.crypto.address',
                'btn_pay'       => 'user.add.money.razor.payment.btn.pay',
                'return_url'    => 'user.add.money.razor.success',
                'cancel_url'    => 'user.add.money.razor.cancel',
                'callback_url'  => 'razorpay.payment.callback',
            ],
            'api'  =>[
                'btn_pay'      =>  'api.v1.user.add-money.razor.payment.btn.pay',
                'return_url'    => 'api.v1.user.add-money.razor.payment.success',
                'cancel_url'    => 'api.v1.user.add-money.razor.payment.cancel',
                'callback_url'  => 'razorpay.payment.callback',
            ],
        ];

        return $data = [
            self::TYPEDONATION  => $donation,
            self::TYPEADDMONEY   => $addmoney
        ];

    }
}
