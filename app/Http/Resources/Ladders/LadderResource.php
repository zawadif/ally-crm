<?php

namespace App\Http\Resources\Ladders;

use App\Models\Country;
use App\Models\Purchase;
use App\Models\Team;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
        $participants = Purchase::where('ladderId', $this->id)->count();
        $isPurchased = false;
        $teamIds = Team::where('ladderId', $this->id)->where('firstMemberId', Auth()->id())->orWhere('secondMemberId', Auth()->id())->pluck('id')->toArray();
        if (Purchase::where('ladderId', $this->id)->whereIn('teamId', $teamIds)->first()) {
            $isPurchased = true;
        } elseif(Purchase::where('ladderId', $this->id)->where('amountedUserId', Auth()->id())->first()){
            $isPurchased = true;
        }
        $ladderImage = null;
        if ($this->seasonGenderType == 'Men') {
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
            "ladderId" => $this->id ?: null,
            "ladderName" => $this->name ?: null,
            "ladderImage" => !is_null($this->image) ? Storage::disk('s3')->url($this->image) : $ladderImage,
            "numberOfParticipants" => $participants,
            "price" => $this->price ? (float)$this->price : null,
            "isPurchased" => $isPurchased
        ];
    }
}
