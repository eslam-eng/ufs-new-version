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


    public function listing(array $filters = [], array $withRelations = [],int $paginateLength = 10): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->queryGet(filters: $filters, withRelations: $withRelations)->paginate();
    }

    public function queryGet(array $filters = [], array $withRelations = []): Builder
    {
        $awbs= $this->model->query()->orderByDesc('id')->with($withRelations);
        return $awbs->filter(new AwbsFilter($filters));
    }

}
