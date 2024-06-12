<?php

namespace App\Console\Commands;

use App\Enums\LadderPlayoffStatusEnum;
use Carbon\Carbon;
use App\Models\Ladder;
use App\Models\Region;
use App\Models\Season;
use App\Models\PlayOff;
use App\Models\Ranking;
use App\Models\Purchase;
use App\Models\TeamMatch;
use App\Traits\SetWinner;
use App\Enums\MatchTypeEnum;
use App\Traits\RankingTrait;
use App\Models\PlayoffWinner;
use App\Enums\MatchStatusEnum;
use App\Enums\PlayOffTypeEnum;
use App\Models\OverAllRanking;
use Illuminate\Console\Command;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Log;

class SchedulePoolMatch extends Command
{
    use NotificationTrait;
    use RankingTrait;
    use SetWinner;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule-pool:match';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule pool match';

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
        $seasons = Season::where('isEnded', 0)->where('isPlayOffStarted', 0)->get();

        foreach ($seasons as $season) {
            if (env('IS_WEEK_TO_HOUR') == true) {
                $playOfCreateDay = $season->createdAt->addHour($season->noOfWeeks);
            } else {
                $playOfCreateDay = $season->createdAt->addWeeks($season->noOfWeeks);
            }
            if (Carbon::now() > $playOfCreateDay) {
                $ladders = Ladder::where("seasonId", $season->id)->get();
                foreach ($ladders as $ladder) {
                    $playoff = PlayOff::where('seasonId', $season->id)->where('ladderId', $ladder->id)->where('regionId', $season->regionId)->first();
                    if ($playoff) {
                        Season::where('id', $season->id)->update(['isEnded' => 1, 'isPlayOffStarted' => 1]);
                        $purchases = Purchase::where(['seasonId' => $season->id, 'ladderId' => $ladder->id])->count();
                        if ($purchases < 1) {
                            PlayoffWinner::create([
                                'playoffId' => $playoff->id,
                                'ladderId' =>   $ladder->id,
                                'seasonId' =>  $season->id,
                                'teamId' =>  null,
                                'status' => 'COMPLETED'
                            ]);
                            $updateLadder = Ladder::where('id',$ladder->id)->update(['playOffStatus' => LadderPlayoffStatusEnum::WINNER]);
                            $this->PlayOffWinner($season->id);
                        } else {
                            $this->assignOverAllRanking($ladder->categoryId, $ladder->id, $season->id, $season->regionId);
                            $playOffPlayers = OverAllRanking::where('seasonId', $season->id)->where('ladderId', $ladder->id)->orderBy('totalRank', 'asc')->take($playoff->noOfPlayers)->get();
                            $region = Region::find($playoff->regionId);
                            if ($region) {
                                $totalNoOfPlayers = count($playOffPlayers);
                                $declearWinner = false;
                                if ($playoff->noOfPlayers > $totalNoOfPlayers) {
                                    // 2, 4, 8, 16 
                                    if ($totalNoOfPlayers >= 8) {
                                        $totalNoOfPlayers = 8;
                                    } elseif ($totalNoOfPlayers >= 4) {
                                        $totalNoOfPlayers = 4;
                                    } elseif ($totalNoOfPlayers >= 2) {
                                        $totalNoOfPlayers = 2;
                                    } elseif ($totalNoOfPlayers == 1) {
                                        $declearWinner = true;
                                    } elseif ($totalNoOfPlayers == 0) {
                                        PlayoffWinner::create([
                                            'playoffId' => $playoff->id,
                                            'ladderId' =>   $ladder->id,
                                            'seasonId' =>  $season->id,
                                            'teamId' =>  null,
                                            'status' => 'COMPLETED'
                                        ]);
                                        $updateLadder = Ladder::where('id',$ladder->id)->update(['playOffStatus' => LadderPlayoffStatusEnum::WINNER]);
                                        $this->PlayOffWinner($season->id);
                                    }
                                }
                                if ($declearWinner) {
                                    PlayoffWinner::create([
                                        'playoffId' => $playoff->id,
                                        'ladderId' =>   $ladder->id,
                                        'seasonId' =>  $season->id,
                                        'teamId' =>  $playOffPlayers[0]->teamId,
                                        'status' => 'COMPLETED'
                                    ]);                                    
                                    $updateLadder = Ladder::where('id',$ladder->id)->update(['playOffStatus' => LadderPlayoffStatusEnum::WINNER]);
                                    $this->PlayOffWinner($season->id);
                                } else {
                                    if ($totalNoOfPlayers >= 2) {
                                        if ($totalNoOfPlayers == 16) {                                    
                                            $updateLadder = Ladder::where('id',$ladder->id)->update(['playOffStatus' => LadderPlayoffStatusEnum::ROUND_OF_16]);
                                        } elseif ($totalNoOfPlayers == 8) {                                    
                                            $updateLadder = Ladder::where('id',$ladder->id)->update(['playOffStatus' => LadderPlayoffStatusEnum::QUARTER_FINAL]);
                                        } elseif ($totalNoOfPlayers == 4) {                                    
                                            $updateLadder = Ladder::where('id',$ladder->id)->update(['playOffStatus' => LadderPlayoffStatusEnum::SEMI_FINAL]);
                                        } elseif ($totalNoOfPlayers == 2) {                                    
                                            $updateLadder = Ladder::where('id',$ladder->id)->update(['playOffStatus' => LadderPlayoffStatusEnum::FINAL]);
                                        }
                                        $playOffPlayers = OverAllRanking::where('seasonId', $season->id)->where('ladderId', $ladder->id)->orderBy('totalRank', 'asc')->take($totalNoOfPlayers)->get();
                                        for ($i = 0; $i < ($totalNoOfPlayers / 2); $i++) {
                                            $firstTeam = $playOffPlayers[$i]->teamId;
                                            $secondTeam = $playOffPlayers[($totalNoOfPlayers - ($i + 1))]->teamId;
                                            $teamMatchData = array();
                                            $teamMatchData['matchableType'] = MatchTypeEnum::PLAYOFF;
                                            $teamMatchData['teamOneId'] = $firstTeam;
                                            $teamMatchData['teamTwoId'] = $secondTeam;
                                            $teamMatchData['ladderId'] = $playoff->ladderId;
                                            $teamMatchData['categoryId'] = $ladder->categoryId;
                                            $teamMatchData['seasonId'] = $playoff->seasonId;
                                            $teamMatchData['countryId'] = $region->countryId;
                                            $teamMatchData['regionId'] = $playoff->regionId;
                                            $teamMatchData['matchIndex'] = ($i + 1);
                                            $teamMatchData['matchOpponentIndex'] = (($totalNoOfPlayers / 2) - $i);
                                            $teamMatchData['status'] = MatchStatusEnum::ACCEPTED;
                                            if ($totalNoOfPlayers == 16) {
                                                $teamMatchData['playOfType'] = PlayOffTypeEnum::POOL;
                                            } elseif ($totalNoOfPlayers == 8) {
                                                $teamMatchData['playOfType'] = PlayOffTypeEnum::QATAR_FINAL;
                                            } elseif ($totalNoOfPlayers == 4) {
                                                $teamMatchData['playOfType'] = PlayOffTypeEnum::SEMI_FINAL;
                                            } elseif ($totalNoOfPlayers == 2) {
                                                $teamMatchData['playOfType'] = PlayOffTypeEnum::FINAL;
                                            }
                                            $teamMatchData['playOffStatus'] = 'PENDING';
                                            TeamMatch::create($teamMatchData);
                                        }
                                        if ($totalNoOfPlayers == 16) {
                                            $poolMatch = TeamMatch::where([
                                                'matchableType' => MatchTypeEnum::PLAYOFF,
                                                'playOfType' => PlayOffTypeEnum::POOL,
                                                'seasonId' => $playoff->seasonId,
                                                'regionId' => $playoff->regionId
                                            ])->get();
                                            $this->CreatePlayOffsNotification($poolMatch);
                                        }
                                        if ($totalNoOfPlayers == 8) {
                                            $poolMatch = TeamMatch::where([
                                                'matchableType' => MatchTypeEnum::PLAYOFF,
                                                'playOfType' => PlayOffTypeEnum::QATAR_FINAL,
                                                'seasonId' => $playoff->seasonId,
                                                'regionId' => $playoff->regionId
                                            ])->get();
                                            $this->CreatePlayOffsNotification($poolMatch);
                                        }
                                        if ($totalNoOfPlayers == 4) {
                                            $poolMatch = TeamMatch::where([
                                                'matchableType' => MatchTypeEnum::PLAYOFF,
                                                'playOfType' => PlayOffTypeEnum::SEMI_FINAL,
                                                'seasonId' => $playoff->seasonId,
                                                'regionId' => $playoff->regionId
                                            ])->get();
                                            $this->CreatePlayOffsNotification($poolMatch);
                                        }
                                        if ($totalNoOfPlayers == 2) {
                                            $poolMatch = TeamMatch::where([
                                                'matchableType' => MatchTypeEnum::PLAYOFF,
                                                'playOfType' => PlayOffTypeEnum::FINAL,
                                                'seasonId' => $playoff->seasonId,
                                                'regionId' => $playoff->regionId
                                            ])->get();
                                            $this->CreatePlayOffsNotification($poolMatch);
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        Season::where('id', $season->id)->update(['isEnded' => 1]);
                    }
                }
            }
        }
    }
}
