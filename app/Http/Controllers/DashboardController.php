<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\AccessSession;
use App\Models\Device;
use App\Models\MqttMessage;
use App\Models\Operator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = now();
        $today = $now->copy()->startOfDay();

        /*
        |--------------------------------------------------------------------------
        | DEVICES
        |--------------------------------------------------------------------------
        */

        $heartbeatCutoff = $now->copy()
            ->subSeconds(60)
            ->toDateTimeString();

        $devices = Device::query()
            ->orderBy('device_name')
            ->get();

        // Derive connectivity from the latest heartbeat, not the persisted status.
        $devices->each(function (Device $device) use ($heartbeatCutoff) {
            $device->setAttribute(
                'status',
                $device->last_seen !== null && $device->last_seen >= $heartbeatCutoff
                    ? 'online'
                    : 'offline'
            );
        });

        $onlineDevices = $devices
            ->where('status', 'online')
            ->count();

        $offlineDevices = $devices
            ->where('status', 'offline')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | OPERATORS
        |--------------------------------------------------------------------------
        */

        $totalOperators = Operator::query()->count();

        $activeOperators = Operator::query()
            ->where('status', 'active')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | ACCESS LOGS
        |--------------------------------------------------------------------------
        */

        $totalLogs = AccessLog::query()->count();

        $grantedLogs = AccessLog::query()
            ->where('result', 'granted')
            ->count();

        $deniedLogs = AccessLog::query()
            ->where('result', 'denied')
            ->count();

        // Today's access statistics
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

        /*
        |--------------------------------------------------------------------------
        | ACCESS SESSIONS
        |--------------------------------------------------------------------------
        */

        $activeSessions = AccessSession::query()
            ->where('status', 'active')
            ->count();

        $completedSessions = AccessSession::query()
            ->where('status', 'completed')
            ->count();

        // Keep original variable for compatibility with existing Blade view.
        $averageDuration = AccessSession::query()
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds');

        $averageDuration = $averageDuration !== null
            ? (int) round($averageDuration)
            : null;

        // Human-readable duration without 24-hour wrapping.
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

        /*
        |--------------------------------------------------------------------------
        | RECENT ACCESS EVENTS
        |--------------------------------------------------------------------------
        */

        $recentLogs = AccessLog::query()
            ->orderByDesc('event_at')
            ->limit(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | MQTT MESSAGES
        |--------------------------------------------------------------------------
        */

        $recentMessages = MqttMessage::query()
            ->select([
                'id',
                'device_id',
                'topic',
                'received_at',
                'processing_status',
            ])
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $processedMessages = MqttMessage::query()
            ->where('processing_status', 'processed')
            ->count();

        $totalMessages = MqttMessage::query()->count();

        /*
        |--------------------------------------------------------------------------
        | SEVEN-DAY ACTIVITY
        |--------------------------------------------------------------------------
        */

        $startDate = $now->copy()
            ->subDays(6)
            ->startOfDay();

        $activityRows = DB::connection('atomic_access')
            ->table('access_logs')
            ->selectRaw('DATE(event_at) as activity_date')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw(
                "SUM(CASE WHEN result = 'granted' THEN 1 ELSE 0 END) as granted"
            )
            ->selectRaw(
                "SUM(CASE WHEN result = 'denied' THEN 1 ELSE 0 END) as denied"
            )
            ->where(
                'event_at',
                '>=',
                $startDate->toDateTimeString()
            )
            ->groupByRaw('DATE(event_at)')
            ->get()
            ->keyBy('activity_date');

        $activity = collect(range(6, 0))
            ->map(function (int $daysAgo) use ($now, $activityRows) {
                $date = $now->copy()
                    ->subDays($daysAgo)
                    ->startOfDay();

                $key = $date->toDateString();

                $row = $activityRows->get($key);

                return [
                    'label' => $date->translatedFormat('D'),
                    'date' => $date->format('d M'),
                    'total' => (int) ($row->total ?? 0),
                    'granted' => (int) ($row->granted ?? 0),
                    'denied' => (int) ($row->denied ?? 0),
                ];
            });

        $maxActivity = max(
            1,
            (int) $activity->max('total')
        );

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD VIEW
        |--------------------------------------------------------------------------
        */

        return view('dashboard.index', compact(
            'devices',
            'onlineDevices',
            'offlineDevices',

            'totalOperators',
            'activeOperators',

            'totalLogs',
            'grantedLogs',
            'deniedLogs',

            'todayLogs',
            'todayGranted',
            'todayDenied',

            'activeSessions',
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
