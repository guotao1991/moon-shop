<?php

namespace App\Services\V1;

use App\Exceptions\NotesException;
use App\Repositories\V1\SystemRepository;

class SystemService extends BaseService
{
    protected $systemRepo;

    /**
     * UserService constructor.
     * @param SystemRepository $systemRepo
     */
    public function __construct(SystemRepository $systemRepo)
    {
        $this->systemRepo = $systemRepo;
    }

    /**
     * @param string $mobile 手机号
     * @param string $smsCode 验证码
     * @param int $type 验证码类型
     * @return bool
     * @throws NotesException
     */
    public function verifySmsCode(string $mobile, string $smsCode, int $type): bool
    {
        //判断验证码是否有误
        $codeInfo = $this->systemRepo->getSmsCodeByMobile($mobile);

        if (empty($codeInfo)) {
            throw new NotesException("验证码错误");
        }

        if ($codeInfo->type != $type) {
            throw new NotesException("验证码错误");
        }

        if ($codeInfo->sms_code != $smsCode) {
            throw new NotesException("验证码错误");
        }

        $expiryTime = config("captcha.sms_code_expire_time");
        if ((time() - strtotime($codeInfo->created_at)) > $expiryTime) {
            throw new NotesException("验证码已过期，请重新获取");
        }
        return true;
    }

    /**
     * 批量查询未封禁到期的黑名单
     * @param array $ips IP数组
     * @return bool
     */
    public function isInBlackList(array $ips): bool
    {
        return $this->systemRepo->isInBlackList($ips);
    }

    /**
     * 获取系统设置
     */
    public function systemSet()
    {
        return $this->systemRepo->getSystemSet();
    }
}
