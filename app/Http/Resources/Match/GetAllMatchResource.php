<?php

namespace App\Http\Resources\Match;

use App\Models\Team;
use App\Models\Category;
use App\Enums\MatchTypeEnum;
use App\Enums\MatchStatusEnum;
use App\Http\Resources\Team\TeamResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Match\TeamMember\GetTeamMemberResource;
use App\Http\Resources\Match\TeamMember\GetSecondTeamMemberResource;

class GetAllMatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $isPlayOff = false;
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

        if ($this->matchableType == MatchTypeEnum::PLAYOFF) {
            $isPlayOff = true;
        }

        return [
            'address' => $this->address ?: null,
            'id' => $this->id,
            "timeOfMatch" => $this->matchTime ? (string)$this->matchTime : null,
            "dateOfMatch" => $this->matchDay ? (string)$this->matchDay : null,
            "categoryTitle" => $this->category->name ?: null,
            "isCreatedByTheUser" => $isCreatedByTheUser,
            "isPlayOff" => $isPlayOff,
            'firstTeam' =>  new GetTeamMemberResource($this->firstTeam, $this->matchDetail, $this->wonTeamId, $this),
            'secondTeam' => new GetSecondTeamMemberResource($this->secondTeam, $this->matchDetail, $this->wonTeamId, $this),
            "status" => $this->status == MatchStatusEnum::PLAYED || $this->status == MatchStatusEnum::SCORE_UPDATED ? MatchStatusEnum::SCORE_UPDATE : $this->status,
            "type" => $this->matchableType ? $this->matchableType : ($this->getTable() == 'proposals' ? MatchTypeEnum::PROPOSAL : MatchTypeEnum::CHALLENGE),

        ];
    }
}
