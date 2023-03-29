<?php

namespace App\Services\V1;

use App\Repositories\V1\TagRepository;
use App\Utils\Helper;
use Exception;

class TagService extends BaseService
{
    protected $tagRepo;

    /**
     * UserService constructor.
     * @param TagRepository $tagRepo
     */
    public function __construct(
        TagRepository $tagRepo
    ) {
        $this->tagRepo = $tagRepo;
    }

    /**
     * 获取用户标签列表
     *
     * @return array
     * @throws Exception
     */
    public function getTagListByAdmin()
    {
        $admin = Helper::admin();
        return $this->tagRepo->getTagListByAdmin($admin);
    }

    /**
     * 获取商品标签列表
     *
     * @return array
     * @throws Exception
     */
    public function getGoodsTagListByAdmin()
    {
        $admin = Helper::admin();
        return $this->tagRepo->getGoodsTagListByAdmin($admin);
    }

    /**
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function addTag(array $data)
    {
        return $this->tagRepo->addTag($data);
    }
}
