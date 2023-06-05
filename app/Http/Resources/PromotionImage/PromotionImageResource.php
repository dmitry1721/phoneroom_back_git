<?php

namespace App\Http\Resources\PromotionImage;

use Illuminate\Http\Resources\Json\JsonResource;

class PromotionImageResource extends JsonResource
{
    public static $wrap = '';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'path' => $this->path,
        ];
    }
}
