<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

trait ResourceTrait
{
    protected function toResource($data, $resourceObj)
    {
        if (isset($data)) {
            if ($data instanceof Collection) {
                return $resourceObj::collection($data);
            }
            if ($data instanceof Model) {
                return new $resourceObj($data);
            }
            if ($data instanceof SupportCollection) {
                return collect($data)->map(function ($item) use ($resourceObj) {
                    return new $resourceObj($item);
                });
            }
            if ($data instanceof LengthAwarePaginator || $data instanceof Paginator) {
                return [
                    'list'  => $data->items(),
                    'total' => $data->total()
                ];
            }
        }
        return null;
    }
}
