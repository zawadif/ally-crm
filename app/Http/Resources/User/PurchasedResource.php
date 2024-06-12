<?php

namespace App\Http\Resources\User;

use App\Models\Country;
use App\Models\Season;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchasedResource extends JsonResource
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
            'sessionName' => $this->getSeasonId?($this->getSeasonId->title? : null):null,
            'sessionFee' => $this->price? : null
        ];
    }
}
