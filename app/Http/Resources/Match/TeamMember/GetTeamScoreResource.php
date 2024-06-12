<?php

namespace App\Http\Resources\Match\TeamMember;

use Illuminate\Http\Resources\Json\JsonResource;

class GetTeamScoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            array(
                'roundNumber' => 1,
                'roundScore' => $this->teamOneSetOneScore
            ),

            array(
                'roundNumber' => 2,
                'roundScore' => $this->teamOneSetTwoScore
            ),

            array(
                'roundNumber' => 3,
                'roundScore' => $this->teamOneSetThreeScore
            ),

        ];
    }
}