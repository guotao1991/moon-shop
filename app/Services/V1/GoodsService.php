<?php

namespace App\Services\V1;

use App\Exceptions\DbErrorException;
use App\Models\Goods\GoodsModel;
use App\Repositories\V1\GoodsRepository;
use App\Utils\Helper;
use Exception;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Support\Facades\DB;

/**
 * Class GoodsService
 * @package App\Services\V1
 *
 */
class GoodsService extends BaseService
{
    protected $goodsRepo;

    /**
     * GoodsService constructor.
     * @param GoodsRepository $goodsRepo
     */
    public function __construct(GoodsRepository $goodsRepo)
    {
        $this->goodsRepo = $goodsRepo;
    }

    /**
     * 添加商品
     *
     * @param array $data
     * @return GoodsModel
     * @throws Exception
     */
    public function addGoods(array $data): GoodsModel
    {
        $data['goods_imgs'] = json_decode($data['goods_imgs'], 1) ?? [];

        $data['tags'] = empty($data['tags']) ? [] : json_decode($data['tags'], 1);

        $admin = Helper::admin();
        $data['hq_id'] = $admin->last_hq_id;

        DB::beginTransaction();
        $goods = $this->goodsRepo->addGoods($data);

        if (!empty($data['attr_color_list'])) {
            $goodsStock = 0;
            $prices = [];
            foreach ($data['attr_color_list'] as $attrColor) {
                $this->goodsRepo->addGoodsAttr($goods->id, $attrColor);
                $goodsStock += $attrColor['stock'];
            }

            $goods->stock_num = $goodsStock;
            $goods->min_price = Helper::convertPrice(min($prices));
            $goods->max_price = Helper::convertPrice(max($prices));

            $goods->save();
        }

        //关联商品和店铺
        $this->goodsRepo->addGoodsStore($goods, $admin->last_store_id);

        //关联商品和图片
        foreach ($data['goods_imgs'] as $img) {
            $this->goodsRepo->addGoodsImg($goods, $img['url']);
        }

        //关联商品和标签
        foreach ($data['tags'] as $tag) {
            $this->goodsRepo->addGoodsTag($admin, $goods, $tag['tag_name']);
        }
        DB::commit();

        return $goods;
    }

    /**
     * 查询商品列表
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function list(array $data)
    {
        $admin = Helper::admin();
        $data['store_id'] = $admin->last_store_id;
        return $this->goodsRepo->list($data);
    }

    /**
     * 根据商品ID删除商品
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function deleteByIds(array $data)
    {
        $ids = explode(",", $data["ids"]);

        if (count($ids) > 0) {
            $admin = Helper::admin();
            $res = $this->goodsRepo->deleteByIds($admin->last_hq_id, $ids);

            if ($res === false) {
                throw new DbErrorException("删除失败");
            }
        }

        return true;
    }

    /**
     * 批量售罄
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function soldOut(array $data)
    {
        $ids = explode(",", $data["ids"]);

        if (count($ids) > 0) {
            $admin = Helper::admin();

            $res = $this->goodsRepo->soldOut($admin->last_hq_id, $ids);

            if ($res === false) {
                throw new DbErrorException("操作失败");
            }
        }

        return true;
    }

    /**
     * 商品详情接口
     * @param array $data
     * @return GoodsModel
     * @throws NotFound
     * @throws Exception
     */
    public function info(array $data)
    {
        $goodsId = (int)$data['goods_id'];
        $admin = Helper::admin();
        return $this->goodsRepo->getInfoById($admin->last_store_id, $goodsId);
    }

    /**
     * 编辑商品
     * @param array $data
     * @return GoodsModel
     * @throws Exception
     */
    public function goodsEdit(array $data)
    {
        $goodsId = (int)$data['goods_id'];

        $data['goods_imgs'] = json_decode($data['goods_imgs'], 1) ?? [];

        $data['tags'] = empty($data['tags']) ? [] : json_decode($data['tags'], 1);

        $admin = Helper::admin();
        DB::beginTransaction();
        $stockNum = $data['stock'];
        $minPrice = $maxPrice = $data['price'];
        $this->goodsRepo->clearGoodsAttr($goodsId);
        if (!empty($data['attr_color_list'])) {
            $stockNum = 0;
            $prices = [];
            foreach ($data['attr_color_list'] as $attrColor) {
                $this->goodsRepo->addGoodsAttr($goodsId, $attrColor);
                $stockNum += $attrColor['stock'];
                $prices[] = $attrColor['price'];
            }

            $minPrice = min($prices);
            $maxPrice = max($prices);
        }

        $data['min_price'] = $minPrice;
        $data['max_price'] = $maxPrice;

        $data['stock'] = $stockNum;

        $goods = $this->goodsRepo->goodsEdit($admin->last_hq_id, $goodsId, $data);

        //关联商品和图片
        $this->goodsRepo->clearGoodsImages($goods->id);
        foreach ($data['goods_imgs'] as $img) {
            $this->goodsRepo->addGoodsImg($goods, $img['url']);
        }

        //关联商品和标签
        $tags = [];
        foreach ($data['tags'] as $tag) {
            $tags[] = $tag['tag_name'];
            try {
                $this->goodsRepo->getGoodsTagByName($goodsId, $tag['tag_name']);
            } catch (NotFound $e) {
                $this->goodsRepo->addGoodsTag($admin, $goods, $tag['tag_name']);
            }
        }

        //清空多余的tag
        $this->goodsRepo->clearGoodsTag($goods->id, $tags);
        DB::commit();

        return $goods;
    }

    /**
     * @param array $data
     * @return bool
     * @throws DbErrorException
     * @throws Exception
     */
    public function editTag(array $data)
    {
        $goodsId = (int)$data['goods_id'];
        $admin = Helper::admin();
        $data['tags'] = json_decode($data['tags'] ?? "[]", 1);

        $goods = $this->goodsRepo->getInfoById($admin->last_store_id, $goodsId);
        //关联商品和标签
        $tags = [];
        DB::beginTransaction();
        foreach ($data['tags'] as $tag) {
            $tags[] = $tag['tag_name'];
            try {
                $this->goodsRepo->getGoodsTagByName($goodsId, $tag['tag_name']);
            } catch (NotFound $e) {
                $this->goodsRepo->addGoodsTag($admin, $goods, $tag['tag_name']);
            }
        }

        //清空多余的tag
        $this->goodsRepo->clearGoodsTag($goods->id, $tags);
        DB::commit();

        return true;
    }
}
