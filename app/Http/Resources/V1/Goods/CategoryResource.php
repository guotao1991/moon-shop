<?php

namespace App\Http\Resources\V1\Goods;

use App\Http\Resources\V1\BaseResources;
use App\Models\Goods\CategoryAttributeModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class CategoryResource
 * @package App\Http\Resources\V1\Goods
 *
 * @property int $id
 * @property string $name
 * @property CategoryAttributeModel[] $categoryAttribute
 */
class CategoryResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "cat_id" => $this->id,
            "cat_name" => $this->name,
            "attr_list" => CategoryAttributeResource::collection($this->categoryAttribute)
        ];
    }
}
