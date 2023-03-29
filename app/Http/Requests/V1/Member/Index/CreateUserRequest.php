<?php

namespace App\Http\Requests\V1\Member\Index;

use App\Http\Requests\V1\BaseRequest;

class CreateUserRequest extends BaseRequest
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
            'nick_name' => 'required|string|min:1|max:32',
            'password' => '',
            'sex' => '',
            'birthday' => '',
            'head_img' => '',
            'wx_openid' => '',
            'tags' => "json",
            'remark' => ''
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            'mobile.required' => __("member.mobile_null"),
            'mobile.regex' => "手机号码错误",
            'nick_name.required' => "请填写用户姓名",
            'nick_name.string' => "请填写用户姓名",
            'nick_name.min' => "请填写用户姓名",
            'nick_name.max' => "用户姓名过长",
            'tags.json' => "标签数据错误",
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
