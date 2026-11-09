<?php

namespace App\Http\Controllers;

use App\Models\AccessSession;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class SessionController extends Controller
{
    private const INTERRUPTED_TIMEOUT_SECONDS = 90;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,interrupted,completed'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $query = AccessSession::query()
            ->leftJoin('operators', 'access_sessions.operator_id', '=', 'operators.operator_id')
            ->leftJoin('devices', 'access_sessions.device_id', '=', 'devices.device_id')
            ->select([
                'access_sessions.*',
                'operators.operator_name',
                'devices.device_name',
                'devices.last_seen',
            ]);

        if (!empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function ($builder) use ($search) {
                $builder->where('access_sessions.session_id', 'like', '%' . $search . '%')
                    ->orWhere('access_sessions.operator_id', 'like', '%' . $search . '%')
                    ->orWhere('operators.operator_name', 'like', '%' . $search . '%')
                    ->orWhere('access_sessions.device_id', 'like', '%' . $search . '%')
                    ->orWhere('devices.device_name', 'like', '%' . $search . '%');
            });
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'interrupted') {
                $cutoff = now('UTC')->subSeconds(self::INTERRUPTED_TIMEOUT_SECONDS)->toDateTimeString();
                $query->where(function ($builder) use ($cutoff) {
                    $builder->where('access_sessions.status', 'incomplete')
                        ->orWhere(function ($active) use ($cutoff) {
                            $active->where('access_sessions.status', 'active')
                                ->where(function ($stale) use ($cutoff) {
                                    $stale->whereNull('devices.last_seen')
                                        ->orWhere('devices.last_seen', '<', $cutoff);
                                });
                        });
                });
            } elseif ($filters['status'] === 'active') {
                $cutoff = now('UTC')->subSeconds(self::INTERRUPTED_TIMEOUT_SECONDS)->toDateTimeString();
                $query->where('access_sessions.status', 'active')
                    ->whereNotNull('devices.last_seen')
                    ->where('devices.last_seen', '>=', $cutoff);
            } else {
                $query->where('access_sessions.status', $filters['status']);
            }
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('access_sessions.started_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('access_sessions.started_at', '<=', $filters['end_date']);
        }

        $sessions = $query
            ->orderByDesc('access_sessions.started_at')
            ->orderByDesc('access_sessions.session_id')
            ->paginate(20)
            ->withQueryString();

        $cutoff = now('UTC')->subSeconds(self::INTERRUPTED_TIMEOUT_SECONDS);
        $sessions->getCollection()->transform(function ($session) use ($cutoff) {
            $session->display_status = $session->status;

            if ($session->status === 'incomplete') {\n                $session->display_status = 'interrupted';\n            } elseif ($session->status === 'active') {
                $lastSeen = $session->last_seen
                    ? Carbon::parse($session->last_seen, 'UTC')
                    : null;

                $session->display_status = $lastSeen && $lastSeen->greaterThanOrEqualTo($cutoff)
                    ? 'active'
                    : 'interrupted';
            }

            return $session;
        });

        $totalSessions = AccessSession::query()->count();
        $activeSessions = AccessSession::query()
            ->join('devices', 'access_sessions.device_id', '=', 'devices.device_id')
            ->where('access_sessions.status', 'active')
            ->whereNotNull('devices.last_seen')
            ->where('devices.last_seen', '>=', $cutoff->toDateTimeString())
            ->count();

        $interruptedSessions = AccessSession::query()
            ->leftJoin('devices', 'access_sessions.device_id', '=', 'devices.device_id')
            ->where('access_sessions.status', 'active')
            ->where(function ($builder) use ($cutoff) {
                $builder->whereNull('devices.last_seen')
                    ->orWhere('devices.last_seen', '<', $cutoff->toDateTimeString());
            })
            ->count();

        $completedSessions = AccessSession::query()->where('status', 'completed')->count();
        $incompleteSessions = AccessSession::query()->where('status', 'incomplete')->count();

        return view('sessions.index', compact(
            'sessions',
            'filters',
            'totalSessions',
            'activeSessions',
            'interruptedSessions',
            'completedSessions',
            'incompleteSessions'
        ));
    }

    public function show(string $sessionId): View
    {
        $session = AccessSession::query()
            ->leftJoin('operators', 'access_sessions.operator_id', '=', 'operators.operator_id')
            ->leftJoin('devices', 'access_sessions.device_id', '=', 'devices.device_id')
            ->select([
                'access_sessions.*',
                'operators.operator_name',
                'devices.device_name',
                'devices.last_seen',
            ])
            ->where('access_sessions.session_id', $sessionId)
            ->firstOrFail();

        $session->display_status = $session->status;
        if ($session->status === 'incomplete') {\n            $session->display_status = 'interrupted';\n        } elseif ($session->status === 'active') {
            $lastSeen = $session->last_seen
                ? Carbon::parse($session->last_seen, 'UTC')
                : null;
            $session->display_status = $lastSeen && $lastSeen->greaterThanOrEqualTo(
                now('UTC')->subSeconds(self::INTERRUPTED_TIMEOUT_SECONDS)
            ) ? 'active' : 'interrupted';
        }

        return view('sessions.show', compact('session'));
    }
}
