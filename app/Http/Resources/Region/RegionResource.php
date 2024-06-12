<?php

namespace App\Http\Resources\Region;

use App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class RegionResource extends JsonResource
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
            "regionId"=> $this->id?:null,
            "regionName"=> $this->name?:null,
        ];
    }
}
