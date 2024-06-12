<?php

namespace App\Http\Resources\Challenge;

use App\Models\Team;
use App\Models\Category;
use App\Http\Resources\Team\TeamResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GetChallengeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $isCreatedByTheUser = false;
        $isCurrentUserParticipant = false;
        if ($this->firstTeam) {
            if ($this->firstTeam->firstMemberId == Auth()->id() || $this->firstTeam->secondMemberId == Auth()->id()) {
                $isCurrentUserParticipant = true;
            }
        }
        if ($isCurrentUserParticipant == false) {
            if ($this->secondTeam) {
                if ($this->secondTeam->firstMemberId == Auth()->id() || $this->secondTeam->secondMemberId == Auth()->id()) {
                    $isCurrentUserParticipant = true;
                }
            }
        }
        $category = Category::find($this->categoryId);
        if ($category->name == 'Single') {
            $teamBy = Team::where('ladderId', $this->ladderId)->where('firstMemberId', Auth()->id())->where('secondMemberId', null)->first();
        } else {
            $teamBy = Team::where('ladderId', $this->ladderId)->where(function ($q) {
                $q->where('firstMemberId', Auth()->id())->orWhere('secondMemberId', Auth()->id());
            })->first();
        }
        if ($teamBy) {
            if ($teamBy->id == $this->firstTeam->id) {
                $isCreatedByTheUser = true;
            }
        }
        return [
            'address' => $this->address,
            'id' => $this->id,
            "timeOfMatch" => $this->matchTime ? (string)$this->matchTime : null,
            "dateOfMatch" => $this->matchDay ? (string)$this->matchDay : null,
            "categoryTitle" => $this->category->name,
            "isCreatedByTheUser" => $isCreatedByTheUser,
            "isCurrentUserParticipant" =>  $isCurrentUserParticipant,
            'firstTeam' => new TeamResource($this->firstTeam),
            'secondTeam' => new TeamResource($this->secondTeam),
            'status' => !is_null($this->cancelBy) ? 'REJECTED' : $this->status,
            "type" => "CHALLENGE",
        ];
    }
}
