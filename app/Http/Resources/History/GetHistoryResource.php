<?php

namespace App\Http\Resources\History;

use App\Models\Team;
use App\Models\Category;
use App\Enums\MatchStatusEnum;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Match\TeamMember\GetTeamMemberResource;
use App\Http\Resources\Match\TeamMember\GetSecondTeamMemberResource;

class GetHistoryResource extends JsonResource
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

        if ($this->matchableType == 'PLAYOFF') {
            $isPlayOff = true;
        }

        $matchResult = null;
        if ($this->wonTeamId != null) {
            $wonTeamIds = Team::where('ladderId', $request->ladderId)->where('firstMemberId', Auth()->id())->orWhere('secondMemberId', Auth()->id())->first();
            if ($wonTeamIds) {
                if ($this->wonTeamId == $wonTeamIds->id) {
                    $matchResult = 'WON';
                } else {
                    $matchResult = 'LOSS';
                }
            }
        }

        return [
            'address' => $this->address,
            "timeOfMatch" => (string)$this->matchTime,
            "dateOfMatch" => (string)$this->matchDay,
            "categoryTitle" => $this->category->name,
            "isCreatedByTheUser" => $isCreatedByTheUser,
            "isPlayOff" => $isPlayOff,
            'status' => $this->status == MatchStatusEnum::PLAYED || $this->status == MatchStatusEnum::SCORE_UPDATED ? MatchStatusEnum::SCORE_UPDATE : $this->status,
            'matchResult' => $matchResult,
            'firstTeam' => new GetTeamMemberResource(Team::find($this->teamOneId), $this->matchDetail, $this->wonTeamId, $this),
            'secondTeam' => new GetSecondTeamMemberResource(Team::find($this->teamTwoId), $this->matchDetail, $this->wonTeamId, $this),
            'type' => $this->matchableType,
        ];
    }
}
