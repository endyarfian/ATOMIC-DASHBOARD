<?php

namespace App\Http\Controllers;

use App\Models\AccessSession;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionController extends Controller
{
    private const INTERRUPTED_TIMEOUT_SECONDS = 90;

    public function index(Request $request): View
    {
        // Snapshot stale sessions into the dashboard database. The source database
        // (atomic_access) is only queried and is never modified.
        $interruptedIds = app(\App\Services\SessionInterruptionDetector::class)->detect();

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

        if (($filters['status'] ?? null) === 'interrupted') {
            $query->whereIn('access_sessions.session_id', $interruptedIds);
        } elseif (($filters['status'] ?? null) === 'active') {
            $query->where('access_sessions.status', 'active')
                ->whereNotIn('access_sessions.session_id', $interruptedIds);
        } elseif (($filters['status'] ?? null) === 'completed') {
            $query->where('access_sessions.status', 'completed');
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

        $interruptedLookup = array_fill_keys($interruptedIds, true);
        $sessions->getCollection()->transform(function ($session) use ($interruptedLookup) {
            $session->display_status = isset($interruptedLookup[$session->session_id])
                ? 'interrupted'
                : $session->status;

            return $session;
        });

        $totalSessions = AccessSession::query()->count();
        $activeSessions = AccessSession::query()
            ->where('status', 'active')
            ->whereNotIn('session_id', $interruptedIds)
            ->count();
        $interruptedSessions = count($interruptedIds);
        $completedSessions = AccessSession::query()
            ->where('status', 'completed')
            ->count();

        return view('sessions.index', compact(
            'sessions',
            'filters',
            'totalSessions',
            'activeSessions',
            'interruptedSessions',
            'completedSessions'
        ));
    }

    public function show(string $sessionId): View
    {
        $interruptedIds = app(\App\Services\SessionInterruptionDetector::class)->detect();

        $session = AccessSession::query()
            ->leftJoin('operators', 'access_sessions.operator_id', '=', 'operators.operator_id')
            ->leftJoin('devices', 'access_sessions.device_id', '=', 'devices.device_id')
            ->select([
                'access_sessions.*',
                'operators.operator_name',
                'devices.device_name',
            ])
            ->where('access_sessions.session_id', $sessionId)
            ->firstOrFail();

        $session->display_status = in_array($session->session_id, $interruptedIds, true)
            ? 'interrupted'
            : $session->status;

        return view('sessions.show', compact('session'));
    }

}
