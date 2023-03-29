<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TblDict
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $group
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @package App\Http\Models
 */
class DictModel extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'dict';

    protected $fillable = [
        'key',
        'value',
        'group'
    ];
}
