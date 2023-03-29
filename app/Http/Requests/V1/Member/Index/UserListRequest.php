<?php

namespace App\Http\Requests\V1\Member\Index;

use App\Http\Requests\V1\BaseRequest;

class UserListRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'search' => '',
            'page' => 'integer',
            'page_size' => 'integer',
            "tag_names" => "",
            "sort" => "integer",
            "min_consumption" => "",
            "max_consumption" => "",
            "min_day" => "",
            "max_day" => ""
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            "sort.integer" => "数据错误",
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
