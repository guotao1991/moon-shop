<?php

namespace App\Http\Resources\V1\User;

use App\Http\Resources\V1\BaseResources;
use App\Http\Resources\V1\Common\TagResource;
use App\Models\Common\TagModel;
use App\Models\User\LevelModel;
use App\Utils\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PHPUnit\TextUI\Help;

/**
 * Class UserResource
 * @package App\Http\Resources\V1\User
 *
 * @property int $id
 * @property string $nick_name
 * @property string $mobile
 * @property string $head_img
 * @property int $sex
 * @property Carbon|null $birthday
 * @property string $province_name
 * @property string $city_name
 * @property string $district_name
 * @property string $token
 * @property Carbon $last_login_time
 * @property int $is_authorized
 *
 */
class UserResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'user_id' => $this->id,
            'nick_name' => $this->nick_name,
            'head_img' => $this->head_img,
            'sex' => $this->sex,
            'birthday' => Helper::getDate($this->birthday, "Y-m-d"),
            'province_name' => $this->province_name,
            'city_name' => $this->city_name,
            'district_name' => $this->district_name,
            'token' => $this->token,
            'last_login_time' => Helper::getDate($this->last_login_time),
            "mobile" => empty($this->mobile) ? "" : substr_replace($this->mobile, '****', 3, 4),
            "is_authorized" => $this->is_authorized,
        ];
    }
}
