<?php

namespace App\Http\Resources\OtherPlayerRanking;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class OtherPlayerRankingResource extends JsonResource
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
            "weekNumber" => $this->week->WeekAndRoundNo,
            "ranking" => $this->rank,
            "won" => $this->matchWon,
            "loss" => $this->matchLose,
            "point" => $this->points
        ];
    }
}
