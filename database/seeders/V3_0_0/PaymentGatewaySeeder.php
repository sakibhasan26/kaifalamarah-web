<?php

namespace Database\Seeders\V3_0_0;

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

        // Tatum
        $tatum_id = PaymentGateway::insertGetId(array('slug' => 'add-money','code' => '2000','type' => 'AUTOMATIC','name' => 'Tatum','title' => 'Tatum Gateway','alias' => 'tatum','image' => '86b3e20e-c81c-40a2-82a4-094be2afd80d.webp','credentials' => '[{"label":"Mainnet","placeholder":"Enter Mainnet","name":"mainnet","value":"98a2f7f9-7d0f-4730-8df7-41615392656b"},{"label":"Testnet","placeholder":"Enter Testnet","name":"testnet","value":"357d91f6-23cd-43e1-9ec6-a5683c1a6a87"}]','supported_currencies' => '["BTC","ETH","SOL"]','crypto' => '1','desc' => NULL,'input_fields' => NULL,'status' => '1','last_edit_by' => '1','created_at' => '2023-12-21 09:32:19','updated_at' => '2023-12-21 10:13:33','env' => 'SANDBOX'));
        // Coingate
        $coingate_id = PaymentGateway::insertGetId(array('slug' => 'add-money','code' => '2005','type' => 'AUTOMATIC','name' => 'Coingate','title' => 'Coingate Gateway','alias' => 'coingate','image' => '541b9c48-fed3-4da8-b8de-1c13327b0936.webp','credentials' => '[{"label":"Production App Token","placeholder":"Enter Production App Token","name":"production-app-token","value":null},{"label":"Production URL","placeholder":"Enter Production URL","name":"production-url","value":"https:\\/\\/api.coingate.com\\/v2"},{"label":"Sandbox App Token","placeholder":"Enter Sandbox App Token","name":"sandbox-app-token","value":"XJW4RyhT8F-xssX2PvaHMWJjYe5nsbsrbb2Uqy4m"},{"label":"Sandbox URL","placeholder":"Enter Sandbox URL","name":"sandbox-url","value":"https:\\/\\/api-sandbox.coingate.com\\/v2"}]','supported_currencies' => '["USD","BTC","LTC","ETH","BCH","TRX","ETC","DOGE","BTG","BNB","TUSD","USDT","BSV","MATIC","BUSD","SOL","WBTC","RVN","BCD","ATOM","BTTC","EURT"]','crypto' => '1','desc' => NULL,'input_fields' => NULL,'status' => '1','last_edit_by' => '1','created_at' => '2023-12-24 11:59:38','updated_at' => '2023-12-24 12:13:15','env' => 'SANDBOX'));
        // Pagadito
        $pagadito_id = PaymentGateway::insertGetId(array('slug' => 'add-money','code' => '2010','type' => 'AUTOMATIC','name' => 'Pagadito','title' => 'Pagadito Paymnet Gateway','alias' => 'pagadito','image' => 'bda39a56-76c3-4b1d-a66e-cca24fb681be.webp','credentials' => '[{"label":"Live Base URL","placeholder":"Enter Live Base URL","name":"live-base-url","value":"https:\\/\\/pagadito.com"},{"label":"Sandbox Base URL","placeholder":"Enter Sandbox Base URL","name":"sandbox-base-url","value":"https:\\/\\/sandbox.pagadito.com"},{"label":"WSK","placeholder":"Enter WSK","name":"wsk","value":"dc843ff5865bac2858ad8f23af081256"},{"label":"UID","placeholder":"Enter UID","name":"uid","value":"b73eb3fa1dc8bea4b4363322c906a8fd"}]','supported_currencies' => '["USD","HNL","CRC","DOP","GTQ","NIU","PAB"]','crypto' => '0','desc' => NULL,'input_fields' => NULL,'status' => '1','last_edit_by' => '1','created_at' => '2023-12-25 11:33:58','updated_at' => '2023-12-25 11:34:38','env' => 'SANDBOX'));

        $payment_gateway_currencies = array(
            array('payment_gateway_id' => $tatum_id,'name' => 'Tatum SOL','alias' => 'add-money-tatum-sol-automatic','currency_code' => 'SOL','currency_symbol' => 'S/','image' => NULL,'min_limit' => '1.00000000','max_limit' => '2000.00000000','percent_charge' => '2.00000000','fixed_charge' => '0.00000000','rate' => '3.72000000','created_at' => '2023-12-21 10:13:33','updated_at' => '2023-12-21 10:13:33'),
            array('payment_gateway_id' => $tatum_id,'name' => 'Tatum ETH','alias' => 'add-money-tatum-eth-automatic','currency_code' => 'ETH','currency_symbol' => 'Ξ','image' => NULL,'min_limit' => '1.00000000','max_limit' => '2000.00000000','percent_charge' => '2.00000000','fixed_charge' => '0.00000000','rate' => '0.00049000','created_at' => '2023-12-21 10:13:33','updated_at' => '2023-12-21 10:13:33'),
            array('payment_gateway_id' => $tatum_id,'name' => 'Tatum BTC','alias' => 'add-money-tatum-btc-automatic','currency_code' => 'BTC','currency_symbol' => '฿','image' => NULL,'min_limit' => '1.00000000','max_limit' => '2000.00000000','percent_charge' => '2.00000000','fixed_charge' => '0.00000000','rate' => '0.00027000','created_at' => '2023-12-21 10:13:33','updated_at' => '2023-12-21 10:13:33'),
            array('payment_gateway_id' => $coingate_id,'name' => 'Coingate USDT','alias' => 'add-money-coingate-usdt-automatic','currency_code' => 'USDT','currency_symbol' => '₮','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '2.00000000','fixed_charge' => '0.00000000','rate' => '1.00000000','created_at' => '2023-12-24 12:13:15','updated_at' => '2023-12-24 12:13:15'),
            array('payment_gateway_id' => $coingate_id,'name' => 'Coingate DOGE','alias' => 'add-money-coingate-doge-automatic','currency_code' => 'DOGE','currency_symbol' => 'Ð','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '2.00000000','fixed_charge' => '0.00000000','rate' => '10.75000000','created_at' => '2023-12-24 12:13:15','updated_at' => '2023-12-24 12:13:15'),
            array('payment_gateway_id' => $coingate_id,'name' => 'Coingate ETC','alias' => 'add-money-coingate-etc-automatic','currency_code' => 'ETC','currency_symbol' => 'Ξ','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '2.00000000','fixed_charge' => '0.00000000','rate' => '0.04700000','created_at' => '2023-12-24 12:13:15','updated_at' => '2023-12-24 12:13:15'),
            array('payment_gateway_id' => $coingate_id,'name' => 'Coingate TRX','alias' => 'add-money-coingate-trx-automatic','currency_code' => 'TRX','currency_symbol' => 'TRX','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '2.00000000','fixed_charge' => '0.00000000','rate' => '9.43000000','created_at' => '2023-12-24 12:13:15','updated_at' => '2023-12-24 12:13:15'),
            array('payment_gateway_id' => $coingate_id,'name' => 'Coingate BCH','alias' => 'add-money-coingate-bch-automatic','currency_code' => 'BCH','currency_symbol' => 'BCH','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '2.00000000','fixed_charge' => '0.00000000','rate' => '0.00400000','created_at' => '2023-12-24 12:13:15','updated_at' => '2023-12-24 12:13:15'),
            array('payment_gateway_id' => $coingate_id,'name' => 'Coingate ETH','alias' => 'add-money-coingate-eth-automatic','currency_code' => 'ETH','currency_symbol' => 'Ξ','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '2.00000000','fixed_charge' => '0.00000000','rate' => '0.00044000','created_at' => '2023-12-24 12:13:15','updated_at' => '2023-12-24 12:13:15'),
            array('payment_gateway_id' => $coingate_id,'name' => 'Coingate LTC','alias' => 'add-money-coingate-ltc-automatic','currency_code' => 'LTC','currency_symbol' => 'Ł','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '3.00000000','fixed_charge' => '0.00000000','rate' => '0.01400000','created_at' => '2023-12-24 12:13:15','updated_at' => '2023-12-24 12:13:15'),
            array('payment_gateway_id' => $coingate_id,'name' => 'Coingate BTC','alias' => 'add-money-coingate-btc-automatic','currency_code' => 'BTC','currency_symbol' => '฿','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '3.00000000','fixed_charge' => '0.00000000','rate' => '0.00002300','created_at' => '2023-12-24 12:13:15','updated_at' => '2023-12-24 12:13:15'),
            array('payment_gateway_id' => $coingate_id,'name' => 'Coingate USD','alias' => 'add-money-coingate-usd-automatic','currency_code' => 'USD','currency_symbol' => '$','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '2.00000000','fixed_charge' => '0.00000000','rate' => '1.00000000','created_at' => '2023-12-24 12:13:15','updated_at' => '2023-12-24 12:13:15'),
            array('payment_gateway_id' => $pagadito_id,'name' => 'Pagadito USD','alias' => 'add-money-pagadito-usd-automatic','currency_code' => 'USD','currency_symbol' => '$','image' => NULL,'min_limit' => '1.00000000','max_limit' => '1000.00000000','percent_charge' => '2.00000000','fixed_charge' => '0.00000000','rate' => '1.00000000','created_at' => '2023-12-25 11:34:38','updated_at' => '2023-12-25 11:34:38')
        );

        PaymentGatewayCurrency::insert($payment_gateway_currencies);
    }
}
