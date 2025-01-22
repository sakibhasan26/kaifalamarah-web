<?php

namespace Database\Seeders\V2_0_0;

use Illuminate\Database\Seeder;
use App\Models\Admin\PaymentGateway;
use App\Constants\PaymentGatewayConst;
use App\Models\Admin\PaymentGatewayCurrency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentGateWaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SSL Commerze
        $ssl_commerze_id = PaymentGateway::insertGetId(array('slug' => 'add-money','code' => '1025','type' => 'AUTOMATIC','name' => 'SSLCommerz','title' => 'SSLCommerz Payment Gateway','alias' => 'sslcommerz','image' => '4e294fd2-985f-4632-bc31-89efd6a35be3.webp','credentials' => '[{"label":"Live Url","placeholder":"Enter Live Url","name":"live-url","value":"https:\\/\\/securepay.sslcommerz.com"},{"label":"Sandbox Url","placeholder":"Enter Sandbox Url","name":"sendbox-url","value":"https:\\/\\/sandbox.sslcommerz.com"},{"label":"Store Password","placeholder":"Enter Store Password","name":"store-password","value":"appde6513b3970d62c@ssl"},{"label":"Store ID","placeholder":"Enter Store ID","name":"store-id","value":"appde6513b3970d62c"}]','supported_currencies' => '["BDT","EUR","GBP","AUD","USD","CAD"]','crypto' => '0','desc' => NULL,'input_fields' => NULL,'status' => '1','last_edit_by' => '1','created_at' => '2023-11-02 12:15:27','updated_at' => '2023-11-02 12:29:07','env' => 'SANDBOX'));

        // Razor Pay
        $razorpay_id = PaymentGateway::insertGetId(array('slug' => 'add-money','code' => '1030','type' => 'AUTOMATIC','name' => 'Razor Pay','title' => 'Razorpay Gateway','alias' => 'razorpay','image' => '1fbc6dbe-2920-4b1b-a469-4cde2125aaec.webp','credentials' => '[{"label":"Secret Key","placeholder":"Enter Secret Key","name":"secret-key","value":"s4UYHtNwq5TkHSexU5Pnp1pm"},{"label":"Public Key","placeholder":"Enter Public Key","name":"public-key","value":"rzp_test_B6FCT9ZBZylfoY"}]','supported_currencies' => '["INR"]','crypto' => '0','desc' => NULL,'input_fields' => NULL,'status' => '1','last_edit_by' => '1','created_at' => '2023-11-02 16:31:56','updated_at' => '2023-11-02 16:33:18','env' => 'SANDBOX'));

        // Qrpay
        $qrpay_id = PaymentGateway::insertGetId( array('slug' => 'add-money','code' => '1035','type' => 'AUTOMATIC','name' => 'Qrpay','title' => 'Qrpay Gateway','alias' => 'qrpay','image' => '1183c622-61a8-465f-908f-1a8fecad02d2.webp','credentials' => '[{"label":"Live Base Url","placeholder":"Enter Live Base Url","name":"live-base-url","value":"https:\\/\\/envato.appdevs.net\\/qrpay\\/pay\\/api\\/v1"},{"label":"Sendbox Base Url","placeholder":"Enter Sendbox Base Url","name":"sendbox-base-url","value":"https:\\/\\/envato.appdevs.net\\/qrpay\\/pay\\/sandbox\\/api\\/v1"},{"label":"Client Secret","placeholder":"Enter Client Secret","name":"client-secret","value":"oZouVmqHCbyg6ad7iMnrwq3d8wy9Kr4bo6VpQnsX6zAOoEs4oxHPjttpun36JhGxDl7AUMz3ShUqVyPmxh4oPk3TQmDF7YvHN5M3"},{"label":"Client Id","placeholder":"Enter Client Id","name":"client-id","value":"tRCDXCuztQzRYThPwlh1KXAYm4bG3rwWjbxM2R63kTefrGD2B9jNn6JnarDf7ycxdzfnaroxcyr5cnduY6AqpulRSebwHwRmGerA"}]','supported_currencies' => '["USD"]','crypto' => '0','desc' => NULL,'input_fields' => NULL,'status' => '1','last_edit_by' => '1','created_at' => '2023-11-04 09:58:24','updated_at' => '2023-11-04 10:00:18','env' => 'SANDBOX'));

        PaymentGatewayCurrency::insert(array(
            array('payment_gateway_id' => $ssl_commerze_id,'name' => 'SSLCommerz BDT','alias' => 'add-money-sslcommerz-bdt-automatic','currency_code' => 'BDT','currency_symbol' => '৳','image' => NULL,'min_limit' => '100.00000000','max_limit' => '5000.00000000','percent_charge' => '2.00000000','fixed_charge' => '2.00000000','rate' => '111.00000000','created_at' => '2023-11-02 12:29:07','updated_at' => '2023-11-02 12:29:07'),
            array('payment_gateway_id' => $razorpay_id,'name' => 'Razorpay INR','alias' => 'add-money-razorpay-inr-automatic','currency_code' => 'INR','currency_symbol' => '₹','image' => NULL,'min_limit' => '100.00000000','max_limit' => '10000.00000000','percent_charge' => '2.00000000','fixed_charge' => '2.00000000','rate' => '82.87000000','created_at' => '2023-11-02 16:33:18','updated_at' => '2023-11-02 16:33:18'),
            array('payment_gateway_id' => $qrpay_id,'name' => 'Qrpay USD','alias' => 'add-money-qrpay-usd-automatic','currency_code' => 'USD','currency_symbol' => '$','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '2.00000000','fixed_charge' => '1.00000000','rate' => '1.00000000','created_at' => '2023-11-04 10:00:18','updated_at' => '2023-11-04 10:00:18')
        ));
    }
}
