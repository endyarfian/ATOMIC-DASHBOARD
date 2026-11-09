<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <title>A.T.O.M. Dashboard</title>
    <style>
        :root{font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#e5e7eb;background:#080d18;font-synthesis:none}
        *{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at 80% -10%,#17243b 0,transparent 34%),#080d18}
        .shell{display:flex;min-height:100vh}.sidebar{width:248px;padding:28px 18px;border-right:1px solid #1e293b;background:#0b1220;display:flex;flex-direction:column}
        .brand{display:flex;align-items:center;gap:12px;padding:0 10px 30px}.brand-mark{width:38px;height:38px;border-radius:12px;background:#a3e635;color:#101827;display:grid;place-items:center;font-weight:900;letter-spacing:-2px}
        .brand strong{display:block;font-size:15px;letter-spacing:.12em}.brand small{color:#64748b;font-size:10px;letter-spacing:.18em}
        .nav-label{padding:14px 12px 8px;color:#64748b;font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase}
        .nav-item{display:flex;align-items:center;gap:12px;padding:11px 12px;margin:3px 0;border-radius:9px;color:#94a3b8;font-size:13px;text-decoration:none}
        .nav-item.active{background:#1a2a25;color:#bef264}.nav-item .dot{width:7px;height:7px;border-radius:50%;background:currentColor}
        .side-bottom{margin-top:auto;padding:14px 12px;border:1px solid #1e293b;border-radius:12px;color:#94a3b8;font-size:11px;line-height:1.6}
        .main{flex:1;min-width:0;padding:30px clamp(18px,4vw,54px) 42px}.top{display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:30px}
        .eyebrow{font-size:10px;letter-spacing:.2em;color:#84cc16;font-weight:800;text-transform:uppercase}.top h1{font-size:26px;margin:7px 0 5px;letter-spacing:-.04em}.sub{color:#64748b;font-size:12px}
        .live{display:flex;align-items:center;gap:9px;padding:9px 13px;border:1px solid #294331;border-radius:999px;background:#102019;color:#bef264;font-size:11px;font-weight:700}
        .live i{width:7px;height:7px;background:#84cc16;border-radius:50%;box-shadow:0 0 12px #84cc16}
        .section-head{display:flex;justify-content:space-between;align-items:end;margin:28px 0 14px}.section-head h2{font-size:14px;margin:0}.section-head span{font-size:11px;color:#64748b}
        .grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.card{background:linear-gradient(145deg,#111c2d,#0d1523);border:1px solid #1e2b40;border-radius:14px;padding:18px;min-width:0}
        .metric-top{display:flex;justify-content:space-between;align-items:center;color:#94a3b8;font-size:11px}.metric-icon{width:30px;height:30px;display:grid;place-items:center;border-radius:9px;background:#1c2b3d;color:#bef264;font-size:14px}
        .metric-value{font-size:29px;font-weight:750;letter-spacing:-.05em;margin:14px 0 5px;color:#f8fafc}.metric-note{font-size:10px;color:#64748b}.good{color:#a3e635}.bad{color:#fb7185}
        .two-col{display:grid;grid-template-columns:minmax(0,1.45fr) minmax(280px,.85fr);gap:14px}.chart-card{padding:20px}.bars{height:170px;display:flex;align-items:end;gap:clamp(8px,2vw,20px);padding:20px 4px 0}
        .bar-group{height:100%;flex:1;display:flex;flex-direction:column;justify-content:end;align-items:center;gap:8px;min-width:0}.bar-stack{height:132px;width:min(100%,34px);display:flex;flex-direction:column;justify-content:end;overflow:hidden;border-radius:6px 6px 3px 3px;background:#172438}
        .bar-granted{background:#84cc16;width:100%;display:block}.bar-denied{background:#fb7185;width:100%;display:block}.bar-label{font-size:10px;color:#94a3b8}.legend{display:flex;gap:16px;font-size:10px;color:#94a3b8}.legend i{display:inline-block;width:7px;height:7px;border-radius:2px;margin-right:6px}
        .device-list{display:flex;flex-direction:column;gap:12px}.device{display:flex;justify-content:space-between;gap:12px;align-items:center;padding:12px 0;border-bottom:1px solid #1e293b}.device:last-child{border:0}.device-name{font-size:12px;font-weight:650}.device-meta{font-size:10px;color:#64748b;margin-top:4px}.pill{font-size:9px;padding:5px 8px;border-radius:999px;background:#17251b;color:#bef264;text-transform:uppercase;font-weight:800;letter-spacing:.08em}.pill.offline{background:#301b25;color:#fda4af}
        .table-wrap{overflow-x:auto}.data-table{width:100%;border-collapse:collapse;white-space:nowrap}.data-table th{text-align:left;color:#64748b;font-size:9px;text-transform:uppercase;letter-spacing:.12em;font-weight:700;padding:12px 10px;border-bottom:1px solid #263449}.data-table td{font-size:11px;padding:13px 10px;border-bottom:1px solid #1b283a;color:#cbd5e1}.data-table tr:last-child td{border:0}.muted{color:#64748b!important}.status{font-size:9px;font-weight:800;text-transform:uppercase}.status.granted,.status.processed{color:#a3e635}.status.denied,.status.failed{color:#fb7185}.status.pending{color:#fbbf24}
        .bottom-grid{display:grid;grid-template-columns:minmax(0,1.4fr) minmax(280px,.8fr);gap:14px}.empty{padding:22px;text-align:center;color:#64748b;font-size:11px}
        footer{margin-top:24px;color:#475569;font-size:10px;display:flex;justify-content:space-between;gap:10px}
        @media(max-width:1050px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}.two-col,.bottom-grid{grid-template-columns:1fr}}
        @media(max-width:700px){.sidebar{width:62px;padding:20px 8px}.brand{padding:0 4px 28px}.brand-copy,.nav-label,.nav-text,.side-bottom{display:none}.brand-mark{width:42px;height:42px}.nav-item{justify-content:center;padding:13px 8px}.main{padding:22px 14px}.top{align-items:flex-start}.top h1{font-size:22px}.live{font-size:9px;padding:8px}.grid{gap:9px}.card{padding:14px}.metric-value{font-size:24px}.section-head{margin-top:22px}}
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div class="brand"><div class="brand-mark">A.</div><div class="brand-copy"><strong>A.T.O.M.</strong><small>ACCESS CONTROL</small></div></div>
        <div class="nav-label">Workspace</div>
        <a class="nav-item active" href="/"><span class="dot"></span><span class="nav-text">Overview</span></a>
        <div class="nav-item"><span class="dot"></span><span class="nav-text">Devices · soon</span></div>
        <div class="nav-item"><span class="dot"></span><span class="nav-text">Operators · soon</span></div>
        <div class="nav-item"><span class="dot"></span><span class="nav-text">Access logs · soon</span></div>
        <div class="nav-item"><span class="dot"></span><span class="nav-text">Sessions · soon</span></div>
        <div class="nav-item"><span class="dot"></span><span class="nav-text">MQTT monitor · soon</span></div>
        <div class="side-bottom">SYSTEM MONITOR<br><span class="good">●</span> Database connection active<br><span class="muted">Read-only source access</span></div>
    </aside>

    <main class="main">
        <header class="top">
            <div><div class="eyebrow">Biometric access intelligence</div><h1>System Overview</h1><div class="sub">Operational status and access activity · {{ now()->translatedFormat('l, d F Y') }}</div></div>
            <div class="live"><i></i> LIVE DATA</div>
        </header>

        <div class="section-head"><h2>Access summary</h2><span>Live counts from atomic_access</span></div>
        <section class="grid">
            <article class="card"><div class="metric-top">Registered devices <span class="metric-icon">⌘</span></div><div class="metric-value">{{ $devices->count() }}</div><div class="metric-note"><span class="good">{{ $devices->where('status', 'online')->count() }} online</span> · {{ $devices->where('status', 'offline')->count() }} offline</div></article>
            <article class="card"><div class="metric-top">Active operators <span class="metric-icon">♙</span></div><div class="metric-value">{{ number_format($activeOperators) }}</div><div class="metric-note">{{ number_format($totalOperators) }} registered operators</div></article>
            <article class="card"><div class="metric-top">Access granted <span class="metric-icon">✓</span></div><div class="metric-value good">{{ number_format($grantedLogs) }}</div><div class="metric-note">{{ number_format($totalLogs) }} total access events</div></article>
            <article class="card"><div class="metric-top">Access denied <span class="metric-icon">×</span></div><div class="metric-value bad">{{ number_format($deniedLogs) }}</div><div class="metric-note">Rejected fingerprint attempts</div></article>
        </section>

        <div class="section-head"><h2>Activity & device health</h2><span>Last 7 days · WIB</span></div>
        <section class="two-col">
            <article class="card chart-card">
                <div class="section-head" style="margin:0 0 8px"><h2>Access activity</h2><div class="legend"><span><i style="background:#84cc16"></i>Granted</span><span><i style="background:#fb7185"></i>Denied</span></div></div>
                <div class="bars">
                    @foreach ($activity as $day)
                        @php($grantedHeight = ($day['granted'] / $maxActivity) * 132)
                        @php($deniedHeight = ($day['denied'] / $maxActivity) * 132)
                        <div class="bar-group" title="{{ $day['date'] }}: {{ $day['total'] }} events">
                            <div class="bar-stack"><span class="bar-denied" style="height:{{ $deniedHeight }}px"></span><span class="bar-granted" style="height:{{ $grantedHeight }}px"></span></div>
                            <span class="bar-label">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>
            <article class="card">
                <div class="section-head" style="margin:0 0 8px"><h2>Registered devices</h2><span>{{ $devices->count() }} total</span></div>
                <div class="device-list">
                    @forelse ($devices as $device)
                        <div class="device"><div><div class="device-name">{{ $device->device_name }}</div><div class="device-meta">{{ $device->device_id }} · FW {{ $device->firmware_version ?? '—' }}</div><div class="device-meta">Last seen: {{ $device->last_seen ? \Illuminate\Support\Carbon::parse($device->last_seen)->format('d M Y, H:i:s') : 'Never' }}</div></div><span class="pill {{ $device->status === 'online' ? '' : 'offline' }}">{{ $device->status }}</span></div>
                    @empty
                        <div class="empty">No devices registered.</div>
                    @endforelse
                </div>
            </article>
        </section>

        <div class="section-head"><h2>Recent activity</h2><span>Latest records from source database</span></div>
        <section class="bottom-grid">
            <article class="card">
                <div class="section-head" style="margin:0 0 8px"><h2>Access events</h2><span>{{ $totalLogs }} records</span></div>
                <div class="table-wrap"><table class="data-table"><thead><tr><th>Time</th><th>Operator</th><th>Device</th><th>Result</th><th>Reason</th></tr></thead><tbody>
                @forelse ($recentLogs as $log)
                    <tr><td>{{ $log->event_at ? \Illuminate\Support\Carbon::parse($log->event_at)->format('d M, H:i:s') : '—' }}</td><td>{{ $log->operator_name ?: $log->operator_id ?: 'Unknown' }}</td><td>{{ $log->device_id }}</td><td><span class="status {{ $log->result }}">{{ $log->result }}</span></td><td class="muted">{{ $log->reason ?: '—' }}</td></tr>
                @empty
                    <tr><td colspan="5" class="empty">No access events recorded.</td></tr>
                @endforelse
                </tbody></table></div>
            </article>
            <article class="card">
                <div class="section-head" style="margin:0 0 8px"><h2>Session & MQTT health</h2></div>
                <div class="grid" style="grid-template-columns:repeat(2,minmax(0,1fr));gap:8px">
                    <div><div class="metric-note">Active sessions</div><div class="metric-value" style="font-size:23px">{{ number_format($activeSessions) }}</div></div>
                    <div><div class="metric-note">Completed sessions</div><div class="metric-value" style="font-size:23px">{{ number_format($completedSessions) }}</div></div>
                </div>
                <div class="metric-note" style="margin:8px 0 16px">Average session duration: {{ $averageDuration !== null ? gmdate('H:i:s', (int) $averageDuration) : '—' }}</div>
                <div class="section-head" style="margin:0 0 6px"><h2>Recent MQTT messages</h2><span>Latest 6</span></div>
                @forelse ($recentMessages as $message)
                    <div class="device"><div><div class="device-name">{{ \Illuminate\Support\Str::limit($message->topic, 36) }}</div><div class="device-meta">{{ $message->received_at ? \Illuminate\Support\Carbon::parse($message->received_at)->format('d M H:i:s') : '—' }}</div></div><span class="status {{ $message->processing_status }}">{{ $message->processing_status }}</span></div>
                @empty
                    <div class="empty">No MQTT messages recorded.</div>
                @endforelse
            </article>
        </section>
        <footer><span>A.T.O.M. · Biometric Access Management</span><span>Source: atomic_access · Read only · Asia/Jakarta</span></footer>
    </main>
</div>
</body>
</html>
