<?php

namespace App\QueryFilters;

use App\Abstracts\QueryFilter;

class AwbsFilter extends QueryFilter
{

    public function __construct($params = array())
    {
        parent::__construct($params);
    }

    public function company_id($term)
    {
        return $this->builder->where('company_id',$term);
    }

    public function branch_id($term)
    {
        return $this->builder->where('branch_id',$term);
    }

    public function code($term)
    {
        return $this->builder->where('code',$term);
    }

    public function receiver_id($term)
    {
        return $this->builder->where('receiver_id',$term);
    }

    public function keyword($term)
    {
        return $this->builder->search($term);
    }

}
