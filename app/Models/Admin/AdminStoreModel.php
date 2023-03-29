<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Admin;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminStoreModel
 *
 * @property int $id
 * @property int $admin_id
 * @property int $store_id
 * @property int $role_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|AdminStoreModel whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminStoreModel whereStoreId($value)
 *
 * @package App\Models\Admin
 */
class AdminStoreModel extends BaseModel
{
    protected $table = 'admin_store';

    protected $casts = [
        'admin_id' => 'int',
        'store_id' => 'int',
        'role_id' => 'int'
    ];

    protected $fillable = [
        'admin_id',
        'store_id',
        'role_id'
    ];
}
