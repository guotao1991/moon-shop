<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ApiLogModel
 *
 * @property int $id
 * @property string $url
 * @property string|null $param
 * @property int $method
 * @property string|null $header
 * @property string|null $response
 * @property Carbon $request_time
 * @property Carbon|null $response_time
 * @property string $flag
 * @property string $remark
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\System
 */
class ApiLogModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'api_log';

    protected $casts = [
        'method' => 'int',
        'request_time' => 'datetime:Y-m-d H:i:s',
        'response_time' => 'datetime:Y-m-d H:i:s'
    ];

    protected $dates = [
        'request_time',
        'response_time'
    ];

    protected $fillable = [
        'url',
        'param',
        'method',
        'header',
        'response',
        'request_time',
        'response_time',
        'flag',
        'remark'
    ];
}
