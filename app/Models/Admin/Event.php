<?php

namespace App\Models\Admin;

use App\Models\CategoryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts =[
        'id'           => 'integer',
        'admin_id'     => 'integer',
        'category_id ' => 'integer',
        'title'        => 'object',
        'details'      => 'object',
        'tags'         => 'object',
        'slug'         => 'string',
        'image'        => 'string',
        'status'       => 'integer',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    public function category(){
        return $this->belongsTo(CategoryType::class, 'category_id', 'id');
    }
}
