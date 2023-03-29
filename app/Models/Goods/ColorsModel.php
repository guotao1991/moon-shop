<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Goods;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ColorsModel
 *
 * @property int $id
 * @property string $color_name
 * @property int $hq_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property CategoryAttributeModel[] $categoryAttribute
 *
 * @package App\Models\Goods
 */
class ColorsModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'colors';

    protected $casts = [
        'hq_id' => 'int',
    ];

    protected $fillable = [
        'color_name',
        'hq_id',
    ];
}
