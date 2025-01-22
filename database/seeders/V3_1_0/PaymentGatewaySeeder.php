<?php

namespace Database\Seeders\V3_1_0;

use App\Models\Admin\PaymentGateway;
use App\Models\Admin\PaymentGatewayCurrency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Perfect
        $perfect_id = PaymentGateway::insertGetId(array('slug' => 'add-money','code' => '3015','type' => 'AUTOMATIC','name' => 'Perfect Money','title' => 'Global Setting for perfect-money in bellow','alias' => 'perfect-money','image' => '21138843-9416-4a5a-9fd9-9383773766b7.webp','credentials' => '[{"label":"USD Account","placeholder":"Enter USD Account","name":"usd-account","value":"U39903302"},{"label":"EUR Account","placeholder":"Enter EUR Account","name":"eur-account","value":"E39620511"},{"label":"Alternate Passphrase","placeholder":"Enter Alternate Passphrase","name":"alternate-passphrase","value":"t0d2nbK2ZA92fRTnIFsMTWsHT"}]','supported_currencies' => '["EUR","USD"]','crypto' => '0','desc' => NULL,'input_fields' => NULL,'status' => '1','last_edit_by' => '1','created_at' => '2024-03-06 07:28:58','updated_at' => '2024-03-06 08:52:47','env' => 'SANDBOX'));

        $payment_gateway_currencies = array(
            array('payment_gateway_id' => $perfect_id,'name' => 'Perfect Money USD','alias' => 'add-money-perfect-money-usd-automatic','currency_code' => 'USD','currency_symbol' => '$','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '1.00000000','fixed_charge' => '1.00000000','rate' => '1.00000000','created_at' => '2024-03-06 08:52:47','updated_at' => '2024-03-06 08:52:47'),
            array('payment_gateway_id' => $perfect_id,'name' => 'Perfect Money EUR','alias' => 'add-money-perfect-money-eur-automatic','currency_code' => 'EUR','currency_symbol' => '€','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '1.00000000','fixed_charge' => '1.00000000','rate' => '0.92000000','created_at' => '2024-03-06 08:52:47','updated_at' => '2024-03-06 08:52:47')
        );

        PaymentGatewayCurrency::insert($payment_gateway_currencies);

        PaymentGateway::where('alias', 'razorpay')->update([
            'supported_currencies' => ["USD","EUR","GBP","SGD","AED","AUD","CAD","CNY","SEK","NZD","MXN","BDT","EGP","HKD","INR","LBP","LKR","MAD","MYR","NGN","NPR","PHP","PKR","QAR","SAR","UZS","GHS"],
            'credentials' =>  '[{"label":"Public Key","placeholder":"Enter Public Key","name":"public-key","value":"rzp_test_voV4gKUbSxoQez"},{"label":"Secret Key","placeholder":"Enter Secret Key","name":"secret-key","value":"cJltc1jy6evA4Vvh9lTO7SWr"}]',
        ]);

    }
}
