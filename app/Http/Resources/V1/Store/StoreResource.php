<?php

namespace App\Http\Resources\V1\Store;

use App\Http\Resources\V1\BaseResources;
use Illuminate\Http\Request;

/**
 * Class StoreResource
 *
 * @property int $id
 * @property string $store_name
 * @property int $province_id
 * @property int $city_id
 * @property int $district_id
 * @property string $address
 * @property string $lgt
 * @property string $lat
 * @property string $head_img
 * @property int $status
 * @property string $contact_name
 * @property int $hq_id
 *
 */
class StoreResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "store_id" => $this->id,
            "store_name" => $this->store_name,
            "province_id" => $this->province_id,
            "city_id" => $this->city_id,
            "district_id" => $this->district_id,
            "address" => $this->address,
            "lgt" => $this->lgt,
            "lat" => $this->lat,
            "head_img" => $this->head_img,
            "status" => $this->status,
            "contact_name" => $this->contact_name,
            "hq_id" => $this->hq_id,
        ];
    }
}
