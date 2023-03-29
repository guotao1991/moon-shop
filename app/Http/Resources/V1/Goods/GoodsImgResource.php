<?php

namespace App\Http\Resources\V1\Goods;

use App\Http\Resources\V1\BaseResources;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class GoodsImgResource
 * @package App\Http\Resources\V1\Goods
 *
 * @property int $id
 * @property string $img_url
 */
class GoodsImgResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "img_id" => $this->id,
            "img_url" => $this->img_url,
        ];
    }
}
