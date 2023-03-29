<?php

namespace App\Http\Resources\V1\User;

use App\Http\Resources\V1\BaseResources;
use App\Http\Resources\V1\Common\TagResource;
use App\Models\Common\TagModel;
use App\Models\User\LevelModel;
use App\Models\User\UserModel;
use App\Utils\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class HqUserResource
 * @package App\Http\Resources\V1\User
 *
 * @property int $total_pay_amount
 * @property int $total_profit_amount
 * @property int $total_reward_points
 * @property int $current_month_pay_amount
 * @property int $attention
 * @property Carbon $last_consumption_time
 * @property string $remark
 *
 * @property UserModel $user
 * @property LevelModel $level
 */
class HqUserResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'user_id' => $this->user->id,
            'nick_name' => $this->user->nick_name ?? "",
            'head_img' => $this->user->head_img ?? "",
            'sex' => $this->user->sex,
            'birthday' => Helper::getDate($this->user->birthday, "Y-m-d"),
            'province_name' => $this->user->province_name ?? "",
            'city_name' => $this->user->city_name ?? "",
            'district_name' => $this->user->district_name ?? "",
            'status' => $this->user->status,
            'last_login_time' => Helper::getDate($this->user->last_login_time),
            "remark" => $this->remark ?? "",
            "mobile" => substr_replace($this->user->mobile, '****', 3, 4),
            'level' => new LevelResource($this->level),
            "total_pay_amount" => Helper::formatPrice($this->total_pay_amount),
            "total_profit_amount" => Helper::formatPrice($this->total_profit_amount),
            "total_reward_points" => $this->total_reward_points,
            "current_month_pay_amount" => Helper::formatPrice($this->current_month_pay_amount),
            "attention" => $this->attention,
            "last_consumption_time" => Helper::getDate($this->last_consumption_time),
            "tags" => TagResource::collection($this->user->tags)
        ];
    }
}
