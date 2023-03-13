<?php

namespace App\Services;

use App\Models\Awb;
use App\QueryFilters\AwbsFilter;
use Illuminate\Database\Eloquent\Builder;

class AwbService extends BaseService
{
    public function __construct(public Awb $model)
    {
    }


    public function listing(array $filters = [], array $withRelations = [],int $paginateLength = 10): \Illuminate\Contracts\Pagination\CursorPaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->cursorPaginate(perPage: $paginateLength);
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $awbs= $this->model->query()->orderByDesc('id')->with($withRelations);
        return $awbs->filter(new AwbsFilter($filters));
    }

}
