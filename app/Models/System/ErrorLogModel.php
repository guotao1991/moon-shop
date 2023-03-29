<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ErrorLogModel
 *
 * @property int $id
 * @property string $code_file
 * @property string|null $error_msg
 * @property string $request_url
 * @property string|null $request_params
 * @property string|null $request_header
 * @property string|null $client_ip
 * @property string $client_ua
 * @property int $is_processed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\System
 */
class ErrorLogModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'error_log';

    protected $casts = [
        'is_processed' => 'int'
    ];

    protected $fillable = [
        'code_file',
        'error_msg',
        'request_url',
        'request_params',
        'request_header',
        'client_ip',
        'client_ua',
        'is_processed'
    ];
}
