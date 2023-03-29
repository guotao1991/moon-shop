<?php

namespace App\Http\Requests\V1\Goods\Category;

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
            'cat_id' => "required",
            'cat_name' => 'required|max:100',
            'parent_id' => 'required|numeric|min:0'
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            "cat_id.required" => "分类Id不能为空",
            "cat_name.required" => "分类名称不能为空",
            "cat_name.max" => "分类名称最多100个字",
            "parent_id.required" => "父分类不能为空",
            "parent_id.numeric" => "父分类错误",
            "parent_id.min" => "父分类错误",
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
