<?php

namespace App\Http\Resources\Ladders;

use App\Enums\LadderPlayoffStatusEnum;
use App\Enums\MatchStatusEnum;
use App\Enums\MatchTypeEnum;
use App\Enums\PlayOffTypeEnum;
use App\Models\Country;
use App\Models\PlayoffWinner;
use App\Models\Purchase;
use App\Models\Team;
use App\Models\TeamMatch;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PlayoffLadderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $ladderStatus = null;
        if ($this->getPurchaseLadder) {
            if ($this->getPurchaseLadder->playOffStatus == LadderPlayoffStatusEnum::ROUND_OF_16) {
                $ladderStatus = 'Round of 16 is on going.';
            }
            if ($this->getPurchaseLadder->playOffStatus == LadderPlayoffStatusEnum::QUARTER_FINAL) {
                $ladderStatus = 'Quarter final is on going.';
            }
            if ($this->getPurchaseLadder->playOffStatus == LadderPlayoffStatusEnum::SEMI_FINAL) {
                $ladderStatus = 'Semi Final is on going.';
            }
            if ($this->getPurchaseLadder->playOffStatus == LadderPlayoffStatusEnum::FINAL) {
                $ladderStatus = 'Final is on going.';
            }
            if ($this->getPurchaseLadder->playOffStatus == LadderPlayoffStatusEnum::WINNER) {
                $playerName = null;
                $playWinner = PlayoffWinner::where('ladderId', $this->getPurchaseLadder->id)->first();
                if ($playWinner) {
                    if ($playWinner->getTeam) {
                        if ($this->getPurchaseLadder->category->id == 1) {
                            $playerName = $playWinner->getTeam->getFirstMember->fullName;
                        } else {
                            $playerName .= " & " . $playWinner->getTeam->getSecondMember->fullName;
                        }
                    } else {
                        $playerName = "No participants";
                    }

                    if ($playWinner->getTeam && $playWinner->getTeam->id == $this->teamId) {
                        $playerName .= "(You) ";
                    }
                }

                $ladderStatus = $playerName . ' is Winner';
            }
        }

        $latestPlayoffMatch = TeamMatch::where(function ($q) {
            $q->where('teamOneId', $this->teamId)->orWhere('teamTwoId', $this->teamId);
        })
            ->where('matchableType', MatchTypeEnum::PLAYOFF)
            ->where('ladderId', $this->getPurchaseLadder->id)
            ->where('regionId', $this->getSeasonId->regionId)
            ->where('playOffStatus', MatchStatusEnum::PENDING)
            ->latest()
            ->first();
        $playerStatus = null;
        if ($latestPlayoffMatch) {
            if ($latestPlayoffMatch->playOfType == PlayOffTypeEnum::POOL) {
                $playerStatus = 'Qualified for Round of 16';
            }
            if ($latestPlayoffMatch->playOfType == PlayOffTypeEnum::QATAR_FINAL) {
                $playerStatus = 'Qualified for Quarter Final';
            }
            if ($latestPlayoffMatch->playOfType == PlayOffTypeEnum::SEMI_FINAL) {
                $playerStatus = 'Qualified for Semi final';
            }
            if ($latestPlayoffMatch->playOfType == PlayOffTypeEnum::FINAL) {
                $playerStatus = 'Qualified for Final';
            }
        } else {
            $playerStatus = 'You  have not qualified for Playoff';
        }

        $latestPlayoffMatch2 = TeamMatch::where(function ($q) {
            $q->where('teamOneId', $this->teamId)->orWhere('teamTwoId', $this->teamId);
        })
            ->where('matchableType', MatchTypeEnum::PLAYOFF)
            ->where('ladderId', $this->getPurchaseLadder->id)
            ->where('regionId', $this->getSeasonId->regionId)
            ->whereNotNull('wonTeamId')
            ->where('playOffStatus', MatchStatusEnum::COMPLETED)
            ->latest()
            ->first();
        if ($latestPlayoffMatch2) {
            ////////////////For disqualifies//////////////////////
            if ($latestPlayoffMatch2->playOfType == PlayOffTypeEnum::POOL) {
                if ($latestPlayoffMatch2->lossTeamId == $this->teamId) {
                    $playerStatus = 'Disqualified from Round of 16';
                }
            }
            if ($latestPlayoffMatch2->playOfType == PlayOffTypeEnum::QATAR_FINAL) {
                if ($latestPlayoffMatch2->lossTeamId == $this->teamId) {
                    $playerStatus = 'Disqualified from Quarter Final';
                }
            }
            if ($latestPlayoffMatch2->playOfType == PlayOffTypeEnum::SEMI_FINAL) {
                if ($latestPlayoffMatch2->lossTeamId == $this->teamId) {
                    $playerStatus = 'Disqualified from Semi final';
                }
            }
            if ($latestPlayoffMatch2->playOfType == PlayOffTypeEnum::FINAL) {
                if ($latestPlayoffMatch2->lossTeamId == $this->teamId) {
                    $playerStatus = 'You are Runner up';
                }
            }
            if ($latestPlayoffMatch2->playOfType == PlayOffTypeEnum::FINAL) {
                if ($latestPlayoffMatch2->wonTeamId == $this->teamId) {
                    $playerStatus = 'Ladder winner announced';
                }
            }
        }

        $playoffWinner = PlayoffWinner::where('ladderId', $this->getPurchaseLadder->id)->where('teamId', $this->teamId)->first();
        if ($playoffWinner) {
            $playerStatus = "Ladder winner announced";
        }

        return [
            "ladderId" => $this->getPurchaseLadder->id ?: null,
            "ladderName" => $this->getPurchaseLadder->name ?: null,
            "playerStatus" => $playerStatus,
            "ladderStatus" => $ladderStatus
        ];
    }
}
