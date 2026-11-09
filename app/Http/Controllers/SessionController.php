<?php

namespace App\\Http\\Controllers;

use App\\Models\\AccessSession;
use Illuminate\\Http\\Request;
use Illuminate\\View\\View;

class SessionController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,completed,incomplete'],
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

        if (!empty($filters['status'])) {
            $query->where('access_sessions.status', $filters['status']);
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

        $totalSessions = AccessSession::query()->count();
        $activeSessions = AccessSession::query()->where('status', 'active')->count();
        $completedSessions = AccessSession::query()->where('status', 'completed')->count();
        $incompleteSessions = AccessSession::query()->where('status', 'incomplete')->count();

        return view('sessions.index', compact(
            'sessions',
            'filters',
            'totalSessions',
            'activeSessions',
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
            ])
            ->where('access_sessions.session_id', $sessionId)
            ->firstOrFail();

        return view('sessions.show', compact('session'));
    }
}
