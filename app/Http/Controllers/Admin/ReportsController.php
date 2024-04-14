<?php

namespace App\Http\Controllers\Admin;

use App\Events\UserDisabled;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\ModelFilters\Admin\ReportFilter;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $reports = Report::filter($request->all(), ReportFilter::class)->with(['user', 'reportable'])->latest()->paginate($request->page_size ?? 15);

        return ReportResource::collection($reports);
    }

    public function handle(Request $request)
    {
        $reports = DB::transaction(function () use ($request) {
            $reports = Report::query()->whereIn('id', $request->report_ids)->get();
            foreach ($reports as $report) {
                $report->handle_type = $request->handle_type;
                $report->handle_remark = $request->handle_remark;
                $report->handled_at = now();
                $report->save();

                if ($request->handle_type === Report::HANDLE_TYPE_DISABLE_USER) {
                    $report->reportable->status = User::STATUS_DISABLE;
                    $report->reportable->online_status = User::ONLINE_STATUS_LOGOUT;
                    $report->reportable->token()?->revoke();
                    $report->reportable->save();

                    event(new UserDisabled($report->reportable));
                }
            }

            return $reports;
        });

        return ReportResource::collection($reports);
    }
}
