<?php

namespace App\Http\Requests\V1\Store\Index;

use App\Http\Requests\V1\BaseRequest;

class UpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'store_id' => 'required',
            'store_name' => 'required',
            'contact_name' => 'required',
            'mobile' => 'required|regex:/^1[3-9]\d{9}$/',
            'province_id' => 'required|int',
            'city_id' => 'required|int',
            'district_id' => 'required|int',
            'address' => 'required',
            'head_img' => 'string',
            'lgt' => 'string',
            'lat' => 'string',
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            'store_id.required' => "店铺没有找到",
            'store_name.required' => __("store.store_name_null"),
            'contact_name.required' => __("store.contact_name_is_null"),
            'mobile.required' => __("store.mobile_is_null"),
            'mobile.regex' => "手机号码错误",
            'province_id.required' => __("store.address_id_null"),
            'province_id.int' => __("store.address_id_null"),
            'city_id.required' => __("store.address_id_null"),
            'city_id.int' => __("store.address_id_null"),
            'district_id.required' => __("store.address_id_null"),
            'district_id.int' => __("store.address_id_null"),
            'address.required' => __("store.address_null"),
            'head_img.string' => "请选择店铺图片",
            'lgt.string' => "经度不能为空",
            'lat.string' => "纬度不能为空",
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
