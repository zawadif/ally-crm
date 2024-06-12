<?php

namespace App\Http\Resources\Relaucnh;

use Illuminate\Http\Resources\Json\JsonResource;

class RelaunchDataResource extends JsonResource
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
            "categoryId" => $this->getLadderId ? ($this->getLadderId->category ? ($this->getLadderId->category->id ? $this->getLadderId->category->id : null) : null) : null,
            "categoryName" => $this->getLadderId ? ($this->getLadderId->category ? ($this->getLadderId->category->name  ? $this->getLadderId->category->name : null) : null) : null,
            "categoryImage" => $this->getLadderId ? ($this->getLadderId->category ? ($this->getLadderId->category->imageUrl ? $this->getLadderId->category->imageUrl : null) : null) : null,
            "ladders" => $this->getLadderId != null ? [new LadderResource($this->getLadderId)] : null
        ];
    }
}