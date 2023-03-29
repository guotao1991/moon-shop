<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Admin;

use App\Models\BaseModel;
use Carbon\Carbon;

/**
 * Class RoleMenuModel
 *
 * @property int $id
 * @property int $role_id
 * @property int $menu_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models\Admin
 */
class RoleMenuModel extends BaseModel
{
    protected $table = 'role_menu';

    protected $casts = [
        'role_id' => 'int',
        'menu_id' => 'int'
    ];

    protected $fillable = [
        'role_id',
        'menu_id'
    ];
}
