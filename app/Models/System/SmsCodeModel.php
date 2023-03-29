<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SmsCodeModel
 *
 * @property int $id
 * @property string $mobile
 * @property string $sms_code
 * @property int $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Models\System
 */
class SmsCodeModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'sms_code';

    protected $fillable = [
        'mobile',
        'sms_code'
    ];

    /** @var int 管理员登录验证码 */
    public const TYPE_ADMIN_LOGIN = 1;
    /** @var int 修改密码验证码 */
    public const TYPE_MODIFY_PASS = 2;
    /** @var int 客人登录验证码 */
    public const TYPE_CLIENT_LOGIN = 3;
}
