<?php

namespace App\Http\Requests\V1\Goods\Index;

use App\Http\Requests\V1\BaseRequest;

class SoldOutRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ids' => 'required',
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            "ids.required" => "数据错误",
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
