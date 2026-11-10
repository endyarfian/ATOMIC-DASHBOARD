<?php

namespace App\Http\Controllers;

use App\Models\MqttMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class MqttMonitorController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'in:processed,pending,failed'],
            'device_id' => ['nullable', 'string', 'max:100'],
            'topic' => ['nullable', 'string', 'max:190'],
        ]);

        $query = MqttMessage::query();

        if (!empty($filters['q'])) {
            $search = trim($filters['q']);
            $query->where(function ($builder) use ($search) {
                $builder->where('device_id', 'like', '%' . $search . '%')
                    ->orWhere('topic', 'like', '%' . $search . '%')
                    ->orWhere('id', $search);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('processing_status', $filters['status']);
        }

        if (!empty($filters['device_id'])) {
            $query->where('device_id', $filters['device_id']);
        }

        if (!empty($filters['topic'])) {
            $query->where('topic', $filters['topic']);
        }

        $totalMessages = (clone $query)->count();
        $processedMessages = (clone $query)->where('processing_status', 'processed')->count();
        $pendingMessages = (clone $query)->where('processing_status', 'pending')->count();
        $failedMessages = (clone $query)->where('processing_status', 'failed')->count();

        $messages = $query
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $messages->getCollection()->each(function (MqttMessage $message) {
            $decoded = json_decode((string) $message->payload, true);
            $message->setAttribute(
                'payload_pretty',
                is_array($decoded)
                    ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                    : (string) $message->payload
            );

            $message->setAttribute(
                'received_at_wib',
                $message->received_at
                    ? Carbon::parse($message->received_at, 'UTC')->timezone('Asia/Jakarta')->format('d M Y, H:i:s')
                    : '—'
            );
        });

        $devices = MqttMessage::query()
            ->whereNotNull('device_id')
            ->distinct()
            ->orderBy('device_id')
            ->pluck('device_id');

        $topics = MqttMessage::query()
            ->whereNotNull('topic')
            ->distinct()
            ->orderBy('topic')
            ->pluck('topic');

        return view('mqtt-monitor.index', compact(
            'messages',
            'filters',
            'totalMessages',
            'processedMessages',
            'pendingMessages',
            'failedMessages',
            'devices',
            'topics'
        ));
    }
}
