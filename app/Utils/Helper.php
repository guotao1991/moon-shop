<?php

namespace App\Utils;

use App\Models\Admin\AdminModel;
use App\Models\System\ErrorLogModel;
use App\Models\User\UserModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class Helper
{
    /**
     * 记录系统错误
     * @param Throwable $e 错误信息
     * @param Request $request 请求信息
     */
    public static function errLog(Throwable $e, Request $request = null)
    {
        if (!empty($request)) {
            $log = new ErrorLogModel();
            $log->code_file = $e->getFile();
            $log->error_msg = $e->getMessage() . "，第" . $e->getLine() . "行，";
            $log->request_url = $request->url();
            $log->request_params = json_encode($request->all());
            $log->request_header = json_encode($request->headers);
            $log->client_ip = json_encode($request->ips());
            $log->client_ua = $request->userAgent();
            $log->save();
        }
    }

    /**
     * 检验手机号
     * @param string $mobile
     * @return string
     */
    public static function checkMobile($mobile)
    {
        $check = '/^[1][3456789]\d{9}$/';
        if (!preg_match($check, $mobile)) {
            return false;
        }
        return true;
    }

    /**
     * 获取用户信息
     *
     * @return UserModel
     * @throws Exception
     */
    public static function user(): UserModel
    {
        $info = session()->get("user.info");

        if (empty($info)) {
            throw new Exception("用户未登录，请重新登录");
        }

        return $info;
    }

    /**
     * 获取管理员信息
     *
     * @return AdminModel
     * @throws Exception
     */
    public static function admin(): AdminModel
    {
        $info = session()->get("admin.info");

        if (empty($info)) {
            throw new Exception("用户未登录，请重新登录");
        }

        return $info;
    }

    /**
     * 价格转换
     *
     * @param int $price 价格：单位分
     *
     * @return string
     */
    public static function formatPrice(int $price)
    {
        return sprintf("%.2f", bcdiv($price, 100, 2));
    }

    /**
     * 价格转换
     *
     * @param int $price 价格：单位元
     *
     * @return string
     */
    public static function convertPrice(int $price)
    {
        return bcmul($price, 100);
    }

    /**
     * 时间转换
     * @param $date
     * @param string $format
     * @return string
     */
    public static function getDate($date, $format = "Y-m-d H:i:s")
    {
        $date = $date ?? "";
        if ($date instanceof Carbon) {
            $date = $date->format($format);
        }

        return $date;
    }
}
