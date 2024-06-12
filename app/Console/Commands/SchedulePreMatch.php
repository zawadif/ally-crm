<?php

namespace App\Console\Commands;

use App\Enums\LadderPlayoffStatusEnum;
use Carbon\Carbon;
use App\Models\Ladder;
use App\Models\Region;
use App\Models\Season;
use App\Models\PlayOff;
use App\Models\Ranking;
use App\Models\TeamMatch;
use App\Traits\SetWinner;
use App\Enums\MatchTypeEnum;
use App\Models\PlayoffWinner;
use App\Enums\MatchStatusEnum;
use App\Enums\PlayOffTypeEnum;
use Illuminate\Console\Command;
use App\Traits\NotificationTrait;

class SchedulePreMatch extends Command
{

    use NotificationTrait;
    use SetWinner;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule-pre:match';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule Pre match';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $teamMatchesIndex = array(
            8 => 1,
            7 => 2,
            6 => 3,
            5 => 4,
            4 => 5,
            3 => 6,
            2 => 7,
            1 => 8,
        );
        $teamOpponentMatchesIndex = array(
            8 => 4,
            7 => 3,
            6 => 2,
            5 => 1,
            4 => 1,
            3 => 2,
            2 => 3,
            1 => 4,
        );
        $teamMatchesQatarIndex = array(
            4 => 1,
            3 => 2,
            2 => 3,
            1 => 4,

        );
        $teamOpponentMatchesQatarIndex = array(
            4 => 2,
            3 => 1,
            2 => 1,
            1 => 2,
        );
        $teamMatchesSemiIndex = array(
            2 => 1,
            1 => 2,

        );
        $teamOpponentMatchesSemiIndex = array(
            2 => 1,
            1 => 1,
        );


