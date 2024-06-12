<?php

namespace App\Http\Resources\Team;

use App\Models\User;
use App\Models\TeamMatch;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class GetTeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $teamType = "DOUBLE";
        if (is_null($this->getsecondMember)) {
            $teamType = "SINGLE";
        }
        $team = [
            'teamId' => $this->id,
            "teamType" => $teamType,
            'firstPlayerName' => $this->getFirstMember->fullName,
            'firstProfileUrl' => $this->getFirstMember ? !is_null($this->getFirstMember->avatar) ? Storage::disk('s3')->url($this->getFirstMember->avatar) : ($this->getFirstMember->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')) : null
        ];

        if (!is_null($this->getsecondMember)) {

            $team += [
                'secondPlayerName' => $this->getSecondMember->fullName,
                'secondProfileUrl' => $this->getSecondMember ? !is_null($this->getSecondMember->avatar) ? Storage::disk('s3')->url($this->getSecondMember->avatar) : ($this->getSecondMember->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')) : null
            ];
        }

        return $team;
    }
}
