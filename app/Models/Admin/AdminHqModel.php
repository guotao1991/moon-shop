<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Admin;

use App\Models\BaseModel;
use App\Models\Store\HqModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class AdminHqModel
 *
 * @property int $id
 * @property int $admin_id
 * @property int $hq_id
 * @property int $role_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property AdminModel $admin
 * @property HqModel $hq
 * @property RoleModel $role
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AdminHqModel whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminHqModel whereHqId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminHqModel whereRoleId($value)
 *
 * @package App\Models\Admin
 */
class AdminHqModel extends BaseModel
{
    protected $table = 'admin_hq';

    protected $casts = [
        'admin_id' => 'int',
        'hq_id' => 'int',
        'role_id' => 'int'
    ];

    protected $fillable = [
        'admin_id',
        'hq_id',
        'role_id'
    ];

    /**
     * 关联管理员模型
     * @return HasOne
     */
    public function admin()
    {
        return $this->hasOne(AdminModel::class, "id", "admin_id");
    }

    /**
     * 关联HQ模型
     * @return HasOne
     */
    public function hq()
    {
        return $this->hasOne(HqModel::class, "id", "hq_id");
    }

    /**
     * 关联角色模型
     * @return HasOne
     */
    public function role()
    {
        return $this->hasOne(RoleModel::class, "id", "role_id");
    }
}
