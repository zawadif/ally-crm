<?php

namespace App\Http\Resources\User;

use App\Models\Team;
use App\Models\Purchase;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ExpertyLevels\ExpertyLevelResource;
use App\Http\Resources\ExpertyLevels\ExpertyLevelCollection;
use App\Http\Resources\Participating\ParticipatingCollection;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $team = Team::where('firstMemberId', $this->id)->orWhere('secondMemberId', $this->id)->get();
        $userSeason = UserDetail::with('region', 'region.season', 'region.season.getLadder')->where('userId', $this->id)->first();
        if ($userSeason->region->season) {
            $purchase = Purchase::with('getLadderId')->where('seasonId', $userSeason->region->season->id)->where(function ($q) use ($team) {
                $q->whereIn('teamId', $team->pluck('id'))->orWhere('amountedUserId', $this->id);
            })->get()->unique();
        } else {

            $userSeason = UserDetail::with('region', 'region.seasonPlayOff')->where('userId', $this->id)->first();
            if ($userSeason->region->seasonPlayOff) {
                $purchase = Purchase::with('getLadderId')->where('seasonId', $userSeason->region->seasonPlayOff->id)->where(function ($q) use ($team) {
                    $q->whereIn('teamId', $team->pluck('id'))->orWhere('amountedUserId', $this->id);
                })->get()->unique();
            } else {
                $purchase = array();
            }
        }
        return [
            'totalCredits' => $this->totalCredits ? (float)$this->totalCredits : null,
            'currentlyParticipating' => new ParticipatingCollection($purchase),
            'expertyLevels' => new ExpertyLevelCollection($this->experties),
            'personalInformation' => new UserDetailResource($this),
        ];
    }
}
