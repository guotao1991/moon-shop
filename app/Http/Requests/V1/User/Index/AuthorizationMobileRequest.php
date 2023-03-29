<?php

namespace App\Http\Requests\V1\User\Index;

use App\Http\Requests\V1\BaseRequest;

class AuthorizationMobileRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'encryptedData' => 'required',
            'iv' => 'required',
            'code' => 'required',
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            "encryptedData.required" => "数据错误",
            "iv.required" => "数据错误",
            "code.required" => "数据错误",
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
