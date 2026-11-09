<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OperatorController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $query = Operator::query()
            ->leftJoin('devices', 'operators.device_id', '=', 'devices.device_id')
            ->select([
                'operators.operator_id',
                'operators.device_id',
                'operators.operator_name',
                'operators.fingerprint_slot',
                'operators.status',
                'operators.created_at',
                'devices.device_name',
            ]);

        if (!empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function ($builder) use ($search) {
                $builder->where('operators.operator_id', 'like', '%' . $search . '%')
                    ->orWhere('operators.operator_name', 'like', '%' . $search . '%')
                    ->orWhere('operators.device_id', 'like', '%' . $search . '%')
                    ->orWhere('devices.device_name', 'like', '%' . $search . '%')
                    ->orWhere('operators.fingerprint_slot', 'like', '%' . $search . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('operators.status', $filters['status']);
        }

        $operators = $query
            ->orderBy('operators.operator_name')
            ->orderBy('operators.operator_id')
            ->paginate(15)
            ->withQueryString();

        $totalOperators = Operator::query()->count();
        $activeOperators = Operator::query()->where('status', 'active')->count();
        $inactiveOperators = Operator::query()->where('status', 'inactive')->count();

        return view('operators.index', compact(
            'operators', 'totalOperators', 'activeOperators', 'inactiveOperators', 'filters'
        ));
    }

    public function show(string $operatorId): View
    {
        $operator = Operator::query()
            ->leftJoin('devices', 'operators.device_id', '=', 'devices.device_id')
            ->select([
                'operators.operator_id',
                'operators.device_id',
                'operators.operator_name',
                'operators.fingerprint_slot',
                'operators.status',
                'operators.created_at',
                'operators.updated_at',
                'devices.device_name',
            ])
            ->where('operators.operator_id', $operatorId)
            ->firstOrFail();

        $totalEvents = AccessLog::query()->where('operator_id', $operator->operator_id)->count();
        $grantedEvents = AccessLog::query()
            ->where('operator_id', $operator->operator_id)
            ->where('result', 'granted')
            ->count();
        $deniedEvents = AccessLog::query()
            ->where('operator_id', $operator->operator_id)
            ->where('result', 'denied')
            ->count();

        $recentEvents = AccessLog::query()
            ->where('operator_id', $operator->operator_id)
            ->orderByDesc('event_at')
            ->limit(20)
            ->get();

        return view('operators.show', compact(
            'operator', 'totalEvents', 'grantedEvents', 'deniedEvents', 'recentEvents'
        ));
    }
}
