<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SplashScreenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                  => $this->id,
            'splash_screen_image' => $this->splash_screen_image ?? null,
            'version'             => $this->version ?? null,
            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at
        ];
    }
}
