<?php

namespace App\Http\Resources\User;

use App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class EmergencyContactResource extends JsonResource
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
            'userName' => $this->emergencyContactName?:null,
            'relationship' => $this->emergencyContactRelation?:null,
            'phoneNumber' => $this->emergencyContactNumber?:null
        ];
    }
}
