<?php

namespace App\Repositories\V1;

use App\Exceptions\NotesException;
use App\Models\Admin\AdminModel;
use App\Models\Admin\AdminStoreModel;
use App\Models\Store\StoreModel;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;

class StoreRepository extends BaseRepository
{
    /**
     * 根据用户ID获取店铺列表
     *
     * @param int $adminId 管理员ID
     * @return StoreModel[]
     * @throws Exception
     */
    public function getStoreListByUserId(int $adminId)
    {
        return StoreModel::where("admin_id", $adminId)
            ->where("status", StoreModel::STATUS_OPEN)
            ->get();
    }

    /**
     * 根据店铺ID获取店铺信息
     * @param int $storeId 店铺ID
     * @return StoreModel
     * @throws NotFound
     */
    public function getStoreById(int $storeId): StoreModel
    {
        $info = StoreModel::whereId($storeId)->first();

        if (empty($info)) {
            throw new NotFound("店铺不存在");
        }

        return $info;
    }

    /**
     * 创建店铺
     *
     * @param array $data 店铺数据
     * @return StoreModel 店铺信息
     * @throws Exception
     */
    public function createStore(array $data): StoreModel
    {
        $store = new StoreModel();
        $store->store_name = $data['name'];
        $store->province_id = $data['province_id'];
        $store->city_id = $data['city_id'];
        $store->district_id = $data['district_id'];
        $store->address = $data['address'];
        $store->head_img = $data['store_image'] ?? "";
        $store->hq_id = $data['hq_id'];

        $res = $store->save();

        if (!$res) {
            throw new Exception("店铺添加失败");
        }
        $store->refresh();
        return $store;
    }

    /**
     * 修改店铺信息
     * @param int $storeId
     * @param array $data
     * @return StoreModel
     * @throws Exception
     */
    public function updateStoreById(int $storeId, array $data): StoreModel
    {
        $store = $this->getStoreById($storeId);

        $store->store_name = $data['store_name'];
        $store->province_id = $data['province_id'];
        $store->city_id = $data['city_id'];
        $store->district_id = $data['district_id'];
        $store->address = $data['address'];
        $store->lgt = $data['lgt'];
        $store->lat = $data['lat'];
        $store->head_img = $data['head_img'];
        $store->contact_name = $data['contact_name'];
        $store->mobile = $data['mobile'];

        $res = $store->save();

        if ($res === false) {
            throw new Exception("修改失败");
        }

        return $store;
    }

    /**
     * @param int $storeId
     * @return array
     * @throws Exception
     */
    public function getStoreSalesStatistics(int $storeId)
    {
    }

    /**
     * 根据用户ID获取用户的主店信息
     * @param int $adminId 管理员ID
     * @param int $storeId 店铺ID
     * @return StoreModel
     * @throws NotesException
     */
    public function getMainStore(int $adminId, int $storeId = 0): StoreModel
    {
        $info = StoreModel::withoutTrashed()
            ->where("admin_id", $adminId)
            ->when($storeId > 0, function ($query) use ($storeId) {
                $query->where("id", $storeId);
            })
            ->orderBy("created_at")
            ->first();
        if (empty($info)) {
            throw new NotesException("没有找到主店信息");
        }

        return $info;
    }

    /**
     * 查找管理员关联信息
     *
     * @param int $adminId
     * @param int $storeId
     * @return AdminStoreModel
     * @throws NotFound
     */
    public function getStoreByAdmin(int $adminId, int $storeId): AdminStoreModel
    {
        $info = AdminStoreModel::whereAdminId($adminId)
            ->where("store_id", $storeId)
            ->first();

        if (empty($info)) {
            throw new NotFound("没有找到店铺信息");
        }

        return $info;
    }
}
