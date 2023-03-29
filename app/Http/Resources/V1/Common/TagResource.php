<?php

namespace App\Http\Resources\V1\Common;

use App\Http\Resources\V1\BaseResources;
use App\Models\Common\TagModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class TagResource
 * @package App\Http\Resources\V1\Common
 *
 * @property string $tag_name
 */
class TagResource extends BaseResources
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'tag_name' => $this->tag_name,
        ];
    }
}
