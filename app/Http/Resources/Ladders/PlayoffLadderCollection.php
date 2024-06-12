<?php

namespace App\Http\Resources\Ladders;

use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Ladders\LadderResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;

class PlayoffLadderCollection extends ResourceCollection
{
    public $collects = PlayoffLadderResource::class;
    public function __construct($resource)
    {
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
        return $this->collection;
    }
}
