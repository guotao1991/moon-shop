<?php

namespace App\Http\Requests\V1\Goods\Index;

use App\Http\Requests\V1\BaseRequest;

class EditRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'goods_id' => 'required',
            'master_map' => 'required',
            'name' => 'required|max:100',
            'cat' => 'required',
            'price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'goods_imgs' => 'sometimes',
            'detail' => 'sometimes',
            'tags' => 'sometimes',
            'attr_color_list' => 'sometimes',
            'colors' => 'sometimes',
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            "goods_id.required" => "数据错误",
            "goods_name.required" => "商品名称不能为空",
            "master_map.required" => "商品主图不能为空",
            "goods_name.max" => "商品名称最多100个字",
            "cat.required" => "请选择商品分类",
            "price.required" => "商品价格不能为空",
            "price.numeric" => "数据错误",
            "price.min" => "数据错误",
            "purchase_price.required" => "商品价格不能为空",
            "purchase_price.numeric" => "数据错误",
            "purchase_price.min" => "数据错误",
            "stock.required" => "商品库存不能为空",
            "stock.min" => "数据错误",
        ];
    }


    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
