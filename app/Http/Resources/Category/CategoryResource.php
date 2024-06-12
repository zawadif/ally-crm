<?php

namespace App\Http\Resources\Category;

use App\Models\Ladder;
use App\Models\Season;
use App\Models\Country;
use App\Enums\GenderEnum;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\Ladders\LadderCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $ladder = null;
        if ($request->path() == "api/v1/categories-ladders") {
            $user = UserDetail::where('userId', Auth()->id())->first();
            $season = Season::where('regionId', $user->regionId)->where('isEnded', 0)->where('isPlayOffStarted', 0)->first();
            if ($user->gender == GenderEnum::MALE) {
                $gender = 'Men';
            } else {
                $gender = 'Women';
            }
            if ($season) {
                $ladder = Ladder::with('season')->where([
                    'categoryId' => $this->id,
                ])->whereIn('seasonGenderType', [$gender, 'Mixed Double'])->orderBy('name', 'asc')
                    ->whereHas('season', function ($q) use ($user) {
                        $q->where('regionId', $user->regionId)->where('isEnded', 0)->where('isPlayOffStarted', 0);
                    })->get();
            } else {
                $ladder = Ladder::with('season')->where([
                    'categoryId' => $this->id,
                ])->whereIn('seasonGenderType', [$gender, 'Mixed Double'])->orderBy('name', 'asc')
                    ->whereHas('season', function ($q) use ($user) {
                        $q->where('regionId', $user->regionId)->where(['isEnded' => 1, 'isPlayOffStarted' => 1]);
                    })
                    ->get();
            }
        }

        $ladderImage = null;
        if ($this->id == 1) {
            $ladderImage = asset('img/ladders/maleSingle.png');
        } else if ($this->id == 2) {
            $ladderImage = asset('img/ladders/maleDouble.png');
        } else {
            $ladderImage = asset('img/ladders/mixDouble.png');
        }
        return [
            "categoryId" => $this->id ?: null,
            "categoryName" => $this->name ?: null,
            "categoryImage" => !is_null($this->imageUrl) ? $ladderImage : $ladderImage,
            "ladders" => $ladder ? new LadderCollection($ladder) :
                new LadderCollection($this->ladders),
        ];
    }
}
