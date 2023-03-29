<?php

namespace App\Http\Requests\V1\Member\Index;

use App\Http\Requests\V1\BaseRequest;

class EditRemarkRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "user_id" => 'required|integer',
            'remark' => '',
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            'user_id.required' => "用户不存在",
            'user_id.integer' => "用户不存在",
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
