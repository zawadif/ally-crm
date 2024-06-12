<?php

namespace App\Http\Resources\History;

use App\Http\Requests\Api\Match\GetMatchHistory;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GetHistoryCollection extends ResourceCollection
{
    public $collects = GetHistoryResource::class;
    private $pagination;
    private $totalMatches, $wonMatches, $lostMatches;
    public function __construct($resource, $totalMatch, $wonMatches, $lostMatches)
    {
        $this->pagination = [
            'total' => $resource->total(),
            'count' => $resource->count(),
            'perPage' => (int)$resource->perPage(),
            'currentPage' => $resource->currentPage(),
            'totalPages' => $resource->lastPage()
        ];
        $this->totalMatches = $totalMatch;
        $this->wonMatches = $wonMatches;
        $this->lostMatches = $lostMatches;
        $resource = $resource->getCollection();
        parent::__construct($resource);
    }

    public function toArray($request)
    {

        return [
            'totalMatches' => $this->totalMatches,
            "wonMatches" => $this->wonMatches,
            "lostMatches" => $this->lostMatches,
            'matchesHistory' => $this->collection,
            'meta' => $this->pagination
        ];
    }
}
