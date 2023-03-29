<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\Store;

use App\Models\BaseModel;
use App\Models\Common\TagModel;
use App\Models\User\UserTagModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

/**
 * Class HqModel
 *
 * @property int $id
 * @property string $name
 * @property string $contact_name
 * @property string $mobile
 * @property string $business_license_img
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property UserTagModel[] $tags
 *
 * @method static \Illuminate\Database\Eloquent\Builder|HqModel whereId($value)
 *
 * @package App\Models\Store
 */
class HqModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'hq';

    protected $fillable = [
        'name',
        'contact_name',
        'mobile',
        'business_license_img'
    ];

    /**
     * 关联标签模型
     * @return HasMany
     */
    public function tags()
    {
        return $this->hasMany(UserTagModel::class, "hq_id", "id");
    }
}
