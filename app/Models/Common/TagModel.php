<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Common;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TagModel
 *
 * @property int $id
 * @property int $type
 * @property string $tag_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\Common
 */
class TagModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'tag';

    protected $casts = [
        'type' => 'int',
    ];

    protected $fillable = [
        'type',
        'tag_name'
    ];

    /** @var int 用户标签 */
    public const TYPE_USER = 1;
    /** @var int 商品标签 */
    public const TYPE_GOODS = 2;
}
