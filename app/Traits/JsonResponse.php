<?php

/**
 * Created by PhpStorm.
 * User: Yshiba
 * Date: 2021/3/26
 * Time: 18:14
 * Description:
 */

namespace App\Traits;

trait JsonResponse
{
    protected $codeMessage = [
        200 => '请求成功：success',
        201 => '请求失败：error',
        202 => '登录失败：error',
        203 => '上传失败：fail',
        208 => '参数错误：missing params',
        400 => '常规错误：error',
        401 => '无系统操作权限：no permission',
        403 => '无资源访问权限：forbidden',
        404 => '页面未找到：not found',
        405 => '请求方法有误：method not allowed',
        409 => '文件占用：already exists',
        500 => '系统错误：internal server error',
        502 => '请求超时：request timeout',
        503 => '超出账户限制：traffic rate limit exceeded'
    ];


    /**
     * @param array $data
     * @param string $message
     * @return mixed
     */
    public function success($data = [], $message = "请求成功：success")
    {
        if (empty($data)) {
            $data = [];
        }
        return [
            'code' => 200,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * 失败返回
     *
     * @param string $message
     * @param array $data 错误信息
     * @return mixed
     */
    public function failed(string $message = '', $data = [])
    {
        if (empty($message)) {
            $message = '';
        }
        return [
            'code' => 201,
            'message' => $message ?? '常规错误：error',
            'data' => $data
        ];
    }

    /**
     * 自定义返回内容
     *
     * @param int $code
     * @param string $message
     * @param array $data
     * @return mixed
     */
    public function info(int $code, string $message = '', array $data = [])
    {
        if (isset($this->codeMessage[$code]) && !$message) {
            $message = $this->codeMessage[$code];
        }
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }
}
