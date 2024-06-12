<?php

namespace App\Http\Resources\User;

use App\Http\Resources\User\NotificationResource;
use App\Http\Resources\User\SubscriptionResource;
use App\Models\Season;
use App\Models\UserDetail;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $isSeasonExists = true;
        $userDetail = UserDetail::with('region', 'region.season')->where('userId', $this->id)->first();
        if ($userDetail){
            $userRegion = $userDetail->regionId;
            $getSeason = Season::where('isEnded',0)->where('regionId',$userRegion)->first();
            $getPlayoff = Season::where('isPlayOffStarted',1)->where('regionId',$userRegion)->first();
            if(!$getSeason && !$getPlayoff){
                $isSeasonExists = false;
            }
            
        }
        return [
            'id' => $this->id,
            'jwt' => $this->jwt,
            'fullName' => $this->fullName,
            'firebaseCustomToken' => $this->firebaseCustomToken,
            'isSeasonExists' => $isSeasonExists,
            "avatar" => !is_null($this->avatar) ? Storage::disk('s3')->url($this->avatar) : (($this->getUserDetail->gender == 'MALE') ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png'))
        ];
    }
}
