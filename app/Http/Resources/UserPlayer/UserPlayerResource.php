<?php

namespace App\Http\Resources\UserPlayer;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserPlayerResource extends JsonResource
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
            "userId" => $this->id ?: null,
            "fullName" => $this->fullName ?: null,
            "avatar" => !is_null($this->avatar) ? Storage::disk('s3')->url($this->avatar) : (($this->getUserDetail->gender == 'MALE') ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png'))
        ];
    }
}
