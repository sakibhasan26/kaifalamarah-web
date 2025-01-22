<?php

namespace App\Models;

use App\Constants\GlobalConst;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Admin\Currency;

class UserWallet extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = ['balance', 'status','user_id','currency_id','created_at','updated_at'];

    protected $casts = [
        'user_id'     => 'integer',
        'currency_id' => 'integer',
        'balance'     => 'double',
        'status'      => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function scopeAuth($query) {
        return $query->where('user_id',auth()->guard(get_auth_guard())->user()->id);
    }

    public function scopeActive($query) {
        return $query->where("status",true);
    }


    public function user() {
        return $this->belongsTo(User::class);
    }

    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function scopeSender($query) {
        return $query->whereHas('currency',function($q) {
            $q->where("sender",GlobalConst::ACTIVE);
        });
    }

}
