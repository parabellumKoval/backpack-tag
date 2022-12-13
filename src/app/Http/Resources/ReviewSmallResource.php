<?php

namespace Backpack\Reviews\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewSmallResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      return [
        'id' => $this->id,
        'name' => $this->name,
        'likes' => $this->likes,
        'dislikes' => $this->dislikes,
        'text' => $this->text,
        'user' => $this->user,
        'created_at' => $this->created_at
      ];
    }
}
