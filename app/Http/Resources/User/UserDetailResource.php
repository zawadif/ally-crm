<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'fullName' => $this->fullName,
            'email' => $this->email,
            'avatar' => !is_null($this->avatar) ? Storage::disk('s3')->url($this->avatar) : (($this->getUserDetail->gender == 'MALE') ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')),
            'dOb' => $this->getUserDetail ? ($this->getUserDetail->dob ? (int)$this->getUserDetail->dob : null) : null,
            'bio' => $this->getUserDetail ? ($this->getUserDetail->bio ?: null) : null,
            'phoneNumber' => $this->phoneNumber ?: null,
            'gender' => $this->getUserDetail ? ($this->getUserDetail->gender ?: null) : null,
            'regionId' => $this->getUserDetail ? ($this->getUserDetail->region ? $this->getUserDetail->region->id : null) : null,

            'regionName' => $this->getUserDetail ? ($this->getUserDetail->region ? $this->getUserDetail->region->name : null) : null,
            'address' => new AddressResource($this->getUserDetail),
            'emergencyContact' => new EmergencyContactResource($this->getUserDetail),
        ];
    }
}
