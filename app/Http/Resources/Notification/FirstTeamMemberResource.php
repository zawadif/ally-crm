<?php

namespace App\Http\Resources\Notification;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Match\TeamMember\GetTeamScoreResource;

class FirstTeamMemberResource extends JsonResource
{

    private $matchDetail;
    private $wonTeamId;
    private $match;
    private $user;
    public function __construct($resource, $matchDetail, $wonTeamId, $match, $user)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;

        $this->matchDetail = $matchDetail;
        $this->match = $match;
        $this->wonTeamId = $wonTeamId;
        $this->user = $user;
    }
    public function toArray($request)
    {
        $firstPlayer = User::find($this->firstMemberId);
        $secondPlayer = null;
        if ($this->secondMemberId != null) {
            $secondPlayer = User::find($this->secondMemberId);
        }
        if ($this->wonTeamId == $this->id) {
            $isWinner = true;
            if (!is_null($this->match->winningPoint)) {

                $wp = 'WP(' . $this->match->winningPoint . ')';
            } else {
                $wp = null;
            }
        } else {
            $isWinner = false;
            if (!is_null($this->match->losingPoint)) {
                $lp = 'LP(' . $this->match->losingPoint . ')';
            } else {
                $lp = null;
            }
        }
        $team = [
            'teamId' => $this->id ?: null,
            'firstPlayerName' =>  $firstPlayer ? ($firstPlayer->id == $this->user ? $firstPlayer->fullName . '(You)' : $firstPlayer->fullName) : null,
            'isWinner' => $isWinner,
            'points' =>  $this->wonTeamId == $this->id ? ($wp ?: null) : ($lp ?: null),
            'firstProfileUrl' =>  $firstPlayer ? !is_null($firstPlayer->avatar) ? Storage::disk('s3')->url($firstPlayer->avatar) : ($firstPlayer->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')) : null,
        ];
        if (!is_null($this->getsecondMember)) {
            $team += [
                'secondPlayerName' => $secondPlayer  ? ($secondPlayer->id == $this->user ? $secondPlayer->fullName . '(You)' : $secondPlayer->fullName) : null,
                'secondProfileUrl' => $secondPlayer != null ? !is_null($secondPlayer->avatar) ? Storage::disk('s3')->url($secondPlayer->avatar) : ($secondPlayer->getUserDetail->gender == 'MALE' ? asset('img/avatar/maleAvatar.png') : asset('img/avatar/femaleAvatar.png')) : null,
            ];
        }
        $team += [
            'scores' =>  new GetTeamScoreResource($this->matchDetail)
        ];
        return $team;
    }
}
