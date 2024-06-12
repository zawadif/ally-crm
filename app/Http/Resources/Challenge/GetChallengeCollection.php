<?php

namespace App\Http\Resources\Challenge;

use App\Http\Resources\Challenge\GetChallengeResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GetChallengeCollection extends ResourceCollection
{

    public $collects = GetChallengeResource::class;
    private $pagination, $weeks, $currentWeek;
    public function __construct($resource, $weeks = null, $currentWeek = null)
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
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if (is_null($this->weeks)) {
            return [
                'challenges' => $this->collection,
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
                'weeks' =>  $weeks,
                'challenges' => $this->collection,
                'meta' => $this->pagination
            ];
        }
    }
}
