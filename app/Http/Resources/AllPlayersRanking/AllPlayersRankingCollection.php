<?php

namespace App\Http\Resources\AllPlayersRanking;

use App\Http\Resources\Week\WeekResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AllPlayersRankingCollection extends ResourceCollection
{
    public $collects = AllPlayersRankingResource::class;
    private $pagination, $weeks,  $currentWeek;
    public function __construct($resource, $weeks = null,  $currentWeek = null)
    {
        $this->pagination = [
            'total' => $resource->total(),
            'count' => $resource->count(),
            'perPage' => (int)$resource->perPage(),
            'currentPage' => $resource->currentPage(),
            'totalPages' => $resource->lastPage()
        ];
        $this->weeks = $weeks;
        $this->currentWeek = $currentWeek;
        $resource = $resource->getCollection();
        parent::__construct($resource);
    }
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (is_null($this->weeks)) {
            return [
                "rankings" => $this->collection,
                'meta' => $this->pagination
            ];
        } else {
            $weeks = array();
            foreach ($this->weeks as $week) {
                if ($week->id == $this->currentWeek) {
                    $isCurrentWeek = true;
                } else {
                    $isCurrentWeek = false;
                }
                $weeks[] = array('weekId' => $week->id, 'weekNumber' => $week->WeekAndRoundNo, 'isCurrentWeek' => $isCurrentWeek);
            }
            return [
                'weeks' => $weeks,
                'rankings' => $this->collection,
                'meta' => $this->pagination
            ];
        }
    }
}
