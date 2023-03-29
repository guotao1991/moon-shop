<?php

namespace App\Http\Resources\V1\Admin;

use App\Http\Resources\V1\BaseResources;
use App\Models\Admin\MenuModel;
use Illuminate\Http\Request;

/**
 * Class RoleResource
 * @package App\Http\Resources\V1\Admin
 *
 *
 * @property int $id
 * @property string $role_name
 * @property MenuModel[] $menus
 */
class RoleResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "role_id" => $this->id,
            "rolle_name" => $this->role_name,
            "menus" => MenuResource::collection($this->menus)
        ];
    }
}