        //pool match
        $poolMatches = TeamMatch::where('playOfType', PlayOffTypeEnum::POOL)->where('matchableType', MatchTypeEnum::PLAYOFF)->whereNull('wonTeamId')->get();
        foreach ($poolMatches as $poolMatch) {
            $poolCreated = TeamMatch::select('createdAt')->where('playOfType', PlayOffTypeEnum::POOL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('ladderId', $poolMatch->ladderId)->first();
            if ($poolCreated) {
                $playOfCreateDay = $poolCreated->createdAt->addDays(3);
                if (Carbon::now() > $playOfCreateDay) {
                    if (strtoupper($playOfCreateDay->isoFormat('d')) == 5) {
                        if ($poolMatch->wonTeamId == null) {
                            $poolMatch->wonTeamId = $poolMatch->teamOneId;
                            $poolMatch->lossTeamId = $poolMatch->teamTwoId;
                            $poolMatch->status = MatchStatusEnum::COMPLETED;
                            $poolMatch->save();                            
                            $updateLadder = Ladder::where('id',$poolMatch->ladderId)->update(['playOffStatus' => LadderPlayoffStatusEnum::QUARTER_FINAL]);
                            $this->KnockOutNotification($poolMatch, 'Congradulation! you are qualify for Qatar Final');
                        }
                    }
                }
            }
        }

        //qatar Match
        $qatarMatches = TeamMatch::where('playOfType', PlayOffTypeEnum::QATAR_FINAL)->where('matchableType', MatchTypeEnum::PLAYOFF)->whereNull('wonTeamId')->get();
        foreach ($qatarMatches as $qatarMatch) {
            $poolCreated = TeamMatch::select('createdAt')->where('playOfType', PlayOffTypeEnum::POOL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('ladderId', $qatarMatch->ladderId)->first();
            if ($poolCreated) {
                $playOfCreateDay = $poolCreated->createdAt->addDays(4);
                if (Carbon::now() > $playOfCreateDay) {
                    if (strtoupper($playOfCreateDay->isoFormat('d')) == 6) {
                        if ($qatarMatch->wonTeamId == null) {
                            $qatarMatch->wonTeamId = $qatarMatch->teamOneId;
                            $qatarMatch->lossTeamId = $qatarMatch->teamTwoId;
                            $qatarMatch->status = MatchStatusEnum::COMPLETED;
                            $qatarMatch->save();                               
                            $updateLadder = Ladder::where('id',$qatarMatch->ladderId)->update(['playOffStatus' => LadderPlayoffStatusEnum::SEMI_FINAL]);
                            $this->KnockOutNotification($qatarMatch, 'Congradulation! you are qualify for Semi Final');
                        }
                    }
                }
            }
        }
        //semi match

        $semiMatches = TeamMatch::where('playOfType', PlayOffTypeEnum::SEMI_FINAL)->where('matchableType', MatchTypeEnum::PLAYOFF)->whereNull('wonTeamId')->get();
        foreach ($semiMatches as $semiMatch) {
            $poolCreated = TeamMatch::select('createdAt')->where('playOfType', PlayOffTypeEnum::POOL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('ladderId', $semiMatch->ladderId)->first();
            if ($poolCreated) {
                $playOfCreateDay = $poolCreated->createdAt->addDays(5);
                if (Carbon::now() > $playOfCreateDay) {
                    if (strtoupper($playOfCreateDay->isoFormat('d')) > 6) {
                        if ($semiMatch->wonTeamId == null) {
                            $semiMatch->wonTeamId = $semiMatch->teamOneId;
                            $semiMatch->lossTeamId = $semiMatch->teamTwoId;
                            $semiMatch->status = MatchStatusEnum::COMPLETED;
                            $semiMatch->save();                                    
                            $updateLadder = Ladder::where('id',$semiMatch->ladderId)->update(['playOffStatus' => LadderPlayoffStatusEnum::FINAL]);
                            $this->KnockOutNotification($semiMatch, 'Congradulation! you are qualify for Final');
                        }
                    }
                }
            }
        }
        //final match
        $finalMatches = TeamMatch::where('playOfType', PlayOffTypeEnum::FINAL)->where('matchableType', MatchTypeEnum::PLAYOFF)->whereNull('wonTeamId')->get();
        foreach ($finalMatches as $finalMatch) {
            $poolCreated = TeamMatch::select('createdAt')->where('playOfType', PlayOffTypeEnum::POOL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('ladderId', $finalMatch->ladderId)->first();
            if ($poolCreated) {
                $playOfCreateDay = $poolCreated->createdAt->addDays(6);
                if (Carbon::now() > $playOfCreateDay) {
                    if ($playOfCreateDay->format('H:i:m') == "23:59:00") {
                        if ($finalMatch->wonTeamId == null) {
                            $finalMatch->wonTeamId = $finalMatch->teamOneId;
                            $finalMatch->lossTeamId = $finalMatch->teamTwoId;
                            $finalMatch->status = MatchStatusEnum::COMPLETED;
                            $finalMatch->save();

                            $playoff = PlayOff::where('ladderId', $finalMatch->ladderId)->where('seasonId', $finalMatch->seasonId)->first();
                            PlayoffWinner::create([
                                'playoffId' => $playoff->id,
                                'ladderId' =>   $finalMatch->ladderId,
                                'seasonId' =>  $finalMatch->seasonId,
                                'teamId' =>  $finalMatch->teamOneId,
                                'status' => 'COMPLETED'
                            ]);                               
                            $updateLadder = Ladder::where('id',$finalMatch->ladderId)->update(['playOffStatus' => LadderPlayoffStatusEnum::WINNER]);
                            $this->PlayOffWinner($finalMatch->seasonId);
                        }
                    }
                }
            }
        }

        //Qatar match

        $wonPoolMatches = TeamMatch::where('playOfType', PlayOffTypeEnum::POOL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('status', MatchStatusEnum::COMPLETED)->whereNotNull('wonTeamId')->where('playOffStatus', 'PENDING')->get();
        foreach ($wonPoolMatches as $wonPoolMatche) {
            $findQatarTeamTwo = TeamMatch::where('playOfType', PlayOffTypeEnum::POOL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('matchIndex', $wonPoolMatche->matchOpponentIndex)->where('matchOpponentIndex', $wonPoolMatche->matchIndex)->where('ladderId', $wonPoolMatche->ladderId)->where('regionId', $wonPoolMatche->regionId)->whereNotNull('wonTeamId')->where('status', MatchStatusEnum::COMPLETED)->first();
            if ($wonPoolMatche && $findQatarTeamTwo) {
                $qatarFinalExistTeam = TeamMatch::where('playOfType', PlayOffTypeEnum::QATAR_FINAL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('ladderId', $wonPoolMatche->ladderId)->where('regionId', $wonPoolMatche->regionId)->where('teamTwoId', $findQatarTeamTwo->wonTeamId)->where('teamOneId',  $wonPoolMatche->wonTeamId)->first();
                $qatarFinalExistTeamTwo = TeamMatch::where('playOfType', PlayOffTypeEnum::QATAR_FINAL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('ladderId', $wonPoolMatche->ladderId)->where('regionId', $wonPoolMatche->regionId)->where('teamTwoId', $wonPoolMatche->wonTeamId)->where('teamOneId',  $findQatarTeamTwo->wonTeamId)->first();
                if (!$qatarFinalExistTeam && !$qatarFinalExistTeamTwo) {
                    $teamMatchData['teamTwoId'] = $findQatarTeamTwo->teamTwoId;
                    $teamMatchData['matchIndex'] = $teamMatchesIndex[$wonPoolMatche->matchOpponentIndex];
                    $teamMatchData['matchOpponentIndex'] = $teamOpponentMatchesIndex[$wonPoolMatche->matchOpponentIndex];
                    $teamMatchData['matchableType'] = MatchTypeEnum::PLAYOFF;
                    $teamMatchData['teamOneId'] = $wonPoolMatche->wonTeamId;
                    $teamMatchData['ladderId'] = $wonPoolMatche->ladderId;
                    $teamMatchData['categoryId'] = $wonPoolMatche->categoryId;
                    $teamMatchData['seasonId'] = $wonPoolMatche->seasonId;
                    $teamMatchData['countryId'] = $wonPoolMatche->countryId;
                    $teamMatchData['regionId'] = $wonPoolMatche->regionId;
                    $teamMatchData['status'] = MatchStatusEnum::ACCEPTED;
                    $teamMatchData['playOfType'] = PlayOffTypeEnum::QATAR_FINAL;
                    $teamMatchData['playOffStatus'] = 'PENDING';
                    $match = TeamMatch::create($teamMatchData);

                    $this->CreateKnockOutNotification($match, 'Congradulation! you are qualify for Qatar Final');                                    
                    $updateLadder = Ladder::where('id',$wonPoolMatche->ladderId)->update(['playOffStatus' => LadderPlayoffStatusEnum::QUARTER_FINAL]);

                    $wonPoolMatche->playOffStatus = 'COMPLETED';
                    $wonPoolMatche->save();
                    $findQatarTeamTwo->playOffStatus = 'COMPLETED';
                    $findQatarTeamTwo->save();
                }
            }
        }

        //semi Match

        $wonQatarMatches = TeamMatch::where('playOfType', PlayOffTypeEnum::QATAR_FINAL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('status', MatchStatusEnum::COMPLETED)->whereNotNull('wonTeamId')->where('playOffStatus', 'PENDING')->get();
        $teamMatchData = array();
        foreach ($wonQatarMatches as $wonQatarMatche) {
            $findSemiTeamTwo = TeamMatch::where('playOfType', PlayOffTypeEnum::QATAR_FINAL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('matchIndex', $wonQatarMatche->matchOpponentIndex)->where('matchOpponentIndex', $wonQatarMatche->matchIndex)->where('ladderId', $wonQatarMatche->ladderId)->where('regionId', $wonQatarMatche->regionId)->whereNotNull('wonTeamId')->where('status', MatchStatusEnum::COMPLETED)->first();
            if ($wonQatarMatche && $findSemiTeamTwo) {
                $semiFinalExistTeam = TeamMatch::where('playOfType', PlayOffTypeEnum::SEMI_FINAL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('ladderId', $wonQatarMatche->ladderId)->where('regionId', $wonQatarMatche->regionId)->where('teamTwoId', $findSemiTeamTwo->wonTeamId)->where('teamOneId',  $wonQatarMatche->wonTeamId)->first();

                $semiFinalExistTeamTwo = TeamMatch::where('playOfType', PlayOffTypeEnum::SEMI_FINAL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('ladderId', $wonQatarMatche->ladderId)->where('regionId', $wonQatarMatche->regionId)->where('teamTwoId', $wonQatarMatche->wonTeamId)->where('teamOneId', $findSemiTeamTwo->wonTeamId)->first();
                if (!$semiFinalExistTeam && !$semiFinalExistTeamTwo) {
                    $teamMatchData['teamTwoId'] = $findSemiTeamTwo->wonTeamId;
                    $teamMatchData['matchIndex'] = $teamMatchesQatarIndex[$wonQatarMatche->matchOpponentIndex];
                    $teamMatchData['matchOpponentIndex'] = $teamOpponentMatchesQatarIndex[$wonQatarMatche->matchOpponentIndex];
                    $teamMatchData['matchableType'] = MatchTypeEnum::PLAYOFF;
                    $teamMatchData['teamOneId'] = $wonQatarMatche->wonTeamId;
                    $teamMatchData['ladderId'] = $wonQatarMatche->ladderId;
                    $teamMatchData['categoryId'] = $wonQatarMatche->categoryId;
                    $teamMatchData['seasonId'] = $wonQatarMatche->seasonId;
                    $teamMatchData['countryId'] = $wonQatarMatche->countryId;
                    $teamMatchData['regionId'] = $wonQatarMatche->regionId;
                    $teamMatchData['status'] = MatchStatusEnum::ACCEPTED;
                    $teamMatchData['playOfType'] = PlayOffTypeEnum::SEMI_FINAL;
                    $teamMatchData['playOffStatus'] = 'PENDING';
                    $match = TeamMatch::create($teamMatchData);

                    $this->CreateKnockOutNotification($match, 'Congradulation! you are qualify for Semi Final');                                    
                    $updateLadder = Ladder::where('id',$wonQatarMatche->ladderId)->update(['playOffStatus' => LadderPlayoffStatusEnum::SEMI_FINAL]);

                    $wonQatarMatche->playOffStatus = 'COMPLETED';
                    $wonQatarMatche->save();
                    $findSemiTeamTwo->playOffStatus = 'COMPLETED';
                    $findSemiTeamTwo->save();
                }
            }
        }

        //final match

        $wonSemiMatches = TeamMatch::where('playOfType', PlayOffTypeEnum::SEMI_FINAL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('status', MatchStatusEnum::COMPLETED)->whereNotNull('wonTeamId')->where('playOffStatus', 'PENDING')->get();
        $teamMatchData = array();
        foreach ($wonSemiMatches as $wonSemiMatche) {
            $findSemiTeamTwo = TeamMatch::where('playOfType', PlayOffTypeEnum::SEMI_FINAL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('matchIndex', $wonSemiMatche->matchOpponentIndex)->where('matchOpponentIndex', $wonSemiMatche->matchIndex)->where('ladderId', $wonSemiMatche->ladderId)->where('regionId', $wonSemiMatche->regionId)->whereNotNull('wonTeamId')->where('status', MatchStatusEnum::COMPLETED)->first();

            if ($wonSemiMatche && $findSemiTeamTwo) {
                $finalExistTeam = TeamMatch::where('playOfType', PlayOffTypeEnum::FINAL)->where('matchableType', MatchTypeEnum::PLAYOFF)->where('ladderId', $wonSemiMatche->ladderId)->where('regionId', $wonSemiMatche->regionId)->first();
                if (!$finalExistTeam) {
                    $teamMatchData['teamTwoId'] = $findSemiTeamTwo->wonTeamId;
                    $teamMatchData['matchIndex'] = $teamMatchesSemiIndex[$wonSemiMatche->matchOpponentIndex];
                    $teamMatchData['matchOpponentIndex'] = $teamOpponentMatchesSemiIndex[$wonSemiMatche->matchOpponentIndex];
                    $teamMatchData['matchableType'] = MatchTypeEnum::PLAYOFF;
                    $teamMatchData['teamOneId'] = $wonSemiMatche->wonTeamId;
                    $teamMatchData['ladderId'] = $wonSemiMatche->ladderId;
                    $teamMatchData['categoryId'] = $wonSemiMatche->categoryId;
                    $teamMatchData['seasonId'] = $wonSemiMatche->seasonId;
                    $teamMatchData['countryId'] = $wonSemiMatche->countryId;
                    $teamMatchData['regionId'] = $wonSemiMatche->regionId;
                    $teamMatchData['status'] = MatchStatusEnum::ACCEPTED;
                    $teamMatchData['playOfType'] = PlayOffTypeEnum::FINAL;
                    $teamMatchData['playOffStatus'] = 'PENDING';
                    $match = TeamMatch::create($teamMatchData);
                    $this->CreateKnockOutNotification($match, 'Congradulation! you are qualify for Final');                                    
                    $updateLadder = Ladder::where('id',$wonSemiMatche->ladderId)->update(['playOffStatus' => LadderPlayoffStatusEnum::FINAL]);

                    $wonSemiMatche->playOffStatus = 'COMPLETED';
                    $wonSemiMatche->save();
                    $findSemiTeamTwo->playOffStatus = 'COMPLETED';
                    $findSemiTeamTwo->save();
                }
            }
        }
    }
}
