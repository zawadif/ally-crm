<?php

namespace App\Http\Resources\Notification;

use App\Models\Chat;
use App\Models\Team;
use App\Models\Ladder;
use App\Models\Category;
use App\Enums\MatchTypeEnum;
use App\Enums\MatchStatusEnum;
use App\Http\Resources\Chat\ChatResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Match\TeamMember\GetTeamMemberResource;
use App\Http\Resources\Match\TeamMember\GetSecondTeamMemberResource;

class NotificationScheduleMatchResource extends JsonResource
{
    private $user;

    public function __construct($resource, $user)
    {
        // Ensure we call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;
        $this->user = $user; // $apple param passed
    }
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
        $category = Category::find($this->categoryId);
        $ladder = Ladder::find($this->ladderId);
        if (!is_null($matchDetail)) {
            if ($matchDetail['scoreCreatedBy'] != null) {
                $team = Team::where([
                    'id' => $matchDetail['scoreCreatedBy']
                ])->first();
                if ($team) {
                    if ($team->firstMemberId == $this->user || $team->secondMemberId == $this->user)
                        $isPostedByTheUser = true;
                }
            }
        }
        $winnerTeam = Team::where([
            'id' => $this->wonTeamId,
        ])->where('firstMemberId', $this->user)->orWhere('secondMemberId', $this->user)->first();
        if ($winnerTeam) {
            $isCurrentUserWinner = true;
        }
        if ($category->name == 'Single') {
            $teamBy = Team::where('ladderId', $this->ladderId)->where('firstMemberId', $this->user)->where('secondMemberId', null)->first();
        } else {
            $teamBy = Team::where('ladderId', $this->ladderId)->where(function ($q) {
                $q->where('firstMemberId', $this->user)->orWhere('secondMemberId', $this->user);
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
            if ($this->firstTeam->firstMemberId == $this->user || $this->firstTeam->secondMemberId == $this->user) {
                $isCurrentUserParticipant = true;
            }
        }
        if ($isCurrentUserParticipant == false) {
            if ($this->secondTeam) {
                if ($this->secondTeam->firstMemberId == $this->user || $this->secondTeam->secondMemberId == $this->user) {
                    $isCurrentUserParticipant = true;
                }
            }
        }

        $challengeId = $proposalId = null;
        if ($this->matchableType == MatchTypeEnum::PROPOSAL) {
            if ($this->getProposal) {
                $proposalId = $this->getProposal->id;
            }
        }
        if ($this->matchableType == MatchTypeEnum::CHALLENGE) {
            if ($this->getChallenge) {
                $challengeId = $this->getChallenge->id;
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
            'challengeId' => $challengeId,
            'proposalId' => $proposalId,
            'address' => $this->address ?: null,
            'id' => $this->id,
            'categoryId' => $category->id,
            'categoryName' => $category->name,
            'ladderId' => $this->ladderId,
            'ladderName' => $ladder->name,
            "timeOfMatch" => $this->matchTime ? (string)$this->matchTime : null,
            "dateOfMatch" => $this->matchDay ? (string)$this->matchDay : null,
            "categoryTitle" => $this->category->name ?: null,
            "isCreatedByTheUser" => $isCreatedByTheUser,
            "isCurrentUserWinner" => $isCurrentUserWinner,
            "isPostedByTheUser" => $isPostedByTheUser,
            "isCurrentUserParticipant" =>  $isCurrentUserParticipant,
            "isPlayOff" => $isPlayOff,
            'status' => $this->status ? ($this->status == MatchStatusEnum::PLAYED ? $this->status : ($this->status == MatchStatusEnum::SCORE_UPDATED ? MatchStatusEnum::SCORE_UPDATE : $this->status)) : null,
            'matchResult' => !is_null($matchResult) ? $matchResult : 'null',
            'firstTeam' => new FirstTeamMemberResource($this->firstTeam, $this->matchDetail, $this->wonTeamId, $this, $this->user),
            'secondTeam' => $this->secondTeam ? new SecondTeamMemberResource($this->secondTeam, $this->matchDetail, $this->wonTeamId, $this, $this->user) : null,
            'type' => $this->matchableType ? $this->matchableType : ($this->getTable() == 'proposals' ? MatchTypeEnum::PROPOSAL : MatchTypeEnum::CHALLENGE),
            "inboxChat" => $inboxChat,
            "supportChat" => $supportChat
        ];
    }
}
