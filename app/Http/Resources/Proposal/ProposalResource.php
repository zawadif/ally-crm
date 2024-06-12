<?php

namespace App\Http\Resources\Proposal;

use App\Models\Team;
use App\Models\Category;
use App\Enums\MatchTypeEnum;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Team\TeamResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Match\TeamMember\GetTeamMemberResource;

class ProposalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $category = Category::find($this->categoryId);
        $isCreatedByTheUser = false;
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
            'address' => $this->address ?: null,
            'id' => $this->teamMatch ? $this->teamMatch->id : $this->id,
            "timeOfMatch" => $this->matchTime ? (string)$this->matchTime : null,
            "dateOfMatch" => $this->matchDay ? (string)$this->matchDay : null,
            "categoryTitle" => $this->category ? $this->category->name : null,
            "isCreatedByTheUser" => $isCreatedByTheUser,
            'firstTeam' => $this->firstTeam ? new TeamResource($this->firstTeam) : null,
            'secondTeam' => $this->teamMatch ? new TeamResource(
                $this->teamMatch->secondTeam
            ) : null,
            "status" => $this->status,
            "type" =>  MatchTypeEnum::PROPOSAL,
        ];
    }
}
