<?php

namespace App\Services;

use App\Models\AccessSession;
use Illuminate\Support\Facades\DB;

class SessionInterruptionDetector
{
    private const TIMEOUT_SECONDS = 90;

    /**
     * Persist heartbeat timeouts without writing to the atomic_access database.
     */
    public function detect(): array
    {
        $cutoff = now('UTC')->subSeconds(self::TIMEOUT_SECONDS)->toDateTimeString();
        $now = now('UTC')->toDateTimeString();

        $candidates = AccessSession::query()
            ->leftJoin('devices', 'access_sessions.device_id', '=', 'devices.device_id')
            ->select([
                'access_sessions.session_id',
                'access_sessions.device_id',
                'access_sessions.status',
                'devices.last_seen',
            ])
            ->where(function ($query) use ($cutoff) {
                $query->where('access_sessions.status', 'incomplete')
                    ->orWhere(function ($active) use ($cutoff) {
                        $active->where('access_sessions.status', 'active')
                            ->where(function ($stale) use ($cutoff) {
                                $stale->whereNull('devices.last_seen')
                                    ->orWhere('devices.last_seen', '<', $cutoff);
                            });
                    });
            })
            ->get();

        foreach ($candidates->chunk(100) as $chunk) {
            $rows = [];

            foreach ($chunk as $candidate) {
                $rows[] = [
                    'session_id' => (string) $candidate->session_id,
                    'device_id' => $candidate->device_id,
                    'interrupted_at' => $now,
                    'reason' => $candidate->status === 'incomplete'
                        ? 'source_incomplete'
                        : 'heartbeat_timeout',
                    'last_seen_at' => $candidate->last_seen,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($rows !== []) {
                DB::connection('mysql')
                    ->table('session_interruptions')
                    ->insertOrIgnore($rows);
            }
        }

        return DB::connection('mysql')
            ->table('session_interruptions')
            ->pluck('session_id')
            ->map(static fn ($id) => (string) $id)
            ->all();
    }
}
