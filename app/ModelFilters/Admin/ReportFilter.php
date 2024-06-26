<?php

namespace App\ModelFilters\Admin;

use EloquentFilter\ModelFilter;
use Illuminate\Support\Facades\Auth;

class ReportFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function reportableType($reportableType)
    {
        return $this->where('reportable_type', $reportableType);
    }
}
