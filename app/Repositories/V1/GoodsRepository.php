<?php

namespace App\Repositories\V1;

use App\Exceptions\DbErrorException;
use App\Models\Admin\AdminModel;
use App\Models\Common\TagModel;
use App\Models\Goods\CategoryModel;
use App\Models\Goods\GoodsAttributeModel;
use App\Models\Goods\GoodsImgModel;
use App\Models\Goods\GoodsModel;
use App\Models\Goods\GoodsStoreModel;
use App\Models\Goods\GoodsTagModel;
use App\Models\Store\StoreModel;
use App\Utils\Helper;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GoodsRepository extends BaseRepository
{
    /**
     * 插入商品表
     *
     * @param array $data
     * @return GoodsModel
     * @throws DbErrorException
     */
    public function addGoods(array $data): GoodsModel
    {
        /** @var CategoryModel $cat */
        $cat = $data['cat'];

        $goods = new GoodsModel();
        $goods->goods_name = $data['name'];
        $goods->cat_id = $cat->id;
        $goods->purchase_price = Helper::convertPrice($data['purchase_price']);
        $goods->min_price = Helper::convertPrice($data['price']);
        $goods->max_price = Helper::convertPrice($data['price']);
        $goods->stock_num = (int)$data['stock'];
        $goods->cover_img = $data['master_map'];
        $goods->goods_detail = $data['detail'];
        $res = $goods->save();

        if (!$res) {
            throw new DbErrorException("插入商品表失败");
        }

        $goods->refresh();
        return $goods;
    }

    /**
     * 插入商品图片信息
     *
     * @param GoodsModel $goods
     * @param string $imgUrl
     * @return GoodsImgModel
     * @throws DbErrorException
     */
    public function addGoodsImg(GoodsModel $goods, string $imgUrl): GoodsImgModel
    {
        $goodsImg = new GoodsImgModel();
        $goodsImg->goods_id = $goods->id;
        $goodsImg->img_url = $imgUrl;
        $res = $goodsImg->save();

        if (!$res) {
            throw new DbErrorException("插入商品图片表失败");
        }

        $goodsImg->refresh();
        return $goodsImg;
    }

    /**
     * 插入关联商品和店铺
     *
     * @param GoodsModel $goods
     * @param int $storeId 店铺ID
     * @return GoodsStoreModel
     * @throws DbErrorException
     */
    public function addGoodsStore(GoodsModel $goods, int $storeId): GoodsStoreModel
    {
        $goodsStore = new GoodsStoreModel();
        $goodsStore->goods_id = $goods->id;
        $goodsStore->store_id = $storeId;
        $res = $goodsStore->save();

        if (!$res) {
            throw new DbErrorException("插入商品和店铺关联表失败");
        }

        $goodsStore->refresh();
        return $goodsStore;
    }

    /**
     * 添加商品标签
     *
     * @param AdminModel $admin
     * @param GoodsModel $goods
     * @param $tag
     * @return GoodsTagModel
     * @throws DbErrorException
     * @throws Exception
     */
    public function addGoodsTag(AdminModel $admin, GoodsModel $goods, $tag)
    {
        $goodsTag = new GoodsTagModel();
        $goodsTag->goods_id = $goods->id;
        $goodsTag->tag_name = $tag;
        $goodsTag->hq_id = $admin->last_hq_id;

        $res = $goodsTag->save();

        if (!$res) {
            throw new DbErrorException("插入商品标签表失败");
        }

        return $goodsTag;
    }

    /**
     * 根据标签名查询标签
     *
     * @param string $tag 标签名
     * @return TagModel
     * @throws NotFound
     */
    public function getTagByName(string $tag): TagModel
    {
        $info = TagModel::withoutTrashed()
            ->where("tag_name", $tag)
            ->where("type", TagModel::TYPE_GOODS)
            ->first();

        if (empty($info)) {
            throw new NotFound("tag没有找到");
        }

        return $info;
    }

    /**
     * 查询店铺商品列表
     *
     * @param array $data 查询条件
     * @return Collection|GoodsModel[]
     */
    public function list(array $data)
    {
        $storeId = intval($data['store_id']);
        //类型，1=库存，2=售罄
        $type = intval($data["type"] ?? 1);
        $page = intval($data["page"] ?? 1);
        $pageSize = intval($data['page_size'] ?? 10);
        //搜索关键字
        $name = $data['search'] ?? "";
        //未售出天数
        $unsoldDay = intval($data['unsold_day'] ?? 0);
        //最小价格
        $minPrice = intval($data['min_price'] ?? 0);
        //最高价格
        $maxPrice = intval($data['max_price'] ?? 0);
        //标签名字数组，用逗号隔开
        $tagNames = $data['tag_names'] ?? "";

        $store = StoreModel::whereId($storeId)->first();

        $query = $store->goodsList()->select(['goods.*']);

        if ($name != "") {
            $query->where("goods.goods_name", "like", $name . "%");
        }

        if ($type == 2) {
            $query->where("goods.stock_num", "<=", 0);
        } else {
            $query->where("goods.stock_num", ">", 0);
        }

        if ($minPrice) {
            $query->where('goods.goods_price', ">=", $minPrice);
        }

        if ($maxPrice) {
            $query->where('goods.goods_price', "<=", $maxPrice);
        }

        if ($unsoldDay) {
            $unsoldDay = time() - $unsoldDay * 24 * 60 * 60;
            $query->where('goods.created_at', "<=", date("Y-m-d H:i:s", $unsoldDay));
        }

        if (!empty($tagNames)) {
            $tagNames = explode(",", $tagNames);
            $query->join("goods_tag", "goods_tag.goods_id", "=", "goods.id");
            $query->whereIn("goods_tag.tag_name", $tagNames);
        }

        $start = ($page - 1) * $pageSize;
        return $query->orderByDesc("goods.created_at")
            ->offset($start)
            ->limit($pageSize)
            ->with(['category', "images", "tags", "attrList"])
            ->get();
    }

    /**
     * 添加商品规格
     *
     * @param int $goodsId 商品ID
     * @param array $attrColor 商品规格信息
     * @return GoodsAttributeModel
     * @throws DbErrorException
     */
    public function addGoodsAttr(int $goodsId, array $attrColor)
    {
        $goodsAttr = new GoodsAttributeModel();
        $goodsAttr->goods_id = $goodsId;
        $goodsAttr->attr_id = $attrColor['attr_id'] ?? 0;
        $goodsAttr->color_id = $attrColor['color_id'] ?? 0;
        $goodsAttr->stock_num = $attrColor['stock'];
        $goodsAttr->goods_price = Helper::convertPrice($attrColor['price']);
        $res = $goodsAttr->save();

        if ($res === false) {
            throw new DbErrorException("插入商品规格数据失败");
        }

        $goodsAttr->refresh();
        $goodsAttr->makeBarCode();//生成商品唯一码
        return $goodsAttr;
    }

    /**
     * 删除商品
     *
     * @param int $hqId
     * @param array $goodsIds
     * @return bool|mixed|null
     * @throws Exception
     */
    public function deleteByIds(int $hqId, array $goodsIds)
    {
        return GoodsModel::whereHqId($hqId)
            ->whereIn("id", $goodsIds)
            ->delete();
    }

    /**
     * 批量售罄
     *
     * @param int $hqId
     * @param array $goodsIds
     * @return bool
     * @throws DbErrorException
     */
    public function soldOut(int $hqId, array $goodsIds)
    {
        $goodsIds = GoodsModel::whereHqId($hqId)
            ->whereIn("id", $goodsIds)
            ->pluck('id')
            ->toArray();

        DB::beginTransaction();
        $res = GoodsAttributeModel::query()
            ->whereIn("goods_id", $goodsIds)
            ->update(['stock_num' => 0]);

        if ($res === false) {
            DB::rollBack();
            throw new DbErrorException("操作失败");
        }

        $res = GoodsModel::query()
            ->whereIn("id", $goodsIds)
            ->update(['stock_num' => 0]);

        if ($res === false) {
            DB::rollBack();
            throw new DbErrorException("操作失败");
        }

        DB::commit();

        return true;
    }

    /**
     * 根据ID获取详情
     *
     * @param int $storeId 店铺ID
     * @param int $goodsId 商品ID
     * @return GoodsModel
     * @throws NotFound
     */
    public function getInfoById(int $storeId, int $goodsId)
    {
        $store = StoreModel::whereId($storeId)->first();

        $info = $store->goodsList()->where("goods.id", $goodsId)->first();

        if (empty($info)) {
            throw new NotFound("商品不存在");
        }

        return $info;
    }

    /**
     * 商品编辑
     *
     * @param int $hqId HQ ID
     * @param int $goodsId 商品ID
     * @param array $data 商品数据
     * @return GoodsModel
     * @throws NotFound|DbErrorException
     */
    public function goodsEdit(int $hqId, int $goodsId, array $data)
    {
        $goods = GoodsModel::withoutTrashed()->where("id", $goodsId)->first();

        if (empty($goods) || $goods->hq_id != $hqId) {
            throw new NotFound("商品不存在");
        }

        /** @var CategoryModel $cat */
        $cat = $data['cat'];

        $goods->goods_name = $data['name'];
        $goods->cat_id = $cat->id;
        $goods->purchase_price = Helper::convertPrice($data['purchase_price']);
        $goods->stock_num = (int)$data['stock'];
        $goods->cover_img = $data['master_map'];
        $goods->goods_detail = $data['detail'];
        $goods->min_price = Helper::convertPrice($data['min_price']);
        $goods->max_price = Helper::convertPrice($data['max_price']);
        $goods->goods_detail = $data['detail'];
        $res = $goods->save();

        if (!$res) {
            throw new DbErrorException("插入商品表失败");
        }

        $goods->refresh();
        return $goods;
    }

    /**
     * 清空商品规格
     *
     * @param int $goodsId 商品ID
     * @return bool
     * @throws DbErrorException
     */
    public function clearGoodsAttr(int $goodsId): bool
    {
        $res = GoodsAttributeModel::withoutTrashed()->where("goods_id", $goodsId)->delete();

        if ($res === false) {
            throw new DbErrorException("清除商品规格失败");
        }

        return true;
    }

    /**
     * 根据商品图片URL获取图片详情
     * @param int $goodsId 商品ID
     * @param string $url 图片URL
     * @return GoodsImgModel
     * @throws NotFound
     */
    public function getImageByUrl(int $goodsId, string $url)
    {
        $info = GoodsImgModel::whereGoodsId($goodsId)
            ->where("img_url", $url)
            ->first();

        if (empty($res)) {
            throw new NotFound("没有找到商品图片");
        }

        return $info;
    }

    /**
     * 清空商品图片
     * @param int $goodsId 商品ID
     * @return bool
     * @throws DbErrorException
     */
    public function clearGoodsImages(int $goodsId): bool
    {
        $res = GoodsImgModel::whereGoodsId($goodsId)->delete();

        if ($res === false) {
            throw new DbErrorException("清空商品图片失败");
        }

        return true;
    }

    /**
     * 根据商品ID和标签名字获取记录
     *
     * @param int $goodsId 商品ID
     * @param string $tagName 标签名字
     * @return GoodsTagModel
     * @throws NotFound
     */
    public function getGoodsTagByName(int $goodsId, $tagName)
    {
        $info = GoodsTagModel::withoutTrashed()
            ->where("goods_id", $goodsId)
            ->where("tag_name", $tagName)
            ->first();

        if (empty($info)) {
            throw new NotFound("没有找到商品标签");
        }

        return $info;
    }

    /**
     * 清空商品多余的标签
     * @param int $goodsId 商品ID
     * @param array $exTags 排除的tag name
     * @return bool
     * @throws Exception
     */
    public function clearGoodsTag(int $goodsId, array $exTags): bool
    {
        $res = GoodsTagModel::withoutTrashed()
            ->where("goods_id", $goodsId)
            ->whereNotIn("tag_name", $exTags)
            ->delete();

        if ($res === false) {
            throw new DbErrorException("删除商品标签错误");
        }

        return true;
    }
}
