<?php

namespace App\Http\Requests\V1\User\Index;

use App\Http\Requests\V1\BaseRequest;

class EditRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mobile' => 'sometimes',
            'nick_name' => 'sometimes',
            'sex' => 'sometimes',
            'birthday' => 'sometimes',
            'head_img' => 'sometimes'
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
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
