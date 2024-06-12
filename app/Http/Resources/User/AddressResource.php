<?php

namespace App\Http\Resources\User;

use App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            "completeAddress" => $this->completeAddress ?: null,
            "city" => $this->city ?: null,
            "state" => $this->state ?: null,
            "country" => $this->country,
            "postalCode" => $this->postalCode ?: null
        ];
    }
}