<?php

namespace App\\Http\\Controllers;

use App\\Models\\AccessLog;
use App\\Models\\Device;
use Illuminate\\Http\\Request;
use Illuminate\\View\\View;

class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:online,offline'],
        ]);

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

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $devices = $query
            ->orderByRaw("CASE WHEN status = 'online' THEN 0 ELSE 1 END")
            ->orderByDesc('last_seen')
            ->orderBy('device_name')
            ->paginate(15)
            ->withQueryString();

        $totalDevices = Device::query()->count();
        $onlineDevices = Device::query()->where('status', 'online')->count();
        $offlineDevices = Device::query()->where('status', 'offline')->count();

        return view('devices.index', compact(
            'devices', 'totalDevices', 'onlineDevices', 'offlineDevices', 'filters'
        ));
    }

    public function show(string $deviceId): View
    {
        $device = Device::query()
            ->where('device_id', $deviceId)
            ->firstOrFail();

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
