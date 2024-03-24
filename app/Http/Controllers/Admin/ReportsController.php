<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\ModelFilters\Admin\ReportFilter;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $users = Report::filter($request->all(), ReportFilter::class)->with(['user', 'reportable'])->latest()->paginate($request->page_size ?? 15);

        return ReportResource::collection($users);
    }
}
