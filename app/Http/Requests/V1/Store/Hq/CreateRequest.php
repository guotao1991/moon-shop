<?php

namespace App\Http\Requests\V1\Store\Hq;

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
            'hq_name' => 'required|between:1,32',
            'contact_name' => 'required|between:1,32',
            'mobile' => 'required|regex:/^1[3-9]\d{9}$/',
            'business_license_img' => 'required|url'
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            'hq_name.required' => "组织名不能为空",
            'contact_name.required' => "联系人不能为空",
            'contact_name.between' => "联系人信息填写有误",
            'mobile.required' => "手机号不能为空",
            'mobile.regex' => "手机号错误",
            'business_license_img.required' => "请上传营业执照",
            'business_license_img.url' => "请上传营业执照",
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
