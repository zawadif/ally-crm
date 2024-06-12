<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SigninResource extends JsonResource
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
            'id' => $this->id,
            'fullName' => $this->fullName,
            'email' => $this->email,
            'avatar' => !is_null($this->avatar) ? Storage::disk('s3')->url($this->avatar) : (($this->getUserDetail->gender == 'MALE') ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')),
            'jwt' => $this->jwt,
            'dOb' => $this->getUserDetail ? ($this->getUserDetail->dob ?: null) : null,
            'phoneNumber' => $this->phoneNumber,
            'gender' => $this->getUserDetail ? ($this->getUserDetail->gender ?: null) : null,
            'firebaseCustomToken' => $this->firebaseCustomToken,
            'address' => new AddressResource($this->getUserDetail),
            'totalCredits' => $this->getUserDetail ? ($this->getUserDetail->availableCredits ?: 0) : 0,
            'emergencyContact' => new EmergencyContactResource($this->getUserDetail),
            'purchasedSession' => new PurchasedCollection($this->userBuyingHistories),
        ];
    }
}
