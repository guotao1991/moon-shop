<?php

namespace App\Http\Requests\V1\Admin\Account;

use App\Http\Requests\V1\BaseRequest;

class LoginByPassRequest extends BaseRequest
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
            'password' => 'required|min:6',
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
            "password.required" => "密码错误",
            "password.min" => "密码错误",
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
