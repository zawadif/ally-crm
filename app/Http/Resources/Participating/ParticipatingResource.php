<?php

namespace App\Http\Resources\Participating;

use App\Models\Team;
use App\Models\Country;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipatingResource extends JsonResource
{


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->purchasedType == 'CARD'){
            $total = $this->price + $this->creditAmount;
        }else{
            $total = $this->price;
        }
        $credit = $this ?  number_format((float)$total, 3, '.', '') : 0.00;
        return [
            "ladderName" => $this->getLadderId ? ($this->getLadderId->name ?: null) : null,
            "credits" =>  floatval($credit),
        ];
    }
}
