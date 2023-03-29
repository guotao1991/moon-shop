<?php

namespace App\Repositories\V1;

use App\Models\System\DistrictModel;
use Facade\FlareClient\Http\Exceptions\NotFound;

class DistrictRepository extends BaseRepository
{
    /**
     * 获取省市区列表
     * @return DistrictModel[]
     */
    public function getDistrictList()
    {
        return DistrictModel::withoutTrashed()
            ->with(['cityList',"cityList.districtList"])
            ->where("level", "province")
            ->get();
    }

    /**
     * 获取区域信息
     * @param $id
     * @return DistrictModel
     * @throws NotFound
     */
    public function getInfoById($id): DistrictModel
    {
        $info = DistrictModel::withoutTrashed()
            ->where("id", $id)
            ->first();

        if (empty($info)) {
            throw new NotFound("没有找到区域信息");
        }

        return $info;
    }
}
