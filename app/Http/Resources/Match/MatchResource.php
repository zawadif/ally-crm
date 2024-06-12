<?php

namespace App\Http\Resources\Match;

use App\Models\Team;
use App\Models\Country;
use App\Models\Category;
use Kreait\Firebase\Auth;
use App\Enums\MatchTypeEnum;
use App\Http\Resources\Chat\ChatResource;
use App\Enums\MatchStatusEnum;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Team\TeamResource;
use App\Http\Resources\Ladders\LadderCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Match\TeamMember\GetTeamMemberResource;
use App\Http\Resources\Match\TeamMember\GetSecondTeamMemberResource;
use App\Models\Chat;

class MatchResource extends JsonResource
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
        $isPostedByTheUser = false;
        $isCurrentUserWinner = false;
        if ($this->getTable() == "team_matches") {
            if (!is_null($this->matchDetail)) {
                if ($this->matchDetail->scoreCreatedBy != null) {
                    $team = Team::where([
                        'id' => $this->matchDetail->scoreCreatedBy
                    ])->first();
                    if ($team) {
                        if ($team->firstMemberId == Auth()->id() || $team->secondMemberId == Auth()->id())
                            $isPostedByTheUser = true;
                    }
                }
            }
            $winnerTeam = Team::where('ladderId', $this->ladderId)->where([
                'id' => $this->wonTeamId,
            ])->first();
            if ($winnerTeam) {
                if ($winnerTeam->firstMemberId == Auth()->id() || $winnerTeam->secondMemberId == Auth()->id()) {
                    $isCurrentUserWinner = true;
                }
            }
            if ($this->matchableType == MatchTypeEnum::PLAYOFF) {
                $isPlayOff = true;
            }
        }
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
        $inboxChat = $supportChat = null;
        $getInboxChat = Chat::where('type', 'CHAT')->where('matchId', $this->id)->first();
        if ($getInboxChat) {
            $inboxChat = new ChatResource($getInboxChat);
        }
        $getSupportChat = Chat::where('type', 'SUPPORT')->where('matchId', $this->id)->where('userUId', Auth()->user()->uid)->first();
        if ($getSupportChat) {
            $supportChat = new ChatResource($getSupportChat);
        }
        return [
            "address" => $this->address ?: null,
            "id" => $this->teamMatch ? $this->teamMatch->id : $this->id,
            "timeOfMatch" => $this->matchTime ? (string)$this->matchTime : null,
            "dateOfMatch" => $this->matchDay ? (string)$this->matchDay : null,
            "categoryTitle" => $category ? $category->name : null,
            "ladderName" => $this->ladder->name ?: null,
            "isCreatedByTheUser" => $isCreatedByTheUser,
            "isCurrentUserWinner" => $isCurrentUserWinner,
            "isPostedByTheUser" => $isPostedByTheUser,
            "isCurrentUserParticipant" =>  $isCurrentUserParticipant,
            "isPlayOff" => $isPlayOff,
            "firstTeam" => new GetTeamMemberResource($this->firstTeam, $this->matchDetail, $this->wonTeamId, $this),
            "secondTeam" => new GetSecondTeamMemberResource($this->secondTeam, $this->matchDetail, $this->wonTeamId, $this),
            "status" => $this->status ?: null,
            "type" => $this->type ? $this->type : ($this->getTable() == 'proposals' ? MatchTypeEnum::PROPOSAL : MatchTypeEnum::CHALLENGE), "inboxChat" => $inboxChat,
            "supportChat" => $supportChat

        ];
    }
}
