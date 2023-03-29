<?php

namespace App\Http\Resources\V1\Store;

use App\Http\Resources\V1\BaseResources;
use App\Models\Admin\AdminHqModel;
use Illuminate\Http\Request;

/**
 * Class StoreResource
 *
 * @property int $id
 * @property string $name
 * @property string $contact_name
 * @property string $mobile
 * @property int $last_store_id
 * @property AdminHqModel $pivot
 *
 */
class HqResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $lastStoreId = 0;
        if (!empty($this->pivot)) {
            $lastStoreId = $this->pivot->last_store_id;
        }
        return [
            "hq_id" => $this->id,
            "hq_name" => $this->name,
            "contact_name" => $this->contact_name,
            "mobile" => $this->mobile,
            "last_store_id" => $lastStoreId
        ];
    }
}
