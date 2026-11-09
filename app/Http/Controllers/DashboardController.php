<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\AccessSession;
use App\Models\Device;
use App\Models\MqttMessage;
use App\Models\Operator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $devices = Device::query()->orderBy('device_name')->get();
        $totalOperators = Operator::query()->count();
        $activeOperators = Operator::query()->where('status', 'active')->count();

        $totalLogs = AccessLog::query()->count();
        $grantedLogs = AccessLog::query()->where('result', 'granted')->count();
        $deniedLogs = AccessLog::query()->where('result', 'denied')->count();

        $activeSessions = AccessSession::query()->where('status', 'active')->count();
        $completedSessions = AccessSession::query()->where('status', 'completed')->count();
        $averageDuration = AccessSession::query()
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds');

        $recentLogs = AccessLog::query()
            ->orderByDesc('event_at')
            ->limit(8)
            ->get();

        $recentMessages = MqttMessage::query()
            ->select(['id', 'topic', 'received_at', 'processing_status'])
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $startDate = now()->subDays(6)->startOfDay();

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

        $activity = collect(range(6, 0))->map(function (int $daysAgo) use ($activityRows) {
            $date = now()->subDays($daysAgo)->startOfDay();
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

        $maxActivity = max(1, (int) $activity->max('total'));

        return view('dashboard.index', compact(
            'devices',
            'totalOperators',
            'activeOperators',
            'totalLogs',
            'grantedLogs',
            'deniedLogs',
            'activeSessions',
            'completedSessions',
            'averageDuration',
            'recentLogs',
            'recentMessages',
            'activity',
            'maxActivity'
        ));
    }
}
