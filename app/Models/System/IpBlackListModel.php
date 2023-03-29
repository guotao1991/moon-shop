<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class IpBlackListModel
 *
 * @property int $id
 * @property string $ip_address
 * @property Carbon $end_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\System
 */
class IpBlackListModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'ip_black_list';

    protected $dates = [
        'end_time'
    ];

    protected $fillable = [
        'ip_address',
        'end_time'
    ];
}
