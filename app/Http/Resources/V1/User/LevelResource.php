<?php

namespace App\Http\Resources\V1\User;

use App\Http\Resources\V1\BaseResources;
use App\Utils\Helper;
use Illuminate\Http\Request;

/**
 * Class LevelResource
 * @package App\Http\Resources\V1\User
 *
 * @property int $id
 * @property string $level_name
 * @property int $consumption_amount
 * @property int $discount
 *
 */
class LevelResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'level_id' => $this->id,
            'level_name' => $this->level_name,
            'consumption_amount' => Helper::formatPrice($this->consumption_amount),
            'discount' => $this->discount,
        ];
    }
}
