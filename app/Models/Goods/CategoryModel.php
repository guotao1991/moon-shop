<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Goods;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CategoryModel
 *
 * @property int $id
 * @property string $name
 * @property int $hq_id
 * @property int $parent_id
 * @property string $code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property CategoryAttributeModel[] $categoryAttribute
 *
 * @package App\Models\Goods
 */
class CategoryModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'category';

    protected $casts = [
        'hq_id' => 'int',
        'parent_id' => 'int'
    ];

    protected $fillable = [
        'name',
        'hq_id',
        'parent_id',
        'code'
    ];

    /**
     * 关联属性模型
     * @return HasMany
     */
    public function categoryAttribute()
    {
        return $this->hasMany(CategoryAttributeModel::class, "category_id", "id");
    }
}
