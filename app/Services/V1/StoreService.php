<?php

namespace App\Services\V1;

use App\Exceptions\NotesException;
use App\Models\Store\StoreModel;
use App\Repositories\V1\StoreRepository;
use App\Utils\BaiduMapApi;
use App\Utils\Helper;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;

/**
 * Class StoreService
 * @package App\Services\V1
 *
 */
class StoreService extends BaseService
{
    protected $storeRepo;

    /**
     * StoreService constructor.
     * @param StoreRepository $storeRepo
     */
    public function __construct(StoreRepository $storeRepo)
    {
        $this->storeRepo = $storeRepo;
    }

    /**
     * 根据用户信息获取用户的店铺列表
     * @return mixed
     * @throws Exception
     */
    public function getStoreListByUserId()
    {
        $admin = Helper::admin();
        return $this->storeRepo->getStoreListByUserId($admin->id);
    }

    /**
     * 创建店铺
     * @param array $data 店铺数据
     * @return StoreModel
     * @throws Exception
     */
    public function createStore(array $data): StoreModel
    {
        $admin = Helper::admin();
        $user = Helper::user();

        $hq = null;
        $hqCount = $admin->hqList->count();
        if ($hqCount <= 0) {
            //先增加HQ
            /** @var HqService $hqService */
            $hqService = app(HqService::class);

            $hq = $hqService->createHq(['hq_name' => $data['name']]);
            $user->login_hq_id = $hq->id;
            $user->save();
        }

        $hqUser = $user->hqUser;
        dd($hqUser);
        $data['hq_id'] = $hqUser->hq_id;

        //获取经纬度
        /** @var DistrictService $district */
        $district = app(DistrictService::class);
        $address = $district->getPcdInfo($data);
        $geoCoder = BaiduMapApi::getGeoCoderByAddress($address);

        dd($geoCoder);

        return $this->storeRepo->createStore($data);
    }

    /**
     * 修改店铺信息
     *
     * @param array $data 修改店铺数据
     * @return mixed
     * @throws Exception
     */
    public function updateStore(array $data)
    {
        return $this->storeRepo->updateStoreById($data['store_id'], $data);
    }

    /**
     * 获取店铺详情
     * @param int $storeId 店铺ID
     * @return mixed
     * @throws Exception
     */
    public function getStoreInfo(int $storeId)
    {
        $storeInfo = $this->storeRepo->getStoreById($storeId);
        $admin = Helper::admin();
        if ($storeInfo->admin_id != $admin->id) {
            throw new NotesException("店铺不存在");
        }

        return $storeInfo;
    }

    /**
     * 获取用户主店信息
     * @return mixed
     * @throws Exception
     */
    public function getMainStore()
    {
        $admin = Helper::admin();
        $storeId = (int)$admin->hq->last_store_id;
        return $this->storeRepo->getMainStore($admin->id, $storeId);
    }


    /**
     * 根据ID获取店铺信息
     *
     * @param int $storeId 店铺ID
     * @return StoreModel
     * @throws NotFound
     */
    public function getStoreById($storeId): StoreModel
    {
        return $this->storeRepo->getStoreById($storeId);
    }

    /**
     * 获取管理员管理的店铺
     *
     * @return StoreModel[]
     * @throws Exception
     */
    public function getStoreListByAdmin()
    {
        $admin = Helper::admin();
        return $admin->storeList;
    }

    /**
     * admin 进入店铺
     *
     * @param int $storeId 店铺ID
     * @return bool
     * @throws Exception
     */
    public function intoStore(int $storeId)
    {
        $admin = Helper::admin();

        $this->storeRepo->getStoreByAdmin($admin->id, $storeId);
        $admin->last_store_id = $storeId;
        $admin->last_hq_id = 0;
        $res = $admin->save();

        if ($res == false) {
            throw new NotesException("进入店铺失败");
        }

        return true;
    }

    /**
     * 根据ID获取HQ下面的所有店铺
     *
     * @param int $hqId HQ ID
     * @param array $storeIds 店铺ID数组
     * @return StoreModel[]
     */
    public function getAdminStoreByIds(int $hqId, array $storeIds)
    {
        if (empty($storeIds)) {
            return [];
        }

        $list = StoreModel::withoutTrashed()
            ->whereIn("id", $storeIds)
            ->where("hq_id", $hqId)
            ->get();

        if (count($list) > 0) {
            return $list;
        }

        return [];
    }
}
