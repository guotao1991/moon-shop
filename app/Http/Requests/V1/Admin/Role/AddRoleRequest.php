<?php

namespace App\Http\Requests\V1\Admin\Role;

use App\Http\Requests\V1\BaseRequest;

class AddRoleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role_name' => 'required|max:64',
            'menu_ids' => 'required|json'
        ];
    }

    /**
     * 错误提示
     * @return array|mixed
     */
    public function messages()
    {
        return [
            'role_name.required' => "请输入角色名",
            'role_name.max' => "角色名过长",
            'menu_ids.required' => "请选择菜单",
            'menu_ids.json' => "数据错误",
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
