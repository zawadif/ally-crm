<?php

namespace App\Http\Resources\AllPlayersRanking;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AllPlayersRankingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->category->name == 'Single') {
            return [
                "category" => $this->category->name,
                "firstPlayerName" => $this->team->getFirstMember->fullName,
                "firstPlayerAvatar" =>   $this->team->getFirstMember ? !is_null($this->team->getFirstMember->avatar) ? Storage::disk('s3')->url($this->team->getFirstMember->avatar) : ($this->team->getFirstMember->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')) : null,
                "rank" => $this->rank,
                "totalMatchWon" => $this->matchWon,
                "totalMatchLost" => $this->matchLose,
                "totalPoints" => $this->points
            ];
        } else {
            return [
                "category" => $this->category->name,
                "firstPlayerName" => $this->team->getFirstMember->fullName,
                "firstPlayerAvatar" => $this->team->getFirstMember ? !is_null($this->team->getFirstMember->avatar) ? Storage::disk('s3')->url($this->team->getFirstMember->avatar) : ($this->team->getFirstMember->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')) : null,
                "secondPlayerName" => $this->team->getSecondMember->fullName,
                "secondPlayerAvatar" => $this->team->getSecondMember ? !is_null($this->team->getSecondMember->avatar) ? Storage::disk('s3')->url($this->team->getSecondMember->avatar) : ($this->team->getSecondMember->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')) : null,
                "rank" => $this->rank,
                "totalMatchWon" => $this->matchWon,
                "totalMatchLost" => $this->matchLose,
                "totalPoints" => $this->points
            ];
        }
    }
}
