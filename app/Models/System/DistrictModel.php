<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models\System;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DistrictModel
 *
 * @property int $id
 * @property int $parent_id
 * @property string $city_code
 * @property string $ad_code
 * @property string $name
 * @property string $lng
 * @property string $lat
 * @property string $level
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 *
 * @property DistrictModel[]|Collection $cityList
 * @property DistrictModel[]|Collection $districtList
 *
 * @package App\Models\System
 */
class DistrictModel extends BaseModel
{
    use SoftDeletes;

    protected $table = 'district';

    protected $casts = [
        'parent_id' => 'int'
    ];

    protected $fillable = [
        'parent_id',
        'city_code',
        'ad_code',
        'name',
        'lng',
        'lat',
        'level'
    ];

    /**
     * 城市列表
     * @return HasMany
     */
    public function cityList()
    {
        return $this->hasMany(DistrictModel::class, "parent_id", "id")->where("level", "=", "city");
    }

    /**
     * 区域列表
     * @return HasMany
     */
    public function districtList()
    {
        return $this->hasMany(DistrictModel::class, "parent_id", "id")->where("level", "=", "district");
    }
}
