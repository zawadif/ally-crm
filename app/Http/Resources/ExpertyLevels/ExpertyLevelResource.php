<?php

namespace App\Http\Resources\ExpertyLevels;

use App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpertyLevelResource extends JsonResource
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
            "expertyId" => $this->id ?: null,
            "expertyName" => $this->type ?: null,
            "expertyLevel" => (float) $this->level ?: null
        ];
    }
}