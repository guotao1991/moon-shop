<?php

namespace App\Http\Resources\V1\User;

use App\Http\Resources\V1\BaseResources;
use App\Http\Resources\V1\Common\TagResource;
use App\Http\Resources\V1\Store\HqResource;
use App\Http\Resources\V1\Store\StoreResource;
use App\Http\Resources\V1\User\LevelResource;
use App\Models\Common\TagModel;
use App\Models\Store\HqModel;
use App\Models\Store\StoreModel;
use App\Models\User\LevelModel;
use App\Models\User\UserModel;
use App\Utils\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PHPUnit\TextUI\Help;

/**
 * Class StoreUserResource
 * @package App\Http\Resources\V1\Store
 *
 * @property int $id
 * @property int $hq_id
 * @property int $store_id
 * @property int $user_id
 * @property int $level_id
 * @property
 *
 * @property UserModel $user
 * @property LevelModel $level
 * @property StoreModel $store
 * @property HqModel $hq
 *
 */
class StoreUserResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'user_id' => $this->user->id,
            'nick_name' => $this->user->nick_name,
            'head_img' => $this->user->head_img,
            'sex' => $this->user->sex,
            'birthday' => Helper::getDate($this->user->birthday, "Y-m-d"),
            'province_name' => $this->user->province_name,
            'city_name' => $this->user->city_name,
            'district_name' => $this->user->district_name,
            "mobile" => empty($this->user->mobile) ? "" : substr_replace($this->user->mobile, '****', 3, 4),
            "total_reward_points" => $this->total_reward_points ?? 0,
            "level" => new LevelResource($this->level),
            "store" => !empty($this->store) ? new StoreResource($this->store) : [],
            "hq" => !empty($this->hq) ? new HqResource($this->hq) : [],
            "is_authorized" => $this->user->is_authorized,
            //@todo 获取用户订单列表
            "orders" => []
        ];
    }
}
