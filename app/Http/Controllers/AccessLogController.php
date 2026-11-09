<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccessLogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'result' => ['nullable', 'in:granted,denied'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $query = AccessLog::query()
            ->leftJoin('operators', 'access_logs.operator_id', '=', 'operators.operator_id')
            ->leftJoin('devices', 'access_logs.device_id', '=', 'devices.device_id')
            ->select([
                'access_logs.*',
                'operators.operator_name as directory_operator_name',
                'devices.device_name',
            ]);

        if (!empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function ($builder) use ($search) {
                $builder->where('access_logs.operator_name', 'like', '%' . $search . '%')
                    ->orWhere('operators.operator_name', 'like', '%' . $search . '%')
                    ->orWhere('access_logs.operator_id', 'like', '%' . $search . '%')
                    ->orWhere('access_logs.device_id', 'like', '%' . $search . '%')
                    ->orWhere('devices.device_name', 'like', '%' . $search . '%')
                    ->orWhere('access_logs.event_id', 'like', '%' . $search . '%')
                    ->orWhere('access_logs.mqtt_message_id', 'like', '%' . $search . '%')
                    ->orWhere('access_logs.reason', 'like', '%' . $search . '%');
            });
        }

        if (!empty($filters['result'])) {
            $query->where('access_logs.result', $filters['result']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('access_logs.event_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('access_logs.event_at', '<=', $filters['end_date']);
        }

        $logs = $query
            ->orderByDesc('access_logs.event_at')
            ->orderByDesc('access_logs.id')
            ->paginate(25)
            ->withQueryString();

        $today = now()->toDateString();
        $todayLogs = AccessLog::query()->whereDate('event_at', $today)->count();
        $todayGranted = AccessLog::query()->whereDate('event_at', $today)->where('result', 'granted')->count();
        $todayDenied = AccessLog::query()->whereDate('event_at', $today)->where('result', 'denied')->count();
        $totalLogs = AccessLog::query()->count();

        return view('access-logs.index', compact(
            'logs', 'filters', 'todayLogs', 'todayGranted', 'todayDenied', 'totalLogs'
        ));
    }

    public function show(int $logId): View
    {
        $log = AccessLog::query()
            ->leftJoin('operators', 'access_logs.operator_id', '=', 'operators.operator_id')
            ->leftJoin('devices', 'access_logs.device_id', '=', 'devices.device_id')
            ->select([
                'access_logs.*',
                'operators.operator_name as directory_operator_name',
                'devices.device_name',
            ])
            ->where('access_logs.id', $logId)
            ->firstOrFail();

        return view('access-logs.show', compact('log'));
    }
}
