<?php

namespace App\Http\Controllers\V1\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Common\Index\SendSmsCodeRequest;
use App\Services\V1\DistrictService;
use App\Services\V1\SmsService;
use App\Services\V1\UploadService;
use App\Utils\Helper;
use Exception;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    protected $smsService;

    public function __construct(
        SmsService $smsService
    ) {
        $this->smsService = $smsService;
    }

    /**
     * 发送验证码
     *
     * @param SendSmsCodeRequest $request
     * @return mixed
     * @throws Exception
     */
    public function sendSmsCode(SendSmsCodeRequest $request)
    {
        $data = $request->validated();
        $this->smsService->sendSmsMsg($data);

        return $this->success();
    }

    /**
     * 生成验证码
     * @param Request $request
     * @return mixed
     */
    public function verifyCode(Request $request)
    {
        return $this->success(app('captcha')->create('default', true));
    }

    /**
     * 获取省市区列表
     * @param Request $request
     * @param DistrictService $districtService
     * @return array|mixed
     * @throws Exception
     */
    public function getDistrictList(Request $request, DistrictService $districtService)
    {
        return $districtService->getDistrictList();
    }

    /**
     * 上传图片接口
     *
     * @param Request $request
     * @param UploadService $service
     * @return mixed
     */
    public function uploadImg(Request $request, UploadService $service)
    {
        try {
            $file = $request->file("file_data");

            if (empty($file)) {
                return $this->failed(__("common.non_file"));
            }

            return $service->uploadImg($file);
        } catch (Exception $e) {
            Helper::errLog($e, $request);
            return $this->info(500, __("system.error"));
        }
    }
}
