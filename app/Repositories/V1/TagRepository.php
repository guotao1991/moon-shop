<?php

namespace App\Repositories\V1;

use App\Models\Admin\AdminModel;
use App\Models\Common\TagModel;
use App\Models\Goods\GoodsTagModel;
use App\Models\User\UserTagModel;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TagRepository extends BaseRepository
{
    /**
     * 获取HQ或者店铺的tag列表
     * @param AdminModel $admin
     * @return array
     */
    public function getTagListByAdmin(AdminModel $admin)
    {
        $hqId = $admin->last_hq_id;
        return UserTagModel::withoutTrashed()
            ->select(
                [
                    "tag_name",
                    DB::raw("count(id) as tag_count")
                ]
            )
            ->whereHas('user', function ($sql) use ($hqId) {
                /** @var Builder $sql */
                $sql->where("hq_id", $hqId);
            })
            ->orderBy("tag_count", "DESC")
            ->groupBy("tag_name")
            ->get();
    }

    /**
     * 添加标签
     * @param array $data
     * @return TagModel
     */
    public function addTag(array $data)
    {
        $tagName = $data["tag_name"] ?? "";
        $tagType = $data['tag_type'] ?? TagModel::TYPE_USER;

        $tagInfo = null;
        try {
            $tagInfo = $this->getTagByName($tagName);
        } catch (NotFound $nf) {
            //标签不存在，则添加标签
            $tagInfo = new TagModel();
            $tagInfo->tag_name = $tagName;
            $tagInfo->type = $tagType;
            $tagInfo->save();
            $tagInfo->refresh();
        }

        return $tagInfo;
    }

    /**
     * 根据标签名获取标签信息
     *
     * @param string $tagName 标签名
     * @return TagModel
     * @throws NotFound
     */
    public function getTagByName(string $tagName)
    {
        $tagInfo = TagModel::withoutTrashed()->where("tag_name", $tagName)->first();

        if (empty($tagInfo)) {
            throw new NotFound("标签不存在");
        }

        return $tagInfo;
    }

    /**
     * 获取商品的tag列表
     * @param AdminModel $admin
     * @return array
     */
    public function getGoodsTagListByAdmin(AdminModel $admin)
    {
        return GoodsTagModel::withoutTrashed()
            ->select(
                [
                    "tag_name",
                    DB::raw("count(id) as tag_count")
                ]
            )
            ->where("hq_id", $admin->last_hq_id)
            ->orderBy("tag_count", "DESC")
            ->groupBy("tag_name")
            ->get();
    }
}
