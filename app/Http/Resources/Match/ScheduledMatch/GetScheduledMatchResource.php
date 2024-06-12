<?php

namespace App\Http\Resources\Match\ScheduledMatch;

use App\Models\Chat;
use App\Models\Team;
use App\Models\Category;
use App\Enums\MatchTypeEnum;
use App\Enums\MatchStatusEnum;
use App\Http\Resources\Chat\ChatResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Match\TeamMember\GetTeamMemberResource;
use App\Http\Resources\Match\TeamMember\GetSecondTeamMemberResource;

class GetScheduledMatchResource extends JsonResource
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
        $matchResult = null;
        $isCreatedByTheUser = false;
        $isCurrentUserWinner = false;
        $matchDetail = $this->matchDetail;
        $isPostedByTheUser = false;
        if (!is_null($matchDetail)) {
            if ($matchDetail['scoreCreatedBy'] != null) {
                $team = Team::where([
                    'id' => $matchDetail['scoreCreatedBy']
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
        if ($this->wonTeamId != null) {
            $matchResult = 'WON';
        }

        if ($this->matchableType == MatchTypeEnum::PLAYOFF) {
            $isPlayOff = true;
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
        $getSupportChat = Chat::where('type', 'SUPPORT')->where('matchId', $this->id)->first();
        if ($getSupportChat) {
            $supportChat = new ChatResource($getSupportChat);
        }

        return [
            'address' => $this->address ?: null,
            'id' => $this->id,
            "timeOfMatch" => $this->matchTime ? (string)$this->matchTime : null,
            "dateOfMatch" => $this->matchDay ? (string)$this->matchDay : null,
            "categoryTitle" => $this->category->name ?: null,
            "ladderName" => $this->ladder->name ?: null,
            "isCreatedByTheUser" => $isCreatedByTheUser,
            "isCurrentUserWinner" => $isCurrentUserWinner,
            "isPostedByTheUser" => $isPostedByTheUser,
            "isCurrentUserParticipant" =>  $isCurrentUserParticipant,
            "isPlayOff" => $isPlayOff,
            'status' => $this->status ? ($this->status == MatchStatusEnum::PLAYED || $this->status == MatchStatusEnum::SCORE_UPDATED ? MatchStatusEnum::SCORE_UPDATE : $this->status) : null,
            'matchResult' => !is_null($matchResult) ? $matchResult : 'null',
            'firstTeam' => new GetTeamMemberResource($this->firstTeam, $this->matchDetail, $this->wonTeamId, $this),
            'secondTeam' => $this->secondTeam ? new GetSecondTeamMemberResource($this->secondTeam, $this->matchDetail, $this->wonTeamId, $this) : null,
            'type' => $this->matchableType ? $this->matchableType : ($this->getTable() == 'proposals' ? MatchTypeEnum::PROPOSAL : MatchTypeEnum::CHALLENGE),
            "inboxChat" => $inboxChat,
            "supportChat" => $supportChat
        ];
    }
}
