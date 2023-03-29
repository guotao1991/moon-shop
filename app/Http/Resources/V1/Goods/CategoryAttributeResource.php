<?php

namespace App\Http\Resources\V1\Goods;

use App\Http\Resources\V1\BaseResources;
use Illuminate\Http\Request;

/**
 * Class CategoryAttributeResource
 * @package App\Http\Resources\V1\Goods
 *
 * @property int $id
 * @property string $attribute_value
 */
class CategoryAttributeResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "attr_id" => $this->id,
            "attr_name" => $this->attribute_value,
        ];
    }
}
