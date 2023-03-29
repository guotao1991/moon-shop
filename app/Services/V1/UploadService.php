<?php

namespace App\Services\V1;

use App\Repositories\V1\UploadRepository;
use App\Traits\JsonResponse;
use Illuminate\Http\UploadedFile;
use Exception;

class UploadService
{
    use JsonResponse;

    protected $repo;
    public function __construct(UploadRepository $uploadRepository)
    {
        $this->repo = $uploadRepository;
    }

    /**
     * 上传图片
     * @param UploadedFile $file
     * @return array|mixed
     * @throws Exception
     */
    public function uploadImg(UploadedFile $file)
    {
        try {
            $fileName = $file->getPathname();
            $res = $this->repo->uploadImg($fileName);
            if ($res["code"] != 1) {
                return $this->failed($res["msg"]);
            }
            return $this->success(array("img_url" => $res["data"]));
        } catch (Exception $e) {
            throw $e;
        }
    }
}
