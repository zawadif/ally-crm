<?php

namespace App\Http\Resources\Relaucnh;

use App\Models\Purchase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class LadderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $participants = Purchase::where('purchasedStatus', 'Purchased')->where('ladderId', $this->id)->where('paidAmount', '!=', null)->count();
        $ladderImage = null;
        if ($this->seasonGenderType == 'Men') {
            if ($this->categoryId == 1) {
                $ladderImage = asset('img/ladders/maleSingle.png');
            } else if ($this->categoryId == 2) {
                $ladderImage = asset('img/ladders/maleDouble.png');
            } else {
                $ladderImage = asset('img/ladders/mixDouble.png');
            }
        } else if ($this->seasonGenderType == 'Women') {
            if ($this->categoryId == 1) {
                $ladderImage = asset('img/ladders/maleSingle.png');
            } else if ($this->categoryId == 2) {
                $ladderImage = asset('img/ladders/maleDouble.png');
            } else {
                $ladderImage = asset('img/ladders/mixDouble.png');
            }
        } else {
            if ($this->categoryId == 1) {
                $ladderImage = asset('img/ladders/maleSingle.png');
            } else if ($this->categoryId == 2) {
                $ladderImage = asset('img/ladders/maleDouble.png');
            } else {
                $ladderImage = asset('img/ladders/mixDouble.png');
            }
        }
        return [
            "ladderId" => $this->id,
            "ladderName" => $this->name,
            "ladderImage" => !is_null($this->image) ? Storage::disk('s3')->url($this->image) : $ladderImage,
            "numberOfParticipants" => (int)$participants,
            "price" => (float)$this->price,
            "isPurchased" => true
        ];
    }
}
