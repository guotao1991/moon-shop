<?php

namespace App\Services\V1;

use App\Repositories\V1\SmsCodeRepository;
use Exception;

class SmsService extends BaseService
{
    protected $smsCodeRepo;
    /**
     * SmsService constructor.
     * @param SmsCodeRepository $smsCodeRepo
     */
    public function __construct(
        SmsCodeRepository $smsCodeRepo
    ) {
        $this->smsCodeRepo = $smsCodeRepo;
    }

    /**
     * 发送验证码
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function sendSmsMsg(array $data): bool
    {
        $data["sms_code"] = rand(100000, 999999);
        $uid = env("SMS_UID");
        $pass = env("SMS_PASS");
        $expiryTime = env("SMS_CODE_EXPIRE_TIME");
        //$smsJvtd = new SmsJvtd($uid, $pass);
        $smsContent = env("VERIFY_CODE_CONTENT");
        $smsContent = str_replace(array("{CODE}","{TIME}"), array($data['sms_code'],$expiryTime / 60), $smsContent);

        $this->smsCodeRepo->addSmsCode($data);

        return true;
    }
}
