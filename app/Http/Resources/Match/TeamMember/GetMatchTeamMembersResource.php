<?php

namespace App\Http\Resources\Match\TeamMember;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class GetMatchTeamMembersResource extends JsonResource
{
    public function toArray($request)
    {        
        return [
            "avatar" => !is_null($this->avatar) ? Storage::disk('s3')->url($this->avatar) : asset('img/avatar/maleAvatar.png'),
            "fullName" => $this->fullName,
            "userUid" => $this->uid
        ];
    }
}