<?php

namespace App\Repositories\V1;

use App\Exceptions\DbErrorException;
use App\Models\System\SmsCodeModel;
use Exception;

class SmsCodeRepository extends BaseRepository
{

    /**
     * 添加用户
     * @param array $data 用户信息
     * @return SmsCodeModel
     * @throws Exception
     */
    public function addSmsCode(array $data): SmsCodeModel
    {
        $smsCode = new SmsCodeModel();
        $smsCode->mobile = $data['mobile'];
        $smsCode->sms_code = $data['sms_code'];
        $smsCode->type = $data['type'];
        $res = $smsCode->save();

        if (!$res) {
            throw new DbErrorException("验证码插入失败");
        }

        $smsCode->refresh();
        return $smsCode;
    }
}
