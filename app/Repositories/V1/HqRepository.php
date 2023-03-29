<?php

namespace App\Repositories\V1;

use App\Exceptions\DbErrorException;
use App\Models\Admin\AdminHqModel;
use App\Models\Store\HqModel;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Support\Facades\DB;

class HqRepository extends BaseRepository
{
    /**
     * 根据用户ID获取用户的HQ详情
     * @param int $userId 用户ID
     * @return HqModel
     * @throws Exception
     */
    public function getInfoByUser(int $userId): HqModel
    {
        $hqInfo = HqModel::whereUserId($userId)->first();

        if (empty($hqInfo)) {
            throw new Exception("没有找到HQ");
        }
        return $hqInfo;
    }

    /**
     * 查找管理员关联信息
     * @param int $adminId
     * @param int $hqId
     * @return AdminHqModel
     * @throws NotFound
     */
    public function getInfoByAdmin(int $adminId, int $hqId): AdminHqModel
    {
        $info = AdminHqModel::whereAdminId($adminId)
            ->where("hq_id", $hqId)
            ->first();

        if (empty($info)) {
            throw new NotFound("没有找到总部信息");
        }

        return $info;
    }

    /**
     * 创建HQ
     *
     * @param int $adminId
     * @param array $data
     * @return HqModel
     * @throws Exception
     */
    public function createHq(int $adminId, array $data): HqModel
    {
        $hq = new HqModel();
        $hq->name = $data['hq_name'];
        $hq->contact_name = $data['contact_name'] ?? "";
        $hq->mobile = $data['mobile'] ?? "";

        //添加HQ
        DB::beginTransaction();
        $res = $hq->save();

        if ($res == false) {
            DB::rollBack();
            throw new DbErrorException("创建组织失败");
        }
        $hq->refresh();

        //添加HQ和admin关联
        $adminHq = new AdminHqModel();
        $adminHq->admin_id = $adminId;
        $adminHq->hq_id = $hq->id;
        $res = $adminHq->save();

        if ($res == false) {
            DB::rollBack();
            throw new DbErrorException("创建组织失败");
        }

        DB::commit();

        return $hq;
    }
}
