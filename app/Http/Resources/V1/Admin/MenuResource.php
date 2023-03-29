<?php

namespace App\Http\Resources\V1\Admin;

use App\Http\Resources\V1\BaseResources;
use Illuminate\Http\Request;

/**
 * Class MenuResource
 * @package App\Http\Resources\V1\Admin
 *
 * @property int $id
 * @property string $name
 * @property string|null $explain
 * @property string|null $icon
 *
 */
class MenuResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            "menu_id" => $this->id,
            "menu_name" => $this->name,
            "explain" => $this->explain,
            "icon" => $this->icon
        ];
    }
}
