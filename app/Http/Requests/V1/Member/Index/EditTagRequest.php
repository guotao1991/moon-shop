<?php

namespace App\Http\Requests\V1\Member\Index;

use App\Http\Requests\V1\BaseRequest;

class EditTagRequest extends BaseRequest
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
            'tags' => 'json',
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
