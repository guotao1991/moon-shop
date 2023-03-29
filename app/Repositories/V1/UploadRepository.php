<?php

namespace App\Repositories\V1;

use App\Utils\AliYun;
use Exception;

class UploadRepository
{
    /**
     * 上传图片
     * @param string $file 图片地址
     * @param string $uploadName 图片名字
     * @return array
     * @throws Exception
     */
    public function uploadImg(string $file, string $uploadName = "")
    {
        try {
            if (empty($uploadName)) {
                $uploadName = MD5(uniqid() . $file) . "";
                $fileInfo = getimagesize($file);

                //判断图片是否合法 1 = GIF，2 = JPG，3 = PNG，6 = BMP
                switch ($fileInfo[2]) {
                    case 1:
                        $uploadName .= ".gif";
                        break;
                    case 2:
                        $uploadName .= ".jpg";
                        break;
                    case 3:
                        $uploadName .= ".png";
                        break;
                    case 6:
                        $uploadName .= ".bmp";
                        break;
                    default:
                        return array("code" => 2, "data" => "", "msg" => "文件类型不支持");
                }
            }
            $aliyunBucket = config("aliyun.oss.bucket");
            $aliyunDomin = config("aliyun.oss.end_point");
            $aliyunAccessKey = config("aliyun.oss.access_key_id");
            $aliyunSecretKey = config("aliyun.oss.access_key_secret");

            //上传到阿里云
            $aliyun = new AliYun($aliyunAccessKey, $aliyunSecretKey, $aliyunDomin);

            return $aliyun->uploadImg($file, $uploadName, $aliyunBucket);

        } catch (Exception $e) {
            throw $e;
        }
    }
}
