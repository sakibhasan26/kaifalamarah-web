<?php

namespace Database\Seeders\Update;

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

        $payment_gateways = array(
            array('id' => '1001','slug' => 'add-money','code' => '2020','type' => 'AUTOMATIC','name' => 'Paystack','title' => 'Paystack Gateway','alias' => 'paystack','image' => '14790a56-4945-478b-91fa-c32af8499357.webp','credentials' => '[{"label":"Public Key","placeholder":"Enter Public Key","name":"public-key","value":"pk_test_64a32791e5d7acc43acafb3646a1b9ce898519ea"},{"label":"Secret Key","placeholder":"Enter Secret Key","name":"secret-key","value":"sk_test_d094bb8359027eab06ca8ea9a3b757e47e35684b"}]','supported_currencies' => '["NGN","USD","GHS","ZAR","KES"]','crypto' => '0','desc' => NULL,'input_fields' => NULL,'status' => '1','last_edit_by' => '1','created_at' => '2024-10-14 11:11:07','updated_at' => '2024-10-14 11:11:19','env' => 'SANDBOX')
        );
        PaymentGateway::upsert($payment_gateways,['code'],[]);

        $payment_gateway_currencies = array(
            array('payment_gateway_id' => '1001','name' => 'Paystack KES','alias' => 'add-money-paystack-kes-automatic','currency_code' => 'KES','currency_symbol' => 'KSh','image' => NULL,'min_limit' => '100.00000000','max_limit' => '1000.00000000','percent_charge' => '1.00000000','fixed_charge' => '1.00000000','rate' => '129.00000000','created_at' => '2024-10-14 11:14:13','updated_at' => '2024-10-14 11:14:13'),
            array('payment_gateway_id' => '1001','name' => 'Paystack ZAR','alias' => 'add-money-paystack-zar-automatic','currency_code' => 'ZAR','currency_symbol' => 'R','image' => NULL,'min_limit' => '20.00000000','max_limit' => '1000.00000000','percent_charge' => '1.00000000','fixed_charge' => '1.00000000','rate' => '17.73000000','created_at' => '2024-10-14 11:14:13','updated_at' => '2024-10-14 11:14:13'),
            array('payment_gateway_id' => '1001','name' => 'Paystack GHS','alias' => 'add-money-paystack-ghs-automatic','currency_code' => 'GHS','currency_symbol' => 'GH₵','image' => NULL,'min_limit' => '100.00000000','max_limit' => '1000.00000000','percent_charge' => '1.00000000','fixed_charge' => '1.00000000','rate' => '15.59000000','created_at' => '2024-10-14 11:14:13','updated_at' => '2024-10-14 11:14:13'),
            array('payment_gateway_id' => '1001','name' => 'Paystack USD','alias' => 'add-money-paystack-usd-automatic','currency_code' => 'USD','currency_symbol' => '$','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '1.00000000','fixed_charge' => '1.00000000','rate' => '1.00000000','created_at' => '2024-10-14 11:14:13','updated_at' => '2024-10-14 11:14:13'),
            array('payment_gateway_id' => '1001','name' => 'Paystack NGN','alias' => 'add-money-paystack-ngn-automatic','currency_code' => 'NGN','currency_symbol' => '₦','image' => NULL,'min_limit' => '1000.00000000','max_limit' => '10000.00000000','percent_charge' => '1.00000000','fixed_charge' => '1.00000000','rate' => '1621.00000000','created_at' => '2024-10-14 11:14:13','updated_at' => '2024-10-14 11:14:13')

        );

        PaymentGatewayCurrency::upsert($payment_gateway_currencies,['alias'],[]);

    }
}
