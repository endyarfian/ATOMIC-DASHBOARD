<?php

namespace App\Http\Controllers;

use App\Models\AccessSession;
use App\Services\SessionInterruptionDetector;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionController extends Controller
{
    private function orphanedSessionIds(): array
    {
        return AccessSession::query()
            ->leftJoin('operators', 'access_sessions.operator_db_id', '=', 'operators.operator_db_id')
            ->where('access_sessions.status', 'active')
            ->where(function ($query) {
                $query->whereNull('operators.operator_db_id')
                    ->orWhere('operators.status', '!=', 'active');
            })
            ->pluck('access_sessions.session_id')
            ->map(static fn ($id) => (string) $id)
            ->all();
    }

    private function baseQuery(): Builder
    {
        return AccessSession::query()
            ->leftJoin('operators', 'access_sessions.operator_db_id', '=', 'operators.operator_db_id')
            ->leftJoin('devices', 'access_sessions.device_id', '=', 'devices.device_id')
            ->select([
                'access_sessions.*',
                'operators.operator_name',
                'operators.device_operator_id',
                'operators.status as operator_status',
                'devices.device_name',
            ]);
    }

    private function applyDisplayStatus($sessions, array $interruptedIds, array $orphanedIds): void
    {
        $interruptedLookup = array_fill_keys($interruptedIds, true);
        $orphanedLookup = array_fill_keys($orphanedIds, true);

        $sessions->getCollection()->transform(function ($session) use ($interruptedLookup, $orphanedLookup) {
            $id = (string) $session->session_id;

            if ($session->status === 'completed') {
                $session->display_status = 'completed';
            } elseif (isset($orphanedLookup[$id])) {
                $session->display_status = 'orphaned';
            } elseif (isset($interruptedLookup[$id])) {
                $session->display_status = 'interrupted';
            } else {
                $session->display_status = $session->status;
            }

            return $session;
        });
    }

    public function index(Request $request): View
    {
        $interruptedIds = app(SessionInterruptionDetector::class)->detect();
        $orphanedIds = $this->orphanedSessionIds();

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,orphaned,interrupted,completed'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $query = $this->baseQuery();

        if (!empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function ($builder) use ($search) {
                $builder->where('access_sessions.session_id', 'like', '%' . $search . '%')
                    ->orWhere('access_sessions.operator_db_id', 'like', '%' . $search . '%')
                    ->orWhere('operators.device_operator_id', 'like', '%' . $search . '%')
                    ->orWhere('operators.operator_name', 'like', '%' . $search . '%')
                    ->orWhere('access_sessions.device_id', 'like', '%' . $search . '%')
                    ->orWhere('devices.device_name', 'like', '%' . $search . '%');
            });
        }

        if (($filters['status'] ?? null) === 'orphaned') {
            $query->whereIn('access_sessions.session_id', $orphanedIds);
        } elseif (($filters['status'] ?? null) === 'interrupted') {
            $query->whereIn('access_sessions.session_id', $interruptedIds)
                ->whereNotIn('access_sessions.session_id', $orphanedIds)
                ->where('access_sessions.status', '!=', 'completed');
        } elseif (($filters['status'] ?? null) === 'active') {
            $query->where('access_sessions.status', 'active')
                ->where('operators.status', 'active')
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

        $this->applyDisplayStatus($sessions, $interruptedIds, $orphanedIds);

        $totalSessions = AccessSession::query()->count();
        $activeSessions = $this->baseQuery()
            ->where('access_sessions.status', 'active')
            ->where('operators.status', 'active')
            ->whereNotIn('access_sessions.session_id', $interruptedIds)
            ->count('access_sessions.session_id');
        $orphanedSessions = count($orphanedIds);
        $interruptedSessions = $this->baseQuery()
            ->whereIn('access_sessions.session_id', $interruptedIds)
            ->whereNotIn('access_sessions.session_id', $orphanedIds)
            ->where('access_sessions.status', '!=', 'completed')
            ->count('access_sessions.session_id');
        $completedSessions = AccessSession::query()
            ->where('status', 'completed')
            ->count();

        return view('sessions.index', compact(
            'sessions',
            'filters',
            'totalSessions',
            'activeSessions',
            'orphanedSessions',
            'interruptedSessions',
            'completedSessions'
        ));
    }

    public function show(string $sessionId): View
    {
        $interruptedIds = app(SessionInterruptionDetector::class)->detect();
        $orphanedIds = $this->orphanedSessionIds();

        $session = $this->baseQuery()
            ->where('access_sessions.session_id', $sessionId)
            ->firstOrFail();

        $id = (string) $session->session_id;
        if ($session->status === 'completed') {
            $session->display_status = 'completed';
        } elseif (in_array($id, $orphanedIds, true)) {
            $session->display_status = 'orphaned';
        } elseif (in_array($id, $interruptedIds, true)) {
            $session->display_status = 'interrupted';
        } else {
            $session->display_status = $session->status;
        }

        return view('sessions.show', compact('session'));
    }
}
