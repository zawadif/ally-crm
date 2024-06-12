<?php

namespace App\Http\Resources\Match\Collections;

use App\Http\Resources\Match\GetOtherPlayerResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GetOtherPlayerCollection extends ResourceCollection
{

    public $collects = GetOtherPlayerResource::class;
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
            'matches' => $this->collection,
            'meta' => $this->pagination
        ];
    }
}