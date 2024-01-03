<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

trait ResourceTrait
{
    protected function toResource($data, $resourceClass)
    {
        if (isset($data)) {
            if ($data instanceof Collection) {
                return $resourceClass::collection($data);
            }
            if ($data instanceof Model) {
                return new $resourceClass($data);
            }
            if ($data instanceof SupportCollection) {
                return collect($data)->map(function ($item) use ($resourceClass) {
                    return new $resourceClass($item);
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
