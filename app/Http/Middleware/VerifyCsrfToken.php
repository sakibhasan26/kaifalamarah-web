<?php

namespace App\Http\Middleware;

use App\Constants\PaymentGatewayConst;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'user/username/check',
        'user/check/email',
        // Add Money
        '/add-money/sslcommerz/success',
        '/add-money/sslcommerz/cancel',
        '/add-money/sslcommerz/fail',
        '/api/v1/user/add-money/sslcommerz/success',
        '/api/v1/user/add-money/sslcommerz/cancel',
        '/api/v1/user/add-money/sslcommerz/fail',
        // Donation
        '/donation/sslcommerz/success',
        '/donation/sslcommerz/cancel',
        '/donation/sslcommerz/fail',

        'donation/razor-pay/success/'. PaymentGatewayConst::RAZORPAY,
        'donation/razor-pay/cancel/'. PaymentGatewayConst::RAZORPAY,
        'user/add-money/razor-pay/success/'. PaymentGatewayConst::RAZORPAY,
        'user/add-money/razor-pay/cancel/'. PaymentGatewayConst::RAZORPAY,


    ];
}
