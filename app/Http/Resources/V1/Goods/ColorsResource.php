<?php

namespace App\Http\Resources\V1\Goods;

use App\Http\Resources\V1\BaseResources;
use App\Models\Goods\CategoryAttributeModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class ColorsResource
 * @package App\Http\Resources\V1\Goods
 *
 * @property int $id
 * @property string $color_name
 */
class ColorsResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "color_id" => $this->id,
            "color_name" => $this->color_name,
        ];
    }
}
