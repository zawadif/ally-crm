<?php

namespace App\Traits;

use App\Enums\MatchStatusEnum;
use App\Models\Ladder;
use App\Models\Season;
use App\Models\PlayOff;
use App\Models\Ranking;
use App\Enums\MatchTypeEnum;
use App\Models\PlayoffWinner;


trait SetWinner
{
    public function SetWonAndLossTeam($matchDetail, $teamMatch)
    {
        $teamOne = 0;
        $teamTwo = 0;
        if ($matchDetail->teamOneSetOneScore) {
            if ($matchDetail->teamOneSetOneScore != $matchDetail->teamTwoSetOneScore) {
                if ($matchDetail->teamOneSetOneScore > $matchDetail->teamTwoSetOneScore) {
                    $teamOne++;
                } else {
                    $teamTwo++;
                }
            } else {
                $teamOne++;
                $teamTwo++;
            }
        }
        if ($matchDetail->teamOneSetTwoScore) {

            if ($matchDetail->teamOneSetTwoScore != $matchDetail->teamTwoSetTwoScore) {
                if ($matchDetail->teamOneSetTwoScore > $matchDetail->teamTwoSetTwoScore) {
                    $teamOne++;
                } else {
                    $teamTwo++;
                }
            } else {
                $teamOne++;
                $teamTwo++;
            }
        }

        if ($matchDetail->teamOneSetThreeScore) {
            if ($matchDetail->teamOneSetThreeScore != $matchDetail->teamTwoSetThreeScore) {
                if ($matchDetail->teamOneSetThreeScore > $matchDetail->teamTwoSetThreeScore) {
                    $teamOne++;
                } else {
                    $teamTwo++;
                }
            } else {
                $teamOne++;
                $teamTwo++;
            }
        }
        if ($teamOne > $teamTwo) {
            $teamMatch->wonTeamId = $teamMatch->teamOneId;
            $teamMatch->lossTeamId = $teamMatch->teamTwoId;
        } else {
            $teamMatch->wonTeamId = $teamMatch->teamTwoId;
            $teamMatch->lossTeamId = $teamMatch->teamOneId;
        }
        $teamMatch->save();
        return true;
    }

    public function PlayOffWinner($seasonId)
    {
        $count = 0;
        $seasonLadders = Ladder::where('seasonId', $seasonId)->get();
        $ladderCount = count($seasonLadders);
        foreach ($seasonLadders as $ladder) {
            $playOffWinners = PlayoffWinner::where('seasonId', $seasonId)->where('ladderId', $ladder->id)->first();
            if ($playOffWinners) {
                $count = $count + 1;
            }
        }
        if ($ladderCount == $count) {
            $season = Season::find($seasonId);
            $season->isPlayOffStarted = 0;
            $season->save();
        }
    }
}
