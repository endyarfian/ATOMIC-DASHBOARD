<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="color-scheme" content="dark"><meta name="theme-color" content="#080d16">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<title>{{ $device->device_name }} | A.T.O.M.</title>
<style>
:root{color-scheme:dark;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:#080d16;color:#e8eef7;--line:#223044;--muted:#8392a8;--muted2:#607087;--lime:#b7f36b;--cyan:#62d9e8;--red:#ff7188}
*{box-sizing:border-box}body{margin:0;min-width:320px;background:radial-gradient(ellipse at 78% -15%,rgba(46,105,130,.2),transparent 38%),#080d16}a{color:inherit;text-decoration:none}.app{min-height:100vh;display:grid;grid-template-columns:252px minmax(0,1fr)}.sidebar{position:sticky;top:0;height:100vh;padding:26px 17px 18px;display:flex;flex-direction:column;background:rgba(10,17,28,.96);border-right:1px solid var(--line)}.brand{display:flex;align-items:center;gap:12px;padding:0 9px 30px}.brand-mark{width:43px;height:43px;display:grid;place-items:center;border-radius:13px;background:linear-gradient(145deg,#d1ff8b,#86c94b);color:#101a20;font-size:20px;font-weight:950}.brand-name{font-size:15px;font-weight:850;letter-spacing:.17em}.brand-caption{margin-top:4px;color:var(--muted2);font-size:9px;letter-spacing:.19em}.side-label{padding:15px 11px 9px;color:#53647a;font-size:9px;font-weight:800;letter-spacing:.18em;text-transform:uppercase}.nav-link,.nav-soon{min-height:43px;display:flex;align-items:center;gap:12px;margin:3px 0;padding:0 12px;border-radius:10px;color:#91a0b5;font-size:12px}.nav-link.active{color:var(--lime);background:rgba(183,243,107,.1);border:1px solid rgba(183,243,107,.12)}.nav-icon{width:19px;text-align:center;font-size:15px}.nav-soon{color:#536176}.soon-tag{margin-left:auto;font-size:8px}.spacer{flex:1}.source{padding:14px;border:1px solid var(--line);border-radius:12px;background:#0d1724;color:var(--muted);font-size:9px}.source strong{display:block;color:var(--lime);margin:8px 0}
.main{min-width:0;padding:30px clamp(18px,3.4vw,48px) 34px}.topbar{display:flex;justify-content:space-between;align-items:flex-start;gap:18px;padding-bottom:24px;border-bottom:1px solid var(--line)}.eyebrow{color:var(--cyan);font-size:9px;font-weight:850;letter-spacing:.2em;text-transform:uppercase}h1{margin:8px 0 5px;font-size:clamp(25px,3vw,34px);letter-spacing:-.05em}.subtitle{color:var(--muted);font-size:11px}.back{padding:10px 13px;border:1px solid #2a3b50;border-radius:8px;color:#cbd7e6;font-size:10px}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:22px}.card{padding:17px;border:1px solid var(--line);border-radius:13px;background:linear-gradient(145deg,#142131,#0d1623)}.label{color:var(--muted);font-size:10px}.value{margin-top:10px;color:#f4f7fb;font-size:21px;font-weight:800;overflow-wrap:anywhere}.green{color:var(--lime)}.red{color:var(--red)}.panel{margin-top:14px;padding:18px;border:1px solid var(--line);border-radius:13px;background:#101927}.panel h2{margin:0 0 14px;font-size:12px}.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse;white-space:nowrap}th{padding:11px;border-bottom:1px solid #2a394d;color:#718198;font-size:8px;letter-spacing:.12em;text-align:left;text-transform:uppercase}td{padding:12px 11px;border-bottom:1px solid rgba(34,48,68,.68);font-size:10px;color:#c8d3e1}tbody tr:last-child td{border-bottom:0}.status{display:inline-block;padding:5px 8px;border-radius:6px;background:rgba(183,243,107,.08);color:var(--lime);font-size:8px;font-weight:850;text-transform:uppercase}.status.denied{background:rgba(255,113,136,.08);color:var(--red)}.empty{padding:22px;text-align:center;color:var(--muted2);font-size:10px}footer{margin-top:22px;padding-top:14px;border-top:1px solid var(--line);color:#596a80;font-size:8px}
@media(max-width:820px){.app{grid-template-columns:66px minmax(0,1fr)}.sidebar{padding:20px 8px 14px}.brand{justify-content:center;padding:0 0 25px}.brand-copy,.side-label,.nav-text,.soon-tag,.source{display:none}.nav-link,.nav-soon{justify-content:center;padding:0}.main{padding:24px 17px}}@media(max-width:600px){.main{padding:20px 12px}.topbar{flex-direction:column}.grid{grid-template-columns:1fr 1fr;gap:8px}.card{padding:12px}.value{font-size:17px}}@media(max-width:360px){.app{grid-template-columns:54px minmax(0,1fr)}.main{padding-left:9px;padding-right:9px}}
</style>
</head>
<body><div class="app">
<aside class="sidebar"><div class="brand"><div class="brand-mark">A.</div><div class="brand-copy"><div class="brand-name">A.T.O.M.</div><div class="brand-caption">BIOMETRIC ACCESS</div></div></div><div class="side-label">Workspace</div>
<a class="nav-link" href="{{ route('dashboard') }}"><span class="nav-icon">▦</span><span class="nav-text">Overview</span></a><a class="nav-link active" href="{{ route('devices.index') }}"><span class="nav-icon">▣</span><span class="nav-text">Devices</span></a>
<a class="nav-link" href="{{ route('operators.index') }}"><span class="nav-icon">♙</span><span class="nav-text">Operators</span></a><a class="nav-link" href="{{ route('access-logs.index') }}"><span class="nav-icon">≡</span><span class="nav-text">Access logs</span></a><a class="nav-link" href="{{ route('sessions.index') }}"><span class="nav-icon">◷</span><span class="nav-text">Sessions</span></a><div class="nav-soon"><span class="nav-icon">⌁</span><span class="nav-text">MQTT monitor</span><span class="soon-tag">Soon</span></div>
<div class="spacer"></div><div class="source">DATA SOURCE<strong>● Read-only connected</strong>atomic_access · monitoring only</div></aside>
<main class="main">
<header class="topbar"><div><div class="eyebrow">Device inventory / detail</div><h1>{{ $device->device_name ?: 'Unnamed device' }}</h1><div class="subtitle">{{ $device->device_id }}</div></div><a class="back" href="{{ route('devices.index') }}">← Back to devices</a></header>
<section class="grid">
<article class="card"><div class="label">Connection status</div><div class="value {{ $device->status === 'online' ? 'green' : 'red' }}">{{ strtoupper($device->status ?: 'UNKNOWN') }}</div></article>
<article class="card"><div class="label">Firmware version</div><div class="value">{{ $device->firmware_version ?: '—' }}</div></article>
<article class="card"><div class="label">Last heartbeat / last seen</div><div class="value" style="font-size:15px">{{ $device->last_seen ? \Illuminate\Support\Carbon::parse($device->last_seen)->format('d M Y, H:i:s') . ' WIB' : 'Never' }}</div></article>
<article class="card"><div class="label">Total access events</div><div class="value">{{ number_format($totalEvents) }}</div></article>
<article class="card"><div class="label">Granted events</div><div class="value green">{{ number_format($grantedEvents) }}</div></article>
<article class="card"><div class="label">Denied events</div><div class="value red">{{ number_format($deniedEvents) }}</div></article>
</section>
<section class="panel"><h2>Device information</h2><div class="table-wrap"><table><tbody>
<tr><th>Device ID</th><td>{{ $device->device_id }}</td></tr><tr><th>Device name</th><td>{{ $device->device_name ?: '—' }}</td></tr>
<tr><th>Registered at</th><td>{{ $device->created_at ? \Illuminate\Support\Carbon::parse($device->created_at)->format('d M Y, H:i:s') . ' WIB' : '—' }}</td></tr>
<tr><th>Last database update</th><td>{{ $device->updated_at ? \Illuminate\Support\Carbon::parse($device->updated_at)->format('d M Y, H:i:s') . ' WIB' : '—' }}</td></tr>
</tbody></table></div></section>
<section class="panel"><h2>Recent access events · latest 10</h2><div class="table-wrap"><table><thead><tr><th>Time</th><th>Operator</th><th>Fingerprint slot</th><th>Result</th><th>Reason</th></tr></thead><tbody>
@forelse($recentEvents as $event)
<tr><td>{{ $event->event_at ? \Illuminate\Support\Carbon::parse($event->event_at)->format('d M Y, H:i:s') : '—' }}</td><td>{{ $event->operator_name ?: 'Unknown operator' }}<div class="sub">DB ID · {{ $event->operator_db_id ?: '—' }}</div></td><td>{{ $event->fingerprint_slot ?? '—' }}</td><td><span class="status {{ $event->result === 'granted' ? '' : 'denied' }}">{{ $event->result }}</span></td><td>{{ $event->reason ?: '—' }}</td></tr>
@empty
<tr><td colspan="5" class="empty">No access events recorded for this device.</td></tr>
@endforelse
</tbody></table></div></section>
<footer>A.T.O.M. · atomic_access is queried in read-only mode · Asia/Jakarta</footer>
</main></div><script>
    // Refresh connection status periodically so heartbeat timeouts appear without manual reload.
    window.setInterval(() => window.location.reload(), 30000);
</script>
</body></html>
