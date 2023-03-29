<?php

namespace App\Http\Requests\V1\Goods\Index;

use App\Http\Requests\V1\BaseRequest;

class ListRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => 'required|between:1,2',
            'page' => "required|integer",
            'page_size' => "sometimes",
            'search' => 'sometimes',
            'min_price' => 'sometimes',
            'max_price' => 'sometimes',
            'unsold_day' => 'sometimes',
            'tag_names' => 'sometimes'
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            "type.between" => "数据错误",
            "page.required" => "数据错误",
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
