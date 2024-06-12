<?php

namespace App\Http\Resources\Notification;

use App\Models\Team;
use App\Models\Ladder;
use App\Models\Category;
use App\Enums\MatchTypeEnum;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Match\TeamMember\GetTeamMemberResource;
use App\Http\Resources\Match\TeamMember\GetSecondTeamMemberResource;

class NotificationMatchResource extends JsonResource
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
        $isPostedByTheUser = false;
        $isCurrentUserWinner = false;

        if ($this->getTable() == "team_matches") {
            if (!is_null($this->matchDetail)) {
                if ($this->matchDetail->scoreCreatedBy != null) {
                    $team = Team::where([
                        'id' => $this->matchDetail->scoreCreatedBy
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
            if ($this->matchableType == MatchTypeEnum::PLAYOFF) {
                $isPlayOff = true;
            }
        }
        $category = Category::find($this->categoryId);
        $ladder = Ladder::find($this->ladderId);
        $isCreatedByTheUser = false;
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


        return [
            "address" => $this->address ?: null,
            "id" => $this->id ?: null,
            'categoryId' => $category->id,
            'categoryName' => $category->name,
            'ladderId' => $this->ladderId,
            'ladderName' => $ladder->name,
            "timeOfMatch" => $this->matchTime ? (string)$this->matchTime : null,
            "dateOfMatch" => $this->matchDay ? (string)$this->matchDay : null,
            "categoryTitle" => $category ? $category->name : null,
            "isCreatedByTheUser" => $isCreatedByTheUser,
            "isCurrentUserWinner" => $isCurrentUserWinner,
            "isPostedByTheUser" => $isPostedByTheUser,
            "isCurrentUserParticipant" =>  $isCurrentUserParticipant,
            "isPlayOff" => $isPlayOff,
            "firstTeam" => new FirstTeamMemberResource($this->firstTeam, $this->matchDetail, $this->wonTeamId, $this, $this->user),
            "secondTeam" => new SecondTeamMemberResource($this->secondTeam, $this->matchDetail, $this->wonTeamId, $this, $this->user),
            "status" => $this->status ?: null,
            "type" => $this->type ? $this->type : ($this->getTable() == 'proposals' ? MatchTypeEnum::PROPOSAL : MatchTypeEnum::CHALLENGE)
        ];
    }
}
