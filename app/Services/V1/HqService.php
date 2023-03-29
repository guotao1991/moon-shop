<?php

namespace App\Services\V1;

use App\Exceptions\NotesException;
use App\Models\Store\HqModel;
use App\Models\Store\StoreModel;
use App\Repositories\V1\HqRepository;
use App\Utils\Helper;
use Exception;

/**
 * Class HqService
 * @package App\Services\V1
 */
class HqService extends BaseService
{
    protected $hqRepo;
    /**
     * StoreService constructor.
     * @param HqRepository $hqRepo
     */
    public function __construct(HqRepository $hqRepo)
    {
        $this->hqRepo = $hqRepo;
    }

    /**
     * 获取管理员管理的HQ
     *
     * @throws Exception
     */
    public function getHqListByAdmin()
    {
        $admin = Helper::admin();

        return $admin->storeList();
    }

    /**
     * 新增HQ
     *
     * @param array $data
     * @return HqModel
     *
     * @throws Exception
     */
    public function createHq(array $data): HqModel
    {
        $admin = Helper::admin();
        return $this->hqRepo->createHq($admin->id, $data);
    }

    /**
     * 记录admin进入HQ
     * @param int $hqId
     * @return bool
     * @throws Exception
     */
    public function intoHq(int $hqId): bool
    {
        $admin = Helper::admin();
        $this->hqRepo->getInfoByAdmin($admin->id, $hqId);

        $admin->last_hq_id = $hqId;
        $admin->last_store_id = 0;
        $res = $admin->save();

        if ($res === false) {
            throw new NotesException("进入店铺失败");
        }

        return true;
    }

    /**
     *
     * @throws Exception
     */
    public function storeList()
    {
        $admin = Helper::admin();
        //判断是不是店长
    }

    /**
     * 获取用户名下的HQ 列表
     * @return HqModel[]
     * @throws Exception
     */
    public function hqList()
    {
        $admin = Helper::admin();

        return $admin->hqList;
    }
}
