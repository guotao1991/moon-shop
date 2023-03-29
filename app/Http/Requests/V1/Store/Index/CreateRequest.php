<?php

namespace App\Http\Requests\V1\Store\Index;

use App\Http\Requests\V1\BaseRequest;

class CreateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'province_id' => 'required|int',
            'city_id' => 'required|int',
            'district_id' => 'required|int',
            'address' => 'required',
            'store_image' => 'sometimes',
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            'name.required' => __("store.store_name_null"),
            'province_id.required' => __("store.address_id_null"),
            'province_id.int' => __("store.address_id_null"),
            'city_id.required' => __("store.address_id_null"),
            'city_id.int' => __("store.address_id_null"),
            'district_id.required' => __("store.address_id_null"),
            'district_id.int' => __("store.address_id_null"),
            'address.required' => __("store.address_null"),
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
