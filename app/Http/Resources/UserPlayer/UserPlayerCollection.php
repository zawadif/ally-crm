<?php

namespace App\Http\Resources\UserPlayer;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserPlayerCollection extends ResourceCollection
{
    public $collects = UserPlayerResource::class;
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
            'players' => $this->collection,
            'meta' => $this->pagination
        ];
    }
}
