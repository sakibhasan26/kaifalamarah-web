<?php

namespace App\Models;

use App\Models\Admin\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryType extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = "category_types";

    protected $casts = [
        'id'         => 'integer',
        'name'       => 'string',
        'data'       => 'object',
        'slug'       => 'string',
        'type'       => 'integer',
        'status'     => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'editData',
    ];

    public function getEditDataAttribute() {

        $data = [
            'id'      => $this->id,
            'name'      => $this->name,
            'slug'      => $this->slug,
            'type'      => $this->type,
            'status'      => $this->status,
        ];

        return json_encode($data);
    }

    public function scopeSearch($query,$text) {
        $query->Where("name","like","%".$text."%");
    }

    public function events(){
        return $this->hasMany(Event::class, 'category_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

}
