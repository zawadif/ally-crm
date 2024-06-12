<?php

namespace App\Http\Resources\Team;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $firstPlayer = User::find($this->firstMemberId);
        $secondPlayer = User::find($this->secondMemberId);

        $team = [
            'teamId' => $this->id ?: null,
            'firstPlayerName' => $firstPlayer ? ($firstPlayer->id == Auth()->id() ? $firstPlayer->fullName . '(You)' : $firstPlayer->fullName) : null,
            'firstProfileUrl' =>  $firstPlayer ? !is_null($firstPlayer->avatar) ? Storage::disk('s3')->url($firstPlayer->avatar) : ($firstPlayer->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')) : null,
        ];
        if (!is_null($this->getsecondMember)) {
            $team += [
                'secondPlayerName' => $secondPlayer ? ($secondPlayer->id == Auth()->id() ? $secondPlayer->fullName . '(You)' : $secondPlayer->fullName) : null,
                'secondProfileUrl' => $secondPlayer ? !is_null($secondPlayer->avatar) ? Storage::disk('s3')->url($secondPlayer->avatar) : ($secondPlayer->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')) : null,
            ];
        }
        return $team;
    }
}
