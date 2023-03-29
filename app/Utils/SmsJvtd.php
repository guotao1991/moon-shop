<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/3
 * Time: 14:58
 */

namespace App\Utils;

use App\Models\ApiLog;
use Exception;

/**
     * 发送短信
     *
     * 短信通知
     * @param string $store_id 商铺ID
     * @param string $content 内容，字符串
     * @return array
     */
class SmsJvtd
{

    protected const SEND_SMS_URL = "https://smshttp.jvtd.cn/jtdsms/smsSend.do";

    private $url;
    // $url string 发送短信的接口 例如 http://ip:8090/jtdsms/smsSend.do
    private $uid;
    // $uid string 用户的id
    private $pass; // $pass string 用户的密码

    public function __construct($uid, $pass)
    {
        $this->uid = $uid;
        $this->pass = $pass;
    }

    /**
     * 发送短信
     * $mobile 要发送的手机号
     * $content 要发送的内容
     * @param $mobile
     * @param $content
     * @return mixed
     * @throws Exception
     */
    public function sendSms($mobile, $content)
    {
        try {
            //组装数组
            $data = array(
                'uid' => $this->uid,
                'password' => strtoupper(MD5($this->pass)) ,
                'mobile' => $mobile,//多条例子 xxxx,xxxx
                'encode' => 'utf8',
                'content' => base64_encode($content),//把内容进行base64转换
                'encodeType' => 'base64',
                'cid' => '',// 唯一标识，选填，如果不填系统自动生成作为当前批次的唯一标识
                'extNumber' => '',// 扩展 选填
                'schtime' => ''// 定时时间，选填，格式2008-06-09 12:00:00
            );

            $apiLog = new ApiLog();

            $id = $apiLog->addLog(self::SEND_SMS_URL, $data, "", 2, "send_sms", "发送短信");
            $res =  $this->httpPost(self::SEND_SMS_URL, $data);

            $apiLog->addResponse($id, $res);

            return $res;
        } catch (Exception $e) {
            throw $e;
        }
    }


    /**
     * php post提交数据
     * @param string $url 请求地址
     * @param mixed $data 请求参数
     * @return mixed
     * @throws Exception
     */
    private function httpPost(string $url, $data)
    {
        try {
            // 启动一个CURL会话
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);//接口地址
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/x-www-form-urlencoded'
            ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            // 执行操作
            $response_body = curl_exec($curl);
            //捕抓异常
            $error_msg = "";
            if (curl_errno($curl)) {
                $error_msg = 'Errno' . curl_error($curl);
            }
            // 关闭CURL会话
            curl_close($curl);
            // 返回结果
            $response["response_body"] = $response_body;//请求接口返回的数据 大于0代表成功，否则根据返回值查找错误
            $response["error_msg"] = $error_msg;//curl post 提交发生的错误
            return $response;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
