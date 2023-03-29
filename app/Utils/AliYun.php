<?php

namespace App\Utils;

use Exception;
use OSS\OssClient;

class AliYun
{
    protected $ossClient;

    /**
     * AliYun constructor.
     * @param $accessKeyId
     * @param $accessKeySecret
     * @param $endpoint
     * @throws Exception
     */
    public function __construct($accessKeyId, $accessKeySecret, $endpoint)
    {
        try {
            $this->ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 上传图片，普通
     * @param $file
     * @param $fileName
     * @param $bucket
     * @return array
     * @throws Exception
     */
    public function uploadImg($file, $fileName, $bucket)
    {
        try {
            $fileInfo = getimagesize($file);

            //判断图片是否合法 1 = GIF，2 = JPG，3 = PNG，6 = BMP
            if (!in_array($fileInfo[2], array(1, 2, 3, 6))) {
                return array("code" => 2, "data" => "", "msg" => "上传图片类型不支持");
            }

            //上传图片
            $res = $this->ossClient->uploadFile($bucket, $fileName, $file);

            if (empty($res["info"]["url"])) {
                return array("code" => 2, "data" => "", "msg" => "上传图片失败");
            }

            return array(
                "code" => 1,
                "data" => config("aliyun.oss.static_domain") . "/" . basename($res["info"]["url"]),
                "msg" => ''
            );
        } catch (Exception $e) {
            throw $e;
        }
    }
}
