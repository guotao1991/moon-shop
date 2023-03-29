<?php

namespace App\Http\Resources\V1\Admin;

use App\Http\Resources\V1\BaseResources;
use App\Models\User\UserModel;
use App\Utils\Helper;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class AdminResource
 * @package App\Http\Resources\V1\Admin
 *
 * @property int $id
 * @property int $status
 * @property string $token
 * @property Carbon $last_login_time
 *
 * @property UserModel $user
 */
class AdminResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
          'nick_name' => $this->user->nick_name,
          'head_img' => $this->user->head_img,
          'province_name' => $this->user->province_name,
          'city_name' => $this->user->city_name,
          'district_name' => $this->user->district_name,
          'status' => $this->status,
          'token' => $this->token,
          'last_login_time' => Helper::getDate($this->last_login_time),
        ];
    }
}
