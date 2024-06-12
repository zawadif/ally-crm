<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\Team;
use App\Models\Week;
use App\Models\Ranking;
use App\Models\Purchase;
use App\Models\TeamMatch;
use App\Models\MatchDetail;
use App\Enums\MatchTypeEnum;
use App\Enums\MatchStatusEnum;
use App\Models\OverAllRanking;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

trait RankingTrait
{
    public function assignRanking($transactionId)
    {
        $rankingObject = array();
        $purchaseTransaction = Purchase::find($transactionId);
        $weeks = Week::where('weekableId', $purchaseTransaction->seasonId)
            ->where('weekableType', "SEASON")
            ->get();
        foreach ($weeks as $week) {
            $totalTeamRankings = Ranking::where('ladderId', $purchaseTransaction->ladderId)
                ->where('seasonId', $purchaseTransaction->seasonId)->where('weekId', $week->id)->max('rank');

            $rankingObject['categoryId'] = $purchaseTransaction->getLadderId->categoryId;
            $rankingObject['seasonId'] = $purchaseTransaction->seasonId;
            $rankingObject['weekId'] = $week->id;
            $rankingObject['ladderId'] = $purchaseTransaction->ladderId;
            $rankingObject['regionId'] = $purchaseTransaction->getSeasonId->regionId;
            $rankingObject['teamId'] = $purchaseTransaction->teamId;
            $rankingObject['points'] = 0;
            $rankingObject['matchWon'] = 0;
            $rankingObject['matchLose'] = 0;
            $rankingObject['type'] = "PROPOSAL";
            $rankingObject['rank'] = $totalTeamRankings + 1;
            Ranking::create($rankingObject);
        }
    }
    public function updateRanking($matchId, $requestStatus)
    {

        $match = TeamMatch::find($matchId);
        $winnerPoints = array();

        $week = Week::where('id', $match->weekId)->where('weekableId', $match->seasonId)
            ->where('weekableType', "SEASON")
            ->first();
        if (!$week) {
            return response()->json(['response' => ['status' => false, 'message' => 'Week not found.']], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        $teamOneRanking = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $match->teamOneId)
            ->where('seasonId', $match->seasonId)
            ->where('weekId', $week->id)
            ->first();

        if ($teamOneRanking) {
            $teamOneRankingId = $teamOneRanking->id;
            $previousWeekData
                = Ranking::where('ladderId', $match->ladderId)
                ->where('teamId', $match->teamOneId)
                ->where('seasonId', $match->seasonId)->where('weekId', '<', $week->id)->where('points', '!=', 0)
                ->orderBy('weekId', 'DESC')
                ->first();
            $totalTeamRankings = Ranking::where('ladderId', $match->ladderId)
                ->where('seasonId', $match->seasonId)->where('weekId', $week->id)->max('rank');
            $rankingObject['categoryId'] = $match->categoryId;
            $rankingObject['seasonId'] = $match->seasonId;
            $rankingObject['weekId'] = $week->id;
            $rankingObject['ladderId'] = $match->ladderId;
            $rankingObject['regionId'] = $match->regionId;
            $rankingObject['teamId'] = $match->teamOneId;

            if (is_null($previousWeekData)) {
                $currentWeekData
                    = Ranking::where('ladderId', $match->ladderId)
                    ->where('teamId', $match->teamOneId)
                    ->where('seasonId', $match->seasonId)->where('weekId', $week->id)
                    ->first();
                $rankingObject['points'] = $currentWeekData->points;
                $rankingObject['matchWon'] =  $currentWeekData->matchWon;
                $rankingObject['matchLose'] = $currentWeekData->matchLose;
            } else {
                $rankingObject['points'] =
                    $previousWeekData ? $previousWeekData->points : 0;
                $rankingObject['matchWon'] =  $previousWeekData ? $previousWeekData->matchWon : 0;
                $rankingObject['matchLose'] = $previousWeekData ? $previousWeekData->matchLose : 0;
            }
            $rankingObject['rank'] = $totalTeamRankings + 1;
            $rankingObject['type'] = $match->matchableType;
            $teamOneRanking->update(['points' => $rankingObject['points'], 'matchWon' => $rankingObject['matchWon'], 'rank' =>  $rankingObject['rank'], 'matchLose' =>  $rankingObject['matchLose']]);
            $teamOneRanking = Ranking::find($teamOneRankingId);
        }

        $teamOneRank = $teamOneRanking->points;

        $teamTwoRanking = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $match->teamTwoId)
            ->where('seasonId', $match->seasonId)
            ->where('weekId', $week->id)
            ->first();

        if ($teamTwoRanking) {
            $teamTwoRankingId = $teamTwoRanking->id;
            $previousWeekData
                = Ranking::where('ladderId', $match->ladderId)
                ->where('teamId', $match->teamTwoId)
                ->where('seasonId', $match->seasonId)->where('weekId', '<', $week->id)->where('points', '!=', 0)
                ->orderBy('weekId', 'DESC')
                ->first();
            $totalTeamRankings = Ranking::where('ladderId', $match->ladderId)
                ->where('seasonId', $match->seasonId)->where('weekId', $week->id)->max('rank');

            $rankingObject['categoryId'] = $match->categoryId;
            $rankingObject['seasonId'] = $match->seasonId;
            $rankingObject['weekId'] = $week->id;
            $rankingObject['ladderId'] = $match->ladderId;
            $rankingObject['regionId'] = $match->regionId;
            $rankingObject['teamId'] = $match->teamTwoId;

            if (is_null($previousWeekData)) {
                $currentWeekData
                    = Ranking::where('ladderId', $match->ladderId)
                    ->where('teamId', $match->teamTwoId)
                    ->where('seasonId', $match->seasonId)->where('weekId', $week->id)
                    ->first();
                $rankingObject['points'] = $currentWeekData->points;
                $rankingObject['matchWon'] =  $currentWeekData->matchWon;
                $rankingObject['matchLose'] = $currentWeekData->matchLose;
            } else {
                $rankingObject['points'] =
                    $previousWeekData ? $previousWeekData->points : 0;
                $rankingObject['matchWon'] =  $previousWeekData ? $previousWeekData->matchWon : 0;
                $rankingObject['matchLose'] = $previousWeekData ? $previousWeekData->matchLose : 0;
            }
            $rankingObject['type'] = $match->matchableType;
            $rankingObject['rank'] = $totalTeamRankings + 1;

            $teamTwoRanking->update(['points' => $rankingObject['points'], 'matchWon' => $rankingObject['matchWon'], 'rank' =>  $rankingObject['rank'], 'matchLose' =>  $rankingObject['matchLose']]);
            $teamTwoRanking = Ranking::find($teamTwoRankingId);
        }

        $teamTwoRank = $teamTwoRanking->points;
        if ($teamOneRank >= $teamTwoRank) {
            $winnerPoints['firstTeamPoints'] = $teamOneRank;
            $winnerPoints['firstTeamId'] = $teamOneRanking->teamId;
            $winnerPoints['firstTeamRank'] = $teamOneRanking->rank;

            $winnerPoints['secondTeamPoints'] = $teamTwoRank;
            $winnerPoints['secondTeamId'] = $teamTwoRanking->teamId;
            $winnerPoints['secondTeamRank'] = $teamTwoRanking->rank;
        } else {
            $winnerPoints['firstTeamPoints'] = $teamTwoRank;
            $winnerPoints['firstTeamId'] = $teamTwoRanking->teamId;
            $winnerPoints['firstTeamRank'] = $teamTwoRanking->rank;

            $winnerPoints['secondTeamPoints'] = $teamOneRank;
            $winnerPoints['secondTeamId'] = $teamOneRanking->teamId;
            $winnerPoints['secondTeamRank'] = $teamOneRanking->rank;
        }

        $lPoints = 0;
        if ($match->wonTeamId !== $match->teamOneId) {
            $lPoints = $match->teamOneId;
        } else {
            $lPoints = $match->teamTwoId;
        }

        $winnerTeam = Team::find($match->wonTeamId);
        $losingTeam = Team::find($lPoints);
        $winnerTeamUpdateRanking = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $winnerTeam->id)
            ->where('weekId', $week->id)
            ->where('seasonId', $match->seasonId)
            ->first();


