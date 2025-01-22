<?php

use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->enum('env',[
                PaymentGatewayConst::ENV_SANDBOX,
                PaymentGatewayConst::ENV_PRODUCTION,
            ])->comment("Payment Gateway Environment (Ex: Production/Sandbox)")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->enum('env',[
                PaymentGatewayConst::ENV_SANDBOX,
                PaymentGatewayConst::ENV_PRODUCTION,
            ])->comment("Payment Gateway Environment (Ex: Production/Sandbox)")->nullable();
        });
    }
};
