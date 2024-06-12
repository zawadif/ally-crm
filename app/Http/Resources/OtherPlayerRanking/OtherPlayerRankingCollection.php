<?php

namespace App\Http\Resources\OtherPlayerRanking;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OtherPlayerRankingCollection extends ResourceCollection
{
    public $collects = OtherPlayerRankingResource::class;
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
            'rankings' => $this->collection,
            'meta' => $this->pagination
        ];
    }
}
