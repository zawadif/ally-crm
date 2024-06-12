<?php

namespace App\Http\Resources\Category;

use App\Http\Resources\Category\CategoryResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Storage;

class CategoryCollection extends ResourceCollection
{
    public $collects = CategoryResource::class;
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
