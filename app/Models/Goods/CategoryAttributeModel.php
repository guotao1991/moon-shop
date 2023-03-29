<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Goods;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CategoryAttributeModel
 *
 * @property int $id
 * @property int $category_id
 * @property string $attribute_value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Goods
 */
class CategoryAttributeModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'category_attribute';

    protected $casts = [
        'category_id' => 'int',
    ];

    protected $fillable = [
        'category_id',
        'attribute_value',
    ];
}
