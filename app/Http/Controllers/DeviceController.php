<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeviceController extends Controller
{
    private const HEARTBEAT_TIMEOUT_SECONDS = 60;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:online,offline'],
        ]);

        $heartbeatCutoff = now()
            ->subSeconds(self::HEARTBEAT_TIMEOUT_SECONDS)
            ->toDateTimeString();

        $query = Device::query()->select([
            'device_id', 'device_name', 'status', 'firmware_version',
            'last_seen', 'created_at', 'updated_at',
        ]);

        if (!empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function ($builder) use ($search) {
                $builder->where('device_id', 'like', '%' . $search . '%')
                    ->orWhere('device_name', 'like', '%' . $search . '%')
                    ->orWhere('firmware_version', 'like', '%' . $search . '%');
            });
        }

        if (($filters['status'] ?? null) === 'online') {
            $query->whereNotNull('last_seen')
                ->where('last_seen', '>=', $heartbeatCutoff);
        } elseif (($filters['status'] ?? null) === 'offline') {
            $query->where(function ($builder) use ($heartbeatCutoff) {
                $builder->whereNull('last_seen')
                    ->orWhere('last_seen', '<', $heartbeatCutoff);
            });
        }

        $devices = $query
            ->orderByRaw(
                'CASE WHEN last_seen IS NOT NULL AND last_seen >= ? THEN 0 ELSE 1 END',
                [$heartbeatCutoff]
            )
            ->orderByDesc('last_seen')
            ->orderBy('device_name')
            ->paginate(15)
            ->withQueryString();

        $devices->getCollection()->each(function (Device $device) use ($heartbeatCutoff) {
            $device->setAttribute(
                'status',
                $device->last_seen !== null && $device->last_seen >= $heartbeatCutoff
                    ? 'online'
                    : 'offline'
            );
        });

        $totalDevices = Device::query()->count();
        $onlineDevices = Device::query()
            ->whereNotNull('last_seen')
            ->where('last_seen', '>=', $heartbeatCutoff)
            ->count();
        $offlineDevices = Device::query()
            ->where(function ($builder) use ($heartbeatCutoff) {
                $builder->whereNull('last_seen')
                    ->orWhere('last_seen', '<', $heartbeatCutoff);
            })
            ->count();

        return view('devices.index', compact(
            'devices', 'totalDevices', 'onlineDevices', 'offlineDevices', 'filters'
        ));
    }

    public function show(string $deviceId): View
    {
        $device = Device::query()
            ->where('device_id', $deviceId)
            ->firstOrFail();

        $heartbeatCutoff = now()
            ->subSeconds(self::HEARTBEAT_TIMEOUT_SECONDS)
            ->toDateTimeString();

        $device->setAttribute(
            'status',
            $device->last_seen !== null && $device->last_seen >= $heartbeatCutoff
                ? 'online'
                : 'offline'
        );

        $totalEvents = AccessLog::query()->where('device_id', $device->device_id)->count();
        $grantedEvents = AccessLog::query()
            ->where('device_id', $device->device_id)->where('result', 'granted')->count();
        $deniedEvents = AccessLog::query()
            ->where('device_id', $device->device_id)->where('result', 'denied')->count();

        $recentEvents = AccessLog::query()
            ->where('device_id', $device->device_id)
            ->orderByDesc('event_at')
            ->limit(10)
            ->get();

        return view('devices.show', compact(
            'device', 'totalEvents', 'grantedEvents', 'deniedEvents', 'recentEvents'
        ));
    }
}
