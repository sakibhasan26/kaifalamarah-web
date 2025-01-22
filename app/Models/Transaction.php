<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Admin\PaymentGateway;
use App\Constants\PaymentGatewayConst;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\PaymentGatewayCurrency;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $appends = ['stringStatus'];

    protected $casts = [
        'admin_id'                    => 'integer',
        'user_id'                     => 'integer',
        'user_wallet_id'              => 'integer',
        'payment_gateway_currency_id' => 'integer',
        'trx_id'                      => 'string',
        'request_amount'              => 'decimal:8',
        'available_balance'           => 'decimal:8',
        'payable'                     => 'decimal:8',
        'remark'                      => 'string',
        'status'                      => 'integer',
        'details'                     => 'object',
        'reject_reason'               => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_wallet()
    {
        return $this->belongsTo(UserWallet::class, 'user_wallet_id');
    }
    public function currency()
    {
        return $this->belongsTo(PaymentGatewayCurrency::class,'payment_gateway_currency_id');
    }

    public function scopeAuth($query) {
        $query->where("user_id",auth()->user()->id);
    }

    public function getStringStatusAttribute() {
        $status = $this->status;
        $data = [
            'class' => "",
            'value' => "",
        ];
        if($status == PaymentGatewayConst::STATUSSUCCESS) {
            $data = [
                'class'     => "badge badge--success",
                'value'     => __("Success"),
            ];
        }else if($status == PaymentGatewayConst::STATUSPENDING) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => __("Pending"),
            ];
        }else if($status == PaymentGatewayConst::STATUSHOLD) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => __("Hold"),
            ];
        }else if($status == PaymentGatewayConst::STATUSREJECTED) {
            $data = [
                'class'     => "badge badge--danger",
                'value'     => __("Rejected"),
            ];
        }else if($status == PaymentGatewayConst::STATUSWAITING) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => __("Waiting"),
            ];
        }

        return (object) $data;
    }

    public function charge() {
        return $this->hasOne(TransactionCharge::class,"transaction_id","id");
    }

    public function scopeAddMoney($query) {
        return $query->where("type",PaymentGatewayConst::TYPEADDMONEY);
    }

    public function scopeMoneyOut($query) {
        return $query->where("type",PaymentGatewayConst::TYPEMONEYOUT);
    }

    public function gateway_currency() {
        return $this->belongsTo(PaymentGatewayCurrency::class,'payment_gateway_currency_id');
    }

    public function scopeSearch($query,$data) {
        $data = Str::slug($data);
        return $query->where("trx_id","like","%".$data."%")
                    ->orWhere('type', 'like', '%'.$data.'%')
                    ->orderBy('id',"DESC");

    }

    public function scopeMoneyExchange($query) {
        return $query->where("type",PaymentGatewayConst::TYPEMONEYEXCHANGE);
    }

    public function isAuthUser() {
        if($this->user_id === auth()->user()->id) return true;
        return false;
    }

    public function campaign(){
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

    public function getConfirmAttribute()
    {
        if($this->gateway_currency == null) return false;
        if($this->gateway_currency->gateway->isTatum($this->gateway_currency->gateway) && $this->status == PaymentGatewayConst::STATUSWAITING) return true;
    }

    public function getDynamicInputsAttribute()
    {
        if($this->confirm == false) return [];
        $input_fields = $this->details->payment_info->requirements;
        return $input_fields;
    }

    public function getConfirmUrlAddMoneyAttribute()
    {
        if($this->confirm == false) return false;
        return setRoute('api.v1.user.add-money.payment.api.crypto.confirm', $this->trx_id);
    }

    public function getConfirmUrlDonationAttribute()
    {
        if($this->confirm == false) return false;
        return setRoute('api.v1.user.donation.payment.api.crypto.confirm', $this->trx_id);
    }
}
