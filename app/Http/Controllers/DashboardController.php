<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\AccessSession;
use App\Models\Device;
use App\Models\MqttMessage;
use App\Models\Operator;
use App\Services\SessionInterruptionDetector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = now();
        $today = $now->copy()->startOfDay();

        $heartbeatCutoff = now('UTC')->subSeconds(60)->toDateTimeString();
        $devices = Device::query()->orderBy('device_name')->get();

        $devices->each(function (Device $device) use ($heartbeatCutoff) {
            $device->setAttribute(
                'status',
                $device->last_seen !== null && $device->last_seen >= $heartbeatCutoff
                    ? 'online'
                    : 'offline'
            );
        });

        $onlineDevices = $devices->where('status', 'online')->count();
        $offlineDevices = $devices->where('status', 'offline')->count();

        $totalOperators = Operator::query()->count();
        $activeOperators = Operator::query()->where('status', 'active')->count();
        $inactiveOperators = Operator::query()->where('status', 'inactive')->count();

        $totalLogs = AccessLog::query()->count();
        $grantedLogs = AccessLog::query()->where('result', 'granted')->count();
        $deniedLogs = AccessLog::query()->where('result', 'denied')->count();

        $todayLogs = AccessLog::query()
            ->where('event_at', '>=', $today->toDateTimeString())
            ->count();
        $todayGranted = AccessLog::query()
            ->where('event_at', '>=', $today->toDateTimeString())
            ->where('result', 'granted')
            ->count();
        $todayDenied = AccessLog::query()
            ->where('event_at', '>=', $today->toDateTimeString())
            ->where('result', 'denied')
            ->count();

        $interruptedIds = app(SessionInterruptionDetector::class)->detect();

        $orphanedSessionIds = DB::connection('atomic_access')
            ->table('access_sessions as sessions')
            ->leftJoin('operators', 'sessions.operator_db_id', '=', 'operators.operator_db_id')
            ->where('sessions.status', 'active')
            ->where(function ($query) {
                $query->whereNull('operators.operator_db_id')
                    ->orWhere('operators.status', '!=', 'active');
            })
            ->pluck('sessions.session_id')
            ->map(static fn ($id) => (string) $id)
            ->all();

        $activeSessions = DB::connection('atomic_access')
            ->table('access_sessions as sessions')
            ->join('operators', 'sessions.operator_db_id', '=', 'operators.operator_db_id')
            ->where('sessions.status', 'active')
            ->where('operators.status', 'active')
            ->whereNotIn('sessions.session_id', $interruptedIds)
            ->count();

        $orphanedSessions = count($orphanedSessionIds);

        $interruptedSessions = DB::connection('atomic_access')
            ->table('access_sessions')
            ->whereIn('session_id', $interruptedIds)
            ->whereNotIn('session_id', $orphanedSessionIds)
            ->where('status', '!=', 'completed')
            ->count();

        $completedSessions = AccessSession::query()->where('status', 'completed')->count();
        $totalSessions = AccessSession::query()->count();

        $averageDuration = AccessSession::query()
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds');
        $averageDuration = $averageDuration !== null ? (int) round($averageDuration) : null;

        if ($averageDuration !== null) {
            $days = intdiv($averageDuration, 86400);
            $hours = intdiv($averageDuration % 86400, 3600);
            $minutes = intdiv($averageDuration % 3600, 60);
            $seconds = $averageDuration % 60;

            $averageDurationFormatted =
                ($days > 0 ? $days . 'd ' : '') .
                sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        } else {
            $averageDurationFormatted = '—';
        }

        $recentLogs = AccessLog::query()
            ->orderByDesc('event_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $recentMessages = MqttMessage::query()
            ->select(['id', 'device_id', 'topic', 'received_at', 'processing_status'])
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $processedMessages = MqttMessage::query()
            ->where('processing_status', 'processed')
            ->count();
        $totalMessages = MqttMessage::query()->count();

        $startDate = $now->copy()->subDays(6)->startOfDay();
        $activityRows = DB::connection('atomic_access')
            ->table('access_logs')
            ->selectRaw('DATE(event_at) as activity_date')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN result = 'granted' THEN 1 ELSE 0 END) as granted")
            ->selectRaw("SUM(CASE WHEN result = 'denied' THEN 1 ELSE 0 END) as denied")
            ->where('event_at', '>=', $startDate->toDateTimeString())
            ->groupByRaw('DATE(event_at)')
            ->get()
            ->keyBy('activity_date');

        $activity = collect(range(6, 0))->map(function (int $daysAgo) use ($now, $activityRows) {
            $date = $now->copy()->subDays($daysAgo)->startOfDay();
            $row = $activityRows->get($date->toDateString());

            return [
                'label' => $date->translatedFormat('D'),
                'date' => $date->format('d M'),
                'total' => (int) ($row->total ?? 0),
                'granted' => (int) ($row->granted ?? 0),
                'denied' => (int) ($row->denied ?? 0),
            ];
        });

        $maxActivity = max(1, (int) $activity->max('total'));

        return view('dashboard.index', compact(
            'devices',
            'onlineDevices',
            'offlineDevices',
            'totalOperators',
            'activeOperators',
            'inactiveOperators',
            'totalLogs',
            'grantedLogs',
            'deniedLogs',
            'todayLogs',
            'todayGranted',
            'todayDenied',
            'totalSessions',
            'activeSessions',
            'orphanedSessions',
            'interruptedSessions',
            'completedSessions',
            'averageDuration',
            'averageDurationFormatted',
            'recentLogs',
            'recentMessages',
            'processedMessages',
            'totalMessages',
            'activity',
            'maxActivity'
        ));
    }
}
