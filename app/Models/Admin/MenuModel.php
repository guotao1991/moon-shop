<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Admin;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class MenuModel
 *
 * @property int $id
 * @property string $name
 * @property string|null $explain
 * @property string|null $icon
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Store
 */
class MenuModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'menu';

    protected $fillable = [
        'name',
        'explain',
        'icon'
    ];
}
