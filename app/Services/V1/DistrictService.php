<?php

namespace App\Services\V1;

use App\Repositories\V1\DistrictRepository;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;

class DistrictService extends BaseService
{
    protected $districtRepo;

    public function __construct(DistrictRepository $districtRepository)
    {
        $this->districtRepo = $districtRepository;
    }

    /**
     * 获取省市区列表
     * @return array
     * @throws Exception
     */
    public function getDistrictList()
    {
        return $this->districtRepo->getDistrictList();
    }

    /**
     * 根据省市区ID获取详情信息
     *
     * @param array $data
     * @return string
     */
    public function getPcdInfo(array $data): string
    {
        $address = "";
        if (empty($data['province_id'])) {
            return $address;
        }
        try {
            $province = $this->districtRepo->getInfoById($data['province_id']);
            $address = $province->name;
        } catch (NotFound $e) {
            return $address;
        }

        if (empty($data['city_id'])) {
            return $address;
        }
        try {
            $city = $this->districtRepo->getInfoById($data['city_id']);
            $address .= $city->name;
        } catch (NotFound $e) {
            return $address;
        }

        if (empty($data['district_id'])) {
            return $address;
        }

        try {
            $district = $this->districtRepo->getInfoById($data['district_id']);
            $address .= $district->name;
        } catch (NotFound $e) {
            return $address;
        }

        if (!empty($data['address'])) {
            $address .= $data['address'];
        }

        return $address;
    }
}