        $losingTeamUpdateRanking = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $losingTeam->id)->where('weekId', $week->id)
            ->where('seasonId', $match->seasonId)
            ->first();

        ///////////////////// Cases ///////////////////////////


        // ---------------- if tied or consecutively ranked ---------
        if ($winnerTeamUpdateRanking->rank == $losingTeamUpdateRanking->rank) {
            $wp = $lp = 0;

            $setsWin = $lprice = 0;
            $matchDetail = $match->matchDetail;
            if ($match->wonTeamId == $match->teamOneId) {
                $set1 = $matchDetail->teamOneSetOneScore - $matchDetail->teamTwoSetOneScore;
                $set2 = $matchDetail->teamOneSetTwoScore - $matchDetail->teamTwoSetTwoScore;
                $set3 = $matchDetail->teamOneSetThreeScore - $matchDetail->teamTwoSetThreeScore;

                if ($set1 > 0) {
                    $setsWin = $setsWin + $set1;
                    $lprice = $lprice + $matchDetail->teamTwoSetOneScore;
                }
                if ($set2 > 0) {
                    $setsWin = $setsWin + $set2;
                    $lprice = $lprice + $matchDetail->teamTwoSetTwoScore;
                }
                if ($set3 > 0) {
                    $setsWin = $setsWin + $set3;
                    $lprice = $lprice + $matchDetail->teamTwoSetThreeScore;
                }
            } else {
                $set1 = $matchDetail->teamTwoSetOneScore - $matchDetail->teamOneSetOneScore;
                $set2 = $matchDetail->teamTwoSetTwoScore - $matchDetail->teamOneSetTwoScore;
                $set3 = $matchDetail->teamTwoSetThreeScore - $matchDetail->teamOneSetThreeScore;

                if ($set1 > 0) {
                    $setsWin = $setsWin + $set1;
                    $lprice = $lprice + $matchDetail->teamOneSetOneScore;
                }
                if ($set2 > 0) {
                    $setsWin = $setsWin + $set2;
                    $lprice = $lprice + $matchDetail->teamOneSetTwoScore;
                }
                if ($set3 > 0) {
                    $setsWin = $setsWin + $set3;
                    $lprice = $lprice + $matchDetail->teamOneSetThreeScore;
                }
            }

            // 20 + Difference in in games in sets won
            $wp = 20 + $setsWin;

            // Games won in all sets
            $lp = $lprice;
        } elseif ($winnerTeamUpdateRanking->rank < $losingTeamUpdateRanking->rank) { // ---------------- if winner is lower rank ---------
            $wp = $lp = 0;
            $setsWin = $lprice = 0;
            // 15 + (( Difference in games in sets won X Difference in rankings up to 7) / 2 )
            $matchDetail = $match->matchDetail;
            if ($match->wonTeamId == $match->teamOneId) {
                $set1 = $matchDetail->teamOneSetOneScore - $matchDetail->teamTwoSetOneScore;
                $set2 = $matchDetail->teamOneSetTwoScore - $matchDetail->teamTwoSetTwoScore;
                $set3 = $matchDetail->teamOneSetThreeScore - $matchDetail->teamTwoSetThreeScore;

                if ($set1 > 0) {
                    $setsWin = $setsWin + $set1;
                    $lprice = $lprice + $matchDetail->teamTwoSetOneScore;
                }
                if ($set2 > 0) {
                    $setsWin = $setsWin + $set2;
                    $lprice = $lprice + $matchDetail->teamTwoSetTwoScore;
                }
                if ($set3 > 0) {
                    $setsWin = $setsWin + $set3;
                    $lprice = $lprice + $matchDetail->teamTwoSetThreeScore;
                }
            } else {
                $set1 = $matchDetail->teamTwoSetOneScore - $matchDetail->teamOneSetOneScore;
                $set2 = $matchDetail->teamTwoSetTwoScore - $matchDetail->teamOneSetTwoScore;
                $set3 = $matchDetail->teamTwoSetThreeScore - $matchDetail->teamOneSetThreeScore;

                if ($set1 > 0) {
                    $setsWin = $setsWin + $set1;
                    $lprice = $lprice + $matchDetail->teamOneSetOneScore;
                }
                if ($set2 > 0) {
                    $setsWin = $setsWin + $set2;
                    $lprice = $lprice + $matchDetail->teamOneSetTwoScore;
                }
                if ($set3 > 0) {
                    $setsWin = $setsWin + $set3;
                    $lprice = $lprice + $matchDetail->teamOneSetThreeScore;
                }
            }

            $wp = 15 + (($setsWin + ($winnerPoints['firstTeamRank'] - $winnerPoints['secondTeamRank'])) / 2);

            // Games won in all sets
            $lp = $lprice;
        } elseif ($winnerTeamUpdateRanking->rank > $losingTeamUpdateRanking->rank) { // ---------------- if winner is higher rank ---------
            $wp = $lp = 0;

            $setsWin = $lprice = 0;
            $matchDetail = $match->matchDetail;
            if ($match->wonTeamId == $match->teamOneId) {
                $set1 = $matchDetail->teamOneSetOneScore - $matchDetail->teamTwoSetOneScore;
                $set2 = $matchDetail->teamOneSetTwoScore - $matchDetail->teamTwoSetTwoScore;
                $set3 = $matchDetail->teamOneSetThreeScore - $matchDetail->teamTwoSetThreeScore;

                if ($set1 > 0) {
                    $setsWin = $setsWin + $set1;
                    $lprice = $lprice + $matchDetail->teamTwoSetOneScore;
                }
                if ($set2 > 0) {
                    $setsWin = $setsWin + $set2;
                    $lprice = $lprice + $matchDetail->teamTwoSetTwoScore;
                }
                if ($set3 > 0) {
                    $setsWin = $setsWin + $set3;
                    $lprice = $lprice + $matchDetail->teamTwoSetThreeScore;
                }
            } else {
                $set1 = $matchDetail->teamTwoSetOneScore - $matchDetail->teamOneSetOneScore;
                $set2 = $matchDetail->teamTwoSetTwoScore - $matchDetail->teamOneSetTwoScore;
                $set3 = $matchDetail->teamTwoSetThreeScore - $matchDetail->teamOneSetThreeScore;

                if ($set1 > 0) {
                    $setsWin = $setsWin + $set1;
                    $lprice = $lprice + $matchDetail->teamOneSetOneScore;
                }
                if ($set2 > 0) {
                    $setsWin = $setsWin + $set2;
                    $lprice = $lprice + $matchDetail->teamOneSetTwoScore;
                }
                if ($set3 > 0) {
                    $setsWin = $setsWin + $set3;
                    $lprice = $lprice + $matchDetail->teamOneSetThreeScore;
                }
            }


            // 10 + Difference in in games in sets won
            $wp = 10 + $setsWin;

            // Games won in all sets
            $lp = $lprice;
        }
        // ---------------- Challange wil get extra 2 points ---------

        if ($match->matchableType == 'CHALLENGE') {
            $team = Team::find($match->wonTeamId);
            if ($team->firstMemberId == $match->createdBy || $team->secondMemberId == $match->createdBy) {
                $wp += 2;
            } else {
                $lp += 2;
            }
        }
        ///////////////////// Cases ///////////////////////////


        // winnerPoints
        $winnerTeamUpdateRanking = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $winnerTeam->id)->where('weekId', $week->id)
            ->where('seasonId', $match->seasonId)
            ->first();

        $nextFirstTeamWeeks = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $winnerTeam->id)
            ->where('seasonId', $match->seasonId)->where('weekId', '>', $week->id)->where('points', '!=', 0)
            ->get();
        foreach ($nextFirstTeamWeeks as $nextFirstTeamWeek) {
            $updatedPoint = $nextFirstTeamWeek->points + $wp;
            $updatedMatchWonCount = $nextFirstTeamWeek->matchWon + 1;
            $nextFirstTeamWeek->update(['points' => $updatedPoint, 'matchWon' => $updatedMatchWonCount]);
        }
        // loserPoints
        $losingTeamUpdateRanking = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $losingTeam->id)->where('weekId', $week->id)
            ->where('seasonId', $match->seasonId)
            ->first();

        $nextSecondTeamWeeks = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $losingTeam->id)
            ->where('seasonId', $match->seasonId)->where('weekId', '>', $week->id)->where('points', '!=', 0)
            ->get();
        foreach ($nextSecondTeamWeeks as $nextSecondTeamWeek) {
            $updatedPoint = $nextSecondTeamWeek->points + $lp;
            $updatedMatchLoseCount = $nextSecondTeamWeek->matchLose + 1;
            $nextSecondTeamWeek->update(['points' => $updatedPoint, 'matchLose' => $updatedMatchLoseCount]);
        }


        $wonp = $winnerTeamUpdateRanking->points + $wp;

        $matchWon = $winnerTeamUpdateRanking->matchWon + 1;
        $winnerTeamUpdateRanking = $winnerTeamUpdateRanking->update(['points' => $wonp, 'matchWon' => $matchWon]);

        $lossp = $losingTeamUpdateRanking->points + $lp;
        $matchLose = $losingTeamUpdateRanking->matchLose + 1;
        $losingTeamUpdateRanking = $losingTeamUpdateRanking->update(['points' => $lossp, 'matchLose' => $matchLose]);

        $match->winningPoint = $wp;
        $match->losingPoint = $lp;
        $match->save();

        $getAllRankings = Ranking::where('ladderId', $match->ladderId)
            ->where('seasonId', $match->seasonId)
            ->where('weekId', $week->id)
            ->orderBy('points', 'desc')
            ->get();

        for ($i = 0; $i < $getAllRankings->count(); $i++) {
            $k = $i + 1;
            $getAllRankings[$i]->update(['rank' => $k]);
        }

        return true;
    }
    public function cancelStatusRanking($matchId)
    {

        $match = TeamMatch::find($matchId);

        // ---------------- player A created challenge and player B quit ---------
        if (($match->matchableType == MatchTypeEnum::CHALLENGE || $match->matchableType == MatchTypeEnum::PROPOSAL) && $match->status == MatchStatusEnum::CANCEL && $match->createdBy != $match->cancelBy) {
            $winnerTeam = Team::find($match->teamOneId);
            $losingTeam = Team::find($match->teamTwoId);
        }

        // ---------------- player A created challenge and player A quit ---------
        if (($match->matchableType == MatchTypeEnum::CHALLENGE || $match->matchableType == MatchTypeEnum::PROPOSAL) && $match->status == MatchStatusEnum::CANCEL && $match->createdBy == $match->cancelBy) {
            $winnerTeam = Team::find($match->teamTwoId);
            $losingTeam = Team::find($match->teamOneId);
        }

        // winnerPoints
        $winnerTeamUpdateRanking = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $winnerTeam->id)->where('weekId', $match->weekId)
            ->where('seasonId', $match->seasonId)
            ->first();

        if ($winnerTeamUpdateRanking) {
            $teamOneRankingId = $winnerTeamUpdateRanking->id;
            $previousWeekData
                = Ranking::where('ladderId', $match->ladderId)
                ->where('teamId', $winnerTeam->id)
                ->where('seasonId', $match->seasonId)->where('weekId', '<', $match->weekId)->where('points', '!=', 0)
                ->orderBy('weekId', 'DESC')
                ->first();
            $totalTeamRankings = Ranking::where('ladderId', $match->ladderId)
                ->where('seasonId', $match->seasonId)->where('weekId', $match->weekId)->max('rank');
            $rankingObject['categoryId'] = $match->categoryId;
            $rankingObject['seasonId'] = $match->seasonId;
            $rankingObject['weekId'] = $match->weekId;
            $rankingObject['ladderId'] = $match->ladderId;
            $rankingObject['regionId'] = $match->regionId;
            $rankingObject['teamId'] = $winnerTeam->id;
            $rankingObject['points'] = $previousWeekData ? $previousWeekData->points : 0;
            $rankingObject['matchWon'] =  $previousWeekData ? $previousWeekData->matchWon : 0;
            $rankingObject['matchLose'] = $previousWeekData ? $previousWeekData->matchLose : 0;
            $rankingObject['type'] = $match->matchableType;
            $rankingObject['rank'] = $totalTeamRankings + 1;
            $winnerTeamUpdateRanking->update(['points' => $rankingObject['points'], 'matchWon' => $rankingObject['matchWon'], 'rank' =>  $rankingObject['rank'], 'matchLose' =>  $rankingObject['matchLose']]);
            $teamOneRanking = Ranking::find($teamOneRankingId);
        }



        // loserPoints
        $losingTeamUpdateRanking = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $losingTeam->id)->where('weekId', $match->weekId)
            ->where('seasonId', $match->seasonId)
            ->first();



        if ($losingTeamUpdateRanking) {
            $teamTwoRankingId = $losingTeamUpdateRanking->id;
            $previousWeekData
                = Ranking::where('ladderId', $match->ladderId)
                ->where('teamId', $losingTeam->id)
                ->where('seasonId', $match->seasonId)->where('weekId', '<', $match->weekId)->where('points', '!=', 0)
                ->orderBy('weekId', 'DESC')
                ->first();
            $totalTeamRankings = Ranking::where('ladderId', $match->ladderId)
                ->where('seasonId', $match->seasonId)->where('weekId', $match->weekId)->max('rank');

            $rankingObject['categoryId'] = $match->categoryId;
            $rankingObject['seasonId'] = $match->seasonId;
            $rankingObject['weekId'] = $match->weekId;
            $rankingObject['ladderId'] = $match->ladderId;
            $rankingObject['regionId'] = $match->regionId;
            $rankingObject['teamId'] = $losingTeam->id;
            $rankingObject['points'] = $previousWeekData ? $previousWeekData->points : 0;
            $rankingObject['matchWon'] =  $previousWeekData ? $previousWeekData->matchWon : 0;
            $rankingObject['matchLose'] = $previousWeekData ? $previousWeekData->matchLose : 0;
            $rankingObject['type'] = $match->matchableType;
            $rankingObject['rank'] = $totalTeamRankings + 1;
            $losingTeamUpdateRanking->update(['points' => $rankingObject['points'], 'matchWon' => $rankingObject['matchWon'], 'rank' =>  $rankingObject['rank'], 'matchLose' =>  $rankingObject['matchLose']]);
            $teamTwoRanking = Ranking::find($teamTwoRankingId);
        }




        // ---------------- player A created challenge and player B quit ---------


        // if play A Created Challenge, and play B quit
        $wp = 20;
        $lp = 0;
        // winnerPoints
        $winnerTeamUpdateRanking = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $winnerTeam->id)->where('weekId', $match->weekId)
            ->where('seasonId', $match->seasonId)
            ->first();


        $nextFirstTeamWeeks = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $winnerTeam->id)
            ->where('seasonId', $match->seasonId)->where('weekId', '>', $match->weekId)->where('points', '!=', 0)
            ->get();
        foreach ($nextFirstTeamWeeks as $nextFirstTeamWeek) {
            $updatedPoint = $nextFirstTeamWeek->points + $wp;
            $updatedMatchWonCount = $nextFirstTeamWeek->matchWon + 1;
            $nextFirstTeamWeek->update(['points' => $updatedPoint, 'matchWon' => $updatedMatchWonCount]);
        }
        // loserPoints
        $losingTeamUpdateRanking = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $losingTeam->id)->where('weekId', $match->weekId)
            ->where('seasonId', $match->seasonId)
            ->first();
        $nextSecondTeamWeeks = Ranking::where('ladderId', $match->ladderId)
            ->where('teamId', $losingTeam->id)
            ->where('seasonId', $match->seasonId)->where('weekId', '>', $match->weekId)->where('points', '!=', 0)
            ->get();
        foreach ($nextSecondTeamWeeks as $nextSecondTeamWeek) {
            $updatedPoint = $nextSecondTeamWeek->points + $lp;
            $updatedMatchLoseCount = $nextSecondTeamWeek->matchLose + 1;
            $nextSecondTeamWeek->update(['points' => $updatedPoint, 'matchLose' => $updatedMatchLoseCount]);
        }
        $wonp = $winnerTeamUpdateRanking->points + $wp;
        $matchWon = $winnerTeamUpdateRanking->matchWon + 1;
        $winnerTeamUpdateRanking = $winnerTeamUpdateRanking->update(['points' => $wonp, 'matchWon' => $matchWon]);

        $matchLose = $losingTeamUpdateRanking->matchLose + 1;
        $lossp = $losingTeamUpdateRanking->points + $lp;
        $losingTeamUpdateRanking = $losingTeamUpdateRanking->update(['points' => $lossp, 'matchLose' => $matchLose]);


        $match->winningPoint = $wp;
        $match->losingPoint = $lp;
        $match->wonTeamId = $winnerTeam->id;
        $match->lossTeamId = $losingTeam->id;
        $match->save();
        $getAllRankings = Ranking::where('ladderId', $match->ladderId)
            ->where('seasonId', $match->seasonId)
            ->where('weekId', $match->weekId)
            ->orderBy('points', 'desc')
            ->get();

        for ($i = 0; $i < $getAllRankings->count(); $i++) {
            $k = $i + 1;
            $getAllRankings[$i]->update(['rank' => $k]);
        }
        return true;
    }

    public function assignOverAllRanking($categoryId, $ladderId, $seasonId, $regionId)
    {
        $overAllRank = array();
        $overAllRank['seasonId'] = $seasonId;
        $overAllRank['ladderId'] = $ladderId;
        $overAllRank['categoryId'] = $categoryId;
        $overAllRank['regionId'] = $regionId;
        $teams = Team::where('ladderId', $ladderId)->get();
        foreach ($teams as $team) {
            $totalMatchWon = Ranking::where('seasonId', $seasonId)->where('ladderId', $ladderId)->where('teamId', $team->id)->sum('matchWon');
            $totalMatchLose = Ranking::where('seasonId', $seasonId)->where('ladderId', $ladderId)->where('teamId', $team->id)->sum('matchLose');
            $totalPoints = Ranking::where('seasonId', $seasonId)->where('ladderId', $ladderId)->where('teamId', $team->id)->sum('points');
            $overAllRank['teamId'] = $team->id;
            $overAllRank['totalMatchWon'] = $totalMatchWon;
            $overAllRank['totalMatchLose'] = $totalMatchLose;
            $overAllRank['totalPoints'] = $totalPoints;
            $rankingCreate = OverAllRanking::create($overAllRank);
        }

        $getAllRankings = OverAllRanking::where('ladderId', $ladderId)
            ->where('seasonId', $seasonId)
            ->orderBy('totalPoints', 'desc')
            ->get();

        for ($i = 0; $i < $getAllRankings->count(); $i++) {
            $k = $i + 1;
            $getAllRankings[$i]->update(['totalRank' => $k]);
        }
    }
    public function removeRanking($matchId, $reqStatus)
    {
        $teamMatch = TeamMatch::find($matchId);

        $matchDetail = MatchDetail::where('matchId', $matchId)->first();
        if ($teamMatch->matchableType != MatchTypeEnum::PLAYOFF) {
            if ($reqStatus == 'SCORE_UPDATE') {
                $winnerTeamUpdateRanking = Ranking::where('ladderId', $teamMatch->ladderId)
                    ->where('teamId', $teamMatch->wonTeamId)->where('weekId', $teamMatch->weekId)
                    ->where('seasonId', $teamMatch->seasonId)
                    ->first();

                $nextTeamWeeks = Ranking::where('ladderId', $teamMatch->ladderId)
                    ->where('teamId', $teamMatch->wonTeamId)
                    ->where('seasonId', $teamMatch->seasonId)->where('weekId', '>', $teamMatch->weekId)->where('points', '!=', 0)
                    ->get();
                $losingTeamUpdateRanking = Ranking::where('ladderId', $teamMatch->ladderId)
                    ->where('teamId', $teamMatch->lossTeamId)->where('weekId', $teamMatch->weekId)
                    ->where('seasonId', $teamMatch->seasonId)
                    ->first();
                $nextLossingTeamWeeks = Ranking::where('ladderId', $teamMatch->ladderId)
                    ->where('teamId', $teamMatch->lossTeamId)
                    ->where('seasonId', $teamMatch->seasonId)->where('weekId', '>', $teamMatch->weekId)->where('points', '!=', 0)
                    ->get();
                if (!is_null($teamMatch->winningPoint) && !is_null($teamMatch->losingPoint)) {

                    if ($winnerTeamUpdateRanking) {
                        $wonp = $winnerTeamUpdateRanking->points - $teamMatch->winningPoint;
                        $winnerPoints = $winnerTeamUpdateRanking->update(['points' => $wonp]);
                        foreach ($nextTeamWeeks as $nextTeamWeek) {
                            $wonp = $nextTeamWeek->points - $teamMatch->winningPoint;
                            $winnerPoints = $nextTeamWeek->update(['points' => $wonp]);
                        }
                    }
                    if ($losingTeamUpdateRanking) {
                        $lossp = $losingTeamUpdateRanking->points - $teamMatch->losingPoint;
                        $losingPoints = $losingTeamUpdateRanking->update(['points' =>  $lossp]);
                        foreach ($nextLossingTeamWeeks as $nextLossingTeamWeek) {
                            $lossp = $nextLossingTeamWeek->points - $teamMatch->losingPoint;
                            $losingPoints = $nextLossingTeamWeek->update(['points' => $lossp]);
                        }
                    }
                }
                if (!is_null($teamMatch->wonTeamId) && !is_null($teamMatch->lossTeamId)) {
                    if ($winnerTeamUpdateRanking) {

                        $matchWon = $winnerTeamUpdateRanking->matchWon - 1;
                        $totalWon = $winnerTeamUpdateRanking->update(['matchWon' => $matchWon]);
                        foreach ($nextTeamWeeks as $nextTeamWeek) {
                            $matchWon = $nextTeamWeek->matchWon - 1;
                            $totalWon = $nextTeamWeek->update(['matchWon' => $matchWon]);
                        }
                    }
                    if ($losingTeamUpdateRanking) {

                        $matchLose = $losingTeamUpdateRanking->matchLose - 1;
                        $totalLose = $losingTeamUpdateRanking->update(['matchLose' => $matchLose]);
                        foreach ($nextLossingTeamWeeks as $nextLossingTeamWeek) {
                            $matchLose = $nextLossingTeamWeek->matchLose - 1;
                            $totalLose = $nextLossingTeamWeek->update(['matchLose' => $matchLose]);
                        }
                    }
                }
            }
        }
    }
}
