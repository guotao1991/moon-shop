<?php

namespace App\Http\Requests\V1\Common\Index;

use App\Http\Requests\V1\BaseRequest;

class SendSmsCodeRequest extends BaseRequest
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
            'verify_code' => 'required|min:4|captcha_api:' . $this->post('captcha_key'),
            'type' => 'required|integer|between:1,3'
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
            'verify_code.required' => __("user.verify_code_null"),
            'verify_code.min' => __("user.verify_code_error"),
            'verify_code.captcha_api' => __("user.verify_code_error"),
            'type.required' => "发送失败",
            'type.integer' => "发送失败",
            'type.between' => "发送失败",
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
