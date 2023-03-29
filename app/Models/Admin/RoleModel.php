<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Admin;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RoleModel
 *
 * @property int $id
 * @property string $role_name
 * @property int $hq_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property MenuModel[] $menus
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RoleModel whereId($value)
 * @package App\Models\Admin
 */
class RoleModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'role';

    protected $casts = [
        'hq_id' => 'int'
    ];

    protected $fillable = [
        'role_name',
        'hq_id'
    ];

    /**
     * 模型关联菜单表
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menus()
    {
        return $this->belongsToMany(MenuModel::class, RoleMenuModel::class, "role_id", "menu_id");
    }
}
