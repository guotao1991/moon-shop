<?php

namespace App\Http\Resources\V1\Common;

use App\Http\Resources\V1\BaseResources;
use App\Models\Common\TagModel;
use App\Models\System\DistrictModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class DistrictResource
 * @package App\Http\Resources\V1\Common
 *
 * @property int $id
 * @property string $name
 * @property string $level
 *
 * @property DistrictModel[] $cityList
 * @property DistrictModel[] $districtList
 */
class DistrictResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $data = [
            "id" => $this->id,
            "name" => $this->name
        ];

        if ($this->level == "province") {
            $data["city_list"] = DistrictResource::collection($this->cityList);
        }

        if ($this->level == "city") {
            $data['district_list'] = DistrictResource::collection($this->districtList);
        }

        return $data;
    }
}
