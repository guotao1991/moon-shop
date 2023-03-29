<?php

namespace App\Http\Resources\V1\Store;

use App\Http\Resources\V1\BaseResources;
use App\Http\Resources\V1\Common\TagResource;
use App\Http\Resources\V1\User\LevelResource;
use App\Models\Common\TagModel;
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
 * @property string|null $remark
 * @property int $total_pay_amount
 * @property int $total_profit_amount
 * @property int $total_reward_points
 * @property int $level_id
 * @property int $attention
 * @property int $current_month_pay_amount
 *
 * @property UserModel $user
 * @property LevelModel $level
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
            'user_id' => $this->id,
            'nick_name' => $this->user->nick_name,
            'head_img' => $this->user->head_img,
            'sex' => $this->user->sex,
            'birthday' => Helper::getDate($this->user->birthday, "Y-m-d"),
            'province_name' => $this->user->province_name,
            'city_name' => $this->user->city_name,
            'district_name' => $this->user->district_name,
            "mobile" => empty($this->mobile) ? "" : substr_replace($this->mobile, '****', 3, 4),
            "remark" => $this->remark,
            "total_pay_amount" => $this->total_pay_amount,
            "total_profit_amount" => $this->total_profit_amount,
            "total_reward_points" => $this->total_reward_points,
            "attention" => $this->attention,
            "current_month_pay_amount" => $this->current_month_pay_amount,
            "level" => new LevelResource($this->level)
        ];
    }
}
