<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'admin_id'   => 'integer',
        'desc'       => 'object',
        'title'      => 'object',
        'slug'       => 'string',
        'our_goal'   => 'decimal:8',
        'raised'     => 'decimal:8',
        'to_go'      => 'decimal:8',
        'image'      => 'string',
        'status'     => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeGetData($query,$status){
        return $this->where('status', $status);
    }
}
