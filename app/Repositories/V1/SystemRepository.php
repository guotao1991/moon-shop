<?php

namespace App\Repositories\V1;

use App\Exceptions\NotesException;
use App\Models\System\DictModel;
use App\Models\System\IpBlackListModel;
use App\Models\System\SmsCodeModel;

class SystemRepository extends BaseRepository
{

    /**
     * 根据手机号获取短信验证码
     *
     * @param string $mobile 手机号
     * @return SmsCodeModel
     * @throws NotesException
     */
    public function getSmsCodeByMobile(string $mobile): SmsCodeModel
    {
        $smsCodeArr = SmsCodeModel::withoutTrashed()
            ->where("mobile", $mobile)
            ->orderBy("created_at", "DESC")
            ->first();
        if (empty($smsCodeArr)) {
            throw new NotesException("验证码错误");
        }

        return $smsCodeArr;
    }

    /**
     * 查询IP是否被加入黑名单
     *
     * @param array $ips ip
     * @return bool
     */
    public function isInBlackList(array $ips): bool
    {
        $count = IpBlackListModel::withoutTrashed()
            ->whereIn("ip_address", $ips)
            ->count();

        if ($count > 0) {
            return true;
        }
        return false;
    }

    /**
     * 获取系统设置
     * @return array
     */
    public function getSystemSet()
    {
        $list = DictModel::withoutTrashed()
            ->where("group", "system")
            ->get();

        $res = [];

        foreach ($list as $item) {
            $res[$item->key] = $item->value;
        }

        return $res;
    }
}
