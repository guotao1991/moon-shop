<?php

namespace App\Http\Requests\V1\Admin\Account;

use App\Http\Requests\V1\BaseRequest;

class AddManagerRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mobile' => 'required|regex:/^1[3-9]\d{9}$/',
            'role_id' => 'required|int',
            'store_ids' => 'required|json',
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            'mobile.required' => __("user.mobile_null"),
            'mobile.regex' => __("user.mobile_error"),
            "role_id.required" => "请选择角色",
            "role_id.int" => "请选择角色",
            "store_ids.required" => "请选择店铺",
            "store_ids.json" => "请选择店铺",
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
