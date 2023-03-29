<?php

namespace App\Http\Requests\V1\Admin\Account;

use App\Http\Requests\V1\BaseRequest;

class ForgetPassRequest extends BaseRequest
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
            'sms_code' => 'required|size:6',
            'password' => 'required|min:8'
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
            "sms_code.required" => __("user.sms_code_null"),
            "sms_code.size" => __("user.sms_code_error"),
            "password.required" => "请输入密码",
            "password.min" => "密码最少8位"
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
