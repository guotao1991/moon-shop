<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\User;

use App\Models\BaseModel;
use Carbon\Carbon;

/**
 * Class UserTagModel
 *
 * @property int $id
 * @property string $level_name
 * @property int $consumption_amount
 * @property int $discount
 * @property int $hq_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|UserTagModel whereHqId($value)
 *
 *
 * @package App\Models\User
 */
class LevelModel extends BaseModel
{
    protected $table = 'level';

    protected $casts = [
        'hq_id' => 'int',
        'consumption_amount' => 'int',
        'discount' => 'int',
    ];

    protected $fillable = [
        'level_name',
        'consumption_amount',
        'discount',
        'hq_id'
    ];
}
