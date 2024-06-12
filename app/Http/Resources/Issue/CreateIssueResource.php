<?php

namespace App\Http\Resources\Issue;

use Illuminate\Http\Resources\Json\JsonResource;

class CreateIssueResource extends JsonResource
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
            'inbox' => null
        ];
    }
}