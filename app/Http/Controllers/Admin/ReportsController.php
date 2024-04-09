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
        $reports = Report::filter($request->all(), ReportFilter::class)->with(['user', 'reportable'])->latest()->paginate($request->page_size ?? 15);

        return ReportResource::collection($reports);
    }

    public function handle(Request $request)
    {
        $reports = Report::query()->whereIn('id', $request->report_ids)->get();
        foreach ($reports as $report) {
            $report->handle_remark = $request->handle_remark;
            $report->handled_at = now();
            $report->save();
        }

        return ReportResource::collection($reports);
    }
}
