<?php

namespace App\Http\Resources\Match\ScheduledMatch;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GetScheduledMatchCollection extends ResourceCollection
{

    public $collects = GetScheduledMatchResource::class;
    private $pagination;
    public function __construct($resource)
    {
        $this->pagination = [
            'total' => $resource->total(),
            'count' => $resource->count(),
            'perPage' => (int)$resource->perPage(),
            'currentPage' => $resource->currentPage(),
            'totalPages' => $resource->lastPage()
        ];
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

        return [
            'scheduledMatches' => $this->collection,
            'meta' => $this->pagination
        ];
    }
}