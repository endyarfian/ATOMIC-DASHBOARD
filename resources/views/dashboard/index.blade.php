<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <meta name="theme-color" content="#080d16">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>A.T.O.M. | Access Control Dashboard</title>
    <style>
        :root {
            color-scheme: dark;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #080d16;
            color: #e8eef7;
            font-synthesis: none;
            --bg: #080d16;
            --panel: #101927;
            --panel-soft: #0d1623;
            --line: #223044;
            --muted: #8392a8;
            --muted-2: #607087;
            --lime: #b7f36b;
            --cyan: #62d9e8;
            --red: #ff7188;
            --amber: #f4c76b;
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-width: 320px; background: radial-gradient(ellipse at 78% -15%, rgba(46, 105, 130, .20), transparent 38%), var(--bg); }
        a { color: inherit; }
        .app { min-height: 100vh; display: grid; grid-template-columns: 252px minmax(0, 1fr); }
        .sidebar { position: sticky; top: 0; height: 100vh; padding: 26px 17px 18px; display: flex; flex-direction: column; background: rgba(10, 17, 28, .96); border-right: 1px solid var(--line); }
        .brand { display: flex; align-items: center; gap: 12px; padding: 0 9px 30px; }
        .brand-mark { width: 43px; height: 43px; display: grid; place-items: center; border-radius: 13px; background: linear-gradient(145deg, #d1ff8b, #86c94b); color: #101a20; font-size: 20px; font-weight: 950; letter-spacing: -2px; box-shadow: 0 7px 24px rgba(183, 243, 107, .12); }
        .brand-name { font-size: 15px; font-weight: 850; letter-spacing: .17em; }
        .brand-caption { margin-top: 4px; color: var(--muted-2); font-size: 9px; letter-spacing: .19em; }
        .side-label { padding: 15px 11px 9px; color: #53647a; font-size: 9px; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; }
        .nav-link, .nav-soon { min-height: 43px; display: flex; align-items: center; gap: 12px; margin: 3px 0; padding: 0 12px; border-radius: 10px; color: #91a0b5; font-size: 12px; text-decoration: none; }
        .nav-link.active { color: var(--lime); background: linear-gradient(90deg, rgba(183, 243, 107, .13), rgba(183, 243, 107, .035)); border: 1px solid rgba(183, 243, 107, .12); }
        .nav-icon { width: 19px; text-align: center; font-size: 15px; }
        .nav-soon { color: #536176; }
        .soon-tag { margin-left: auto; font-size: 8px; letter-spacing: .08em; text-transform: uppercase; }
        .side-spacer { flex: 1; }
        .source-box { padding: 14px; border: 1px solid var(--line); border-radius: 12px; background: #0d1724; }
        .source-title { color: #8293a8; font-size: 9px; font-weight: 800; letter-spacing: .15em; }
        .source-state { display: flex; align-items: center; gap: 8px; margin-top: 11px; color: var(--lime); font-size: 11px; font-weight: 700; }
        .state-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--lime); box-shadow: 0 0 12px rgba(183, 243, 107, .75); }
        .source-note { margin-top: 8px; color: var(--muted-2); font-size: 9px; line-height: 1.6; }
        .main { min-width: 0; padding: 30px clamp(18px, 3.4vw, 48px) 34px; }
        .topbar { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; padding-bottom: 24px; border-bottom: 1px solid rgba(34, 48, 68, .8); }
        .eyebrow { color: var(--cyan); font-size: 9px; font-weight: 850; letter-spacing: .2em; text-transform: uppercase; }
        h1 { margin: 8px 0 5px; color: #f3f7fc; font-size: clamp(25px, 3vw, 34px); line-height: 1.1; letter-spacing: -.055em; }
        .subtitle { color: var(--muted); font-size: 11px; }
        .top-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 9px; }
        .live-badge { display: inline-flex; align-items: center; gap: 9px; padding: 9px 13px; border: 1px solid rgba(183, 243, 107, .24); border-radius: 8px; background: rgba(183, 243, 107, .08); color: var(--lime); font-size: 9px; font-weight: 850; letter-spacing: .13em; white-space: nowrap; }
        .live-badge .state-dot { width: 6px; height: 6px; }
        .updated { color: var(--muted-2); font-size: 9px; white-space: nowrap; }
        .section-heading { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin: 26px 0 13px; }
        .section-heading h2 { margin: 0; color: #dce5f1; font-size: 12px; font-weight: 800; letter-spacing: .035em; }
        .section-hint { color: var(--muted-2); font-size: 9px; }
        .metrics { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        .metric { position: relative; min-height: 126px; overflow: hidden; padding: 16px 17px; border: 1px solid var(--line); border-radius: 13px; background: linear-gradient(145deg, rgba(20, 33, 49, .98), rgba(13, 22, 35, .98)); }
        .metric:after { position: absolute; right: -28px; bottom: -52px; width: 110px; height: 110px; border: 1px solid rgba(98, 217, 232, .08); border-radius: 50%; content: ""; }
        .metric-head { display: flex; justify-content: space-between; align-items: center; gap: 8px; color: #9baabd; font-size: 10px; font-weight: 650; }
        .metric-icon { width: 29px; height: 29px; display: grid; place-items: center; border: 1px solid #2b3b50; border-radius: 9px; background: #172538; color: var(--cyan); font-size: 13px; }
        .metric-icon svg { display: block; width: 16px; height: 16px; flex: none; stroke: currentColor; stroke-width: 1.8; }
        .metric-number { margin-top: 13px; color: #f4f7fb; font-size: 29px; font-weight: 800; line-height: 1; letter-spacing: -.055em; font-variant-numeric: tabular-nums; }
        .metric-number.lime { color: var(--lime); }
        .metric-number.red { color: var(--red); }
        .metric-foot { margin-top: 8px; color: var(--muted); font-size: 9px; line-height: 1.5; }
        .good { color: var(--lime); }
        .bad { color: var(--red); }
        .cyan { color: var(--cyan); }
        .overview-grid { display: grid; grid-template-columns: minmax(0, 1.45fr) minmax(290px, .85fr); gap: 13px; }
        .panel { min-width: 0; padding: 18px; border: 1px solid var(--line); border-radius: 13px; background: linear-gradient(145deg, rgba(16, 26, 40, .98), rgba(12, 20, 32, .98)); }
        .panel-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 15px; }
        .panel-title { color: #e1e9f3; font-size: 11px; font-weight: 800; }
        .panel-description { margin-top: 4px; color: var(--muted-2); font-size: 9px; }
        .legend { display: flex; align-items: center; gap: 12px; color: var(--muted); font-size: 9px; white-space: nowrap; }
        .legend-item { display: inline-flex; align-items: center; gap: 5px; }
        .legend-dot { width: 7px; height: 7px; border-radius: 2px; }
        .chart { height: 174px; display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 10px; padding: 12px 4px 0; }
        .chart-day { min-width: 0; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; gap: 8px; }
        .chart-column { width: min(100%, 35px); height: 139px; display: flex; flex-direction: column; justify-content: flex-end; overflow: hidden; border-radius: 6px 6px 3px 3px; background: #1a293b; }
        .chart-denied { display: block; width: 100%; background: var(--red); min-height: 0; }
        .chart-granted { display: block; width: 100%; background: var(--lime); min-height: 0; }
        .chart-label { color: #8999ad; font-size: 9px; }
        .device-list { display: flex; flex-direction: column; }
        .device-row { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid rgba(34, 48, 68, .75); }
        .device-row:last-child { border-bottom: 0; }
        .device-main { min-width: 0; }
        .device-name { overflow: hidden; color: #e1e9f3; font-size: 10px; font-weight: 750; text-overflow: ellipsis; white-space: nowrap; }
        .device-detail { margin-top: 4px; color: var(--muted-2); font-size: 9px; overflow-wrap: anywhere; }
        .device-status { flex: 0 0 auto; padding: 5px 8px; border: 1px solid rgba(183, 243, 107, .18); border-radius: 6px; background: rgba(183, 243, 107, .08); color: var(--lime); font-size: 8px; font-weight: 850; letter-spacing: .08em; text-transform: uppercase; }
        .device-status.offline { border-color: rgba(255, 113, 136, .2); background: rgba(255, 113, 136, .08); color: var(--red); }
        .activity-grid { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(280px, .8fr); gap: 13px; }
        .table-wrap { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        th { padding: 10px 9px; border-bottom: 1px solid #2a394d; color: #718198; font-size: 8px; font-weight: 800; letter-spacing: .12em; text-align: left; text-transform: uppercase; }
        td { padding: 12px 9px; border-bottom: 1px solid rgba(34, 48, 68, .68); color: #c8d3e1; font-size: 9px; }
        tbody tr:last-child td { border-bottom: 0; }
        .result { display: inline-block; padding: 4px 7px; border-radius: 5px; background: rgba(183, 243, 107, .08); color: var(--lime); font-size: 8px; font-weight: 850; text-transform: uppercase; }
        .result.denied, .result.failed { background: rgba(255, 113, 136, .08); color: var(--red); }
        .result.pending { background: rgba(244, 199, 107, .08); color: var(--amber); }
        .muted { color: var(--muted-2); }
        .health-pair { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 9px; margin-bottom: 17px; }
        .health-box { padding: 12px; border: 1px solid #25354a; border-radius: 9px; background: #0c1725; }
        .health-label { color: var(--muted); font-size: 9px; }
        .health-number { margin-top: 6px; color: #f0f5fb; font-size: 22px; font-weight: 800; letter-spacing: -.05em; font-variant-numeric: tabular-nums; }
        .subsection { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin: 15px 0 8px; }
        .subsection strong { color: #dce5f1; font-size: 10px; }
        .subsection span { color: var(--muted-2); font-size: 9px; }
        .progress-track { height: 7px; overflow: hidden; border-radius: 99px; background: #243247; }
        .progress-fill { height: 100%; border-radius: inherit; background: linear-gradient(90deg, #7dc94e, #c4f47b); }
        .progress-caption { display: flex; justify-content: space-between; gap: 8px; margin-top: 7px; color: var(--muted); font-size: 9px; }
        .mqtt-row { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid rgba(34, 48, 68, .68); }
        .mqtt-row:last-child { border-bottom: 0; }
        .mqtt-topic { overflow: hidden; color: #d7e1ed; font-size: 9px; font-weight: 700; text-overflow: ellipsis; white-space: nowrap; }
        .mqtt-meta { margin-top: 4px; color: var(--muted-2); font-size: 8px; }
        .mqtt-status { flex: 0 0 auto; color: var(--lime); font-size: 8px; font-weight: 850; text-transform: uppercase; }
        .mqtt-status.failed { color: var(--red); }
        .mqtt-status.pending { color: var(--amber); }
        .empty { padding: 20px 10px; color: var(--muted-2); font-size: 10px; text-align: center; }
        footer { display: flex; justify-content: space-between; gap: 12px; margin-top: 22px; padding-top: 14px; border-top: 1px solid rgba(34, 48, 68, .7); color: #596a80; font-size: 8px; }
        @media (max-width: 1120px) { .app { grid-template-columns: 218px minmax(0, 1fr); } .overview-grid, .activity-grid { grid-template-columns: 1fr; } }
        @media (max-width: 820px) { .app { grid-template-columns: 66px minmax(0, 1fr); } .sidebar { padding: 20px 8px 14px; } .brand { justify-content: center; padding: 0 0 25px; } .brand-copy, .side-label, .nav-text, .soon-tag, .source-box { display: none; } .nav-link, .nav-soon { justify-content: center; padding: 0; } .nav-icon { font-size: 17px; } .main { padding: 24px 17px; } }
        @media (max-width: 600px) { .main { padding: 20px 12px 25px; } .topbar { flex-direction: column; gap: 13px; } .top-meta { align-items: flex-start; flex-direction: row; flex-wrap: wrap; } .metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; } .metric { min-height: 116px; padding: 12px; } .metric-number { font-size: 25px; } .metric-head { font-size: 9px; } .metric-icon { width: 25px; height: 25px; } .panel { padding: 13px; } .section-heading { margin-top: 21px; } .chart { gap: 5px; } .legend { gap: 8px; } footer { flex-direction: column; } }
        @media (max-width: 360px) { .app { grid-template-columns: 54px minmax(0, 1fr); } .sidebar { padding-left: 5px; padding-right: 5px; } .main { padding-left: 9px; padding-right: 9px; } .metric-number { font-size: 22px; } .metric { padding: 10px; } }
    .brand-mark svg{display:block;width:24px;height:24px;stroke:currentColor;stroke-width:1.8}
.nav-icon{display:inline-flex;align-items:center;justify-content:center;flex:0 0 19px;width:19px;height:19px}
.nav-icon svg{display:block;width:17px;height:17px;stroke:currentColor;stroke-width:1.9;flex:none}
</style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark" aria-label="A.T.O.M."><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"/><path d="M14 13.12c0 2.38 0 6.38-1 8.88"/><path d="M17.29 21.02c.12-.6.43-2.3.5-3.02"/><path d="M2 12a10 10 0 0 1 18-6"/><path d="M2 16h.01"/><path d="M21.8 16c.2-2 .131-5.354 0-6"/><path d="M5 19.5C5.5 18 6 15 6 12a6 6 0 0 1 .34-2"/><path d="M8.65 22c.21-.66.45-1.32.57-2"/><path d="M9 6.8a6 6 0 0 1 9 5.2v2"/></svg></div>
            <div class="brand-copy">
                <div class="brand-name">A.T.O.M.</div>
                <div class="brand-caption">BIOMETRIC ACCESS</div>
            </div>
        </div>
        <div class="side-label">Workspace</div>
        <a class="nav-link active" href="/" aria-current="page"><span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="17.5" cy="17.5" r="3.5"/><circle cx="17.5" cy="6.5" r="3.5"/><circle cx="6.5" cy="17.5" r="3.5"/><circle cx="6.5" cy="6.5" r="3.5"/></svg></span><span class="nav-text">Overview</span></a>
        <a class="nav-link" href="{{ route('devices.index') }}"><span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20v2"/><path d="M12 2v2"/><path d="M17 20v2"/><path d="M17 2v2"/><path d="M2 12h2"/><path d="M2 17h2"/><path d="M2 7h2"/><path d="M20 12h2"/><path d="M20 17h2"/><path d="M20 7h2"/><path d="M7 20v2"/><path d="M7 2v2"/><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="8" y="8" width="8" height="8" rx="1"/></svg></span><span class="nav-text">Devices</span></a>
        <a class="nav-link" href="{{ route('operators.index') }}"><span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M16 3.128a4 4 0 0 1 0 7.744"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/></svg></span><span class="nav-text">Operators</span></a>
        <a class="nav-link" href="{{ route('access-logs.index') }}"><span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 8h8"/><path d="M7 12h10"/><path d="M7 16h6"/></svg></span><span class="nav-text">Access logs</span></a>
        <a class="nav-link" href="{{ route('sessions.index') }}"><span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="10" x2="14" y1="2" y2="2"/><line x1="12" x2="15" y1="14" y2="11"/><circle cx="12" cy="14" r="8"/></svg></span><span class="nav-text">Sessions</span></a>
        <a class="nav-link" href="{{ route('mqtt-monitor.index') }}"><span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="8" x="2" y="14" rx="2"/><path d="M6.01 18H6"/><path d="M10.01 18H10"/><path d="M15 10v4"/><path d="M17.84 7.17a4 4 0 0 0-5.66 0"/><path d="M20.66 4.34a8 8 0 0 0-11.31 0"/></svg></span><span class="nav-text">MQTT monitor</span></a>
        <div class="side-spacer"></div>
        <div class="source-box">
            <div class="source-title">DATA SOURCE</div>
            <div class="source-state"><span class="state-dot"></span> Read-only connected</div>
            <div class="source-note">atomic_access database<br>Monitoring mode · no write access</div>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div>
                <div class="eyebrow">Access Tracking and Operational Monitoring</div>
                <h1>System Overview</h1>
                <div class="subtitle">Operational command center · {{ now()->translatedFormat('l, d F Y') }}</div>
            </div>
            <div class="top-meta">
                <div class="live-badge"><span class="state-dot"></span> SYSTEM MONITORING</div>
                <div class="updated">LIVE DATA · AUTO REFRESH 30s</div>
            </div>
        </header>

        <div class="section-heading">
            <h2>Access summary</h2>
            <span class="section-hint">Current records · atomic_access</span>
        </div>
        <section class="metrics">
            <article class="metric">
                <div class="metric-head">Registered devices <span class="metric-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6" rx="1"/><path d="M9 2v2M15 2v2M9 20v2M15 20v2M2 9h2M2 15h2M20 9h2M20 15h2"/></svg></span></div>
                <div class="metric-number">{{ number_format($devices->count()) }}</div>
                <div class="metric-foot"><span class="good">{{ number_format($onlineDevices) }} online</span> · <span class="bad">{{ number_format($offlineDevices) }} offline</span></div>
            </article>
            <article class="metric">
                <div class="metric-head">Active operators <span class="metric-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M16 3.128a4 4 0 0 1 0 7.744"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/></svg></span></div>
                <div class="metric-number">{{ number_format($activeOperators) }}</div>
                <div class="metric-foot">{{ number_format($totalOperators) }} registered operators</div>
            </article>
            <article class="metric">
                <div class="metric-head">Access granted <span class="metric-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg></span></div>
                <div class="metric-number lime">{{ number_format($grantedLogs) }}</div>
                <div class="metric-foot">{{ number_format($totalLogs) }} total access events</div>
            </article>
            <article class="metric">
                <div class="metric-head">Access denied <span class="metric-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg></span></div>
                <div class="metric-number red">{{ number_format($deniedLogs) }}</div>
                <div class="metric-foot">Rejected fingerprint attempts</div>
            </article>
            <article class="metric">
                <div class="metric-head">Today's access <span class="metric-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/></svg></span></div>
                <div class="metric-number">{{ number_format($todayLogs) }}</div>
                <div class="metric-foot"><span class="good">{{ number_format($todayGranted) }} granted</span> · <span class="bad">{{ number_format($todayDenied) }} denied</span></div>
            </article>
            <article class="metric">
                <div class="metric-head">MQTT processed <span class="metric-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="8" x="2" y="14" rx="2"/><path d="M6.01 18H6"/><path d="M10.01 18H10"/><path d="M15 10v4"/><path d="M17.84 7.17a4 4 0 0 0-5.66 0"/><path d="M20.66 4.34a8 8 0 0 0-11.31 0"/></svg></span></div>
                <div class="metric-number cyan">{{ number_format($processedMessages) }}</div>
                <div class="metric-foot">{{ number_format($totalMessages) }} total messages received</div>
            </article>
        </section>

        <div class="section-heading">
            <h2>Activity & device health</h2>
            <span class="section-hint">Last 7 days · WIB</span>
        </div>
        <section class="overview-grid">
            <article class="panel">
                <div class="panel-head">
                    <div>
                        <div class="panel-title">Access activity</div>
                        <div class="panel-description">Daily granted and denied events</div>
                    </div>
                    <div class="legend">
                        <span class="legend-item"><i class="legend-dot" style="background:var(--lime)"></i>Granted</span>
                        <span class="legend-item"><i class="legend-dot" style="background:var(--red)"></i>Denied</span>
                    </div>
                </div>
                <div class="chart">
                    @foreach ($activity as $day)
                        @php($grantedHeight = ($day['granted'] / $maxActivity) * 139)
                        @php($deniedHeight = ($day['denied'] / $maxActivity) * 139)
                        <div class="chart-day" title="{{ $day['date'] }}: {{ $day['total'] }} events">
                            <div class="chart-column">
                                <span class="chart-denied" style="height:{{ $deniedHeight }}px"></span>
                                <span class="chart-granted" style="height:{{ $grantedHeight }}px"></span>
                            </div>
                            <span class="chart-label">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="panel">
                <div class="panel-head">
                    <div>
                        <div class="panel-title">Registered devices</div>
                        <div class="panel-description">{{ number_format($devices->count()) }} device records · <a href="{{ route('devices.index') }}" style="color:var(--cyan);font-weight:800">VIEW ALL →</a></div>
                    </div>
                    <span class="section-hint">FIRMWARE</span>
                </div>
                <div class="device-list">
                    @forelse ($devices as $device)
                        <div class="device-row">
                            <div class="device-main">
                                <div class="device-name"><a href="{{ route('devices.show', $device->device_id) }}" style="text-decoration:none;color:inherit">{{ $device->device_name }}</a></div>
                                <div class="device-detail">{{ $device->device_id }} · FW {{ $device->firmware_version ?? '—' }}</div>
                                <div class="device-detail">Last seen: {{ $device->last_seen ? \Illuminate\Support\Carbon::parse($device->last_seen)->format('d M Y, H:i:s') : 'Never' }}</div>
                            </div>
                            <span class="device-status {{ $device->status === 'online' ? '' : 'offline' }}">{{ $device->status }}</span>
                        </div>
                    @empty
                        <div class="empty">No devices registered.</div>
                    @endforelse
                </div>
            </article>
        </section>

        <div class="section-heading">
            <h2>Recent activity</h2>
            <span class="section-hint">Latest events and message pipeline</span>
        </div>
        <section class="activity-grid">
            <article class="panel">
                <div class="panel-head">
                    <div>
                        <div class="panel-title">Access events</div>
                        <div class="panel-description">{{ number_format($totalLogs) }} total records · latest 8 shown</div>
                    </div>
                    <span class="section-hint">ACCESS LOG</span>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Time</th><th>Operator</th><th>Device</th><th>Result</th><th>Reason</th></tr>
                        </thead>
                        <tbody>
                        @forelse ($recentLogs as $log)
                            <tr>
                                <td>{{ $log->event_at ? \Illuminate\Support\Carbon::parse($log->event_at)->format('d M, H:i:s') : '—' }}</td>
                                <td>{{ $log->operator_name ?: $log->operator_db_id ?: 'Unknown' }}</td>
                                <td>{{ $log->device_id }}</td>
                                <td><span class="result {{ $log->result }}">{{ $log->result }}</span></td>
                                <td class="muted">{{ $log->reason ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty">No access events recorded.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="panel">
                <div class="panel-head">
                    <div>
                        <div class="panel-title">Session & MQTT health</div>
                        <div class="panel-description">Processing overview</div>
                    </div>
                    <span class="section-hint">PIPELINE</span>
                </div>
                <div class="health-pair">
                    <div class="health-box">
                        <div class="health-label">Active sessions</div>
                        <div class="health-number">{{ number_format($activeSessions) }}</div>
                    </div>
                    <div class="health-box">
                        <div class="health-label">Orphaned sessions · operator removed</div>
                        <div class="health-number">{{ number_format($orphanedSessions) }}</div>
                    </div>
                    <div class="health-box">
                        <div class="health-label">Interrupted sessions</div>
                        <div class="health-number">{{ number_format($interruptedSessions) }}</div>
                    </div>
                    <div class="health-box">
                        <div class="health-label">Completed sessions</div>
                        <div class="health-number">{{ number_format($completedSessions) }}</div>
                    </div>
                </div>
                <div class="health-label">Average session duration</div>
                <div class="health-number" style="font-size:17px;margin-top:5px">{{ $averageDurationFormatted }}</div>

                @php($mqttRate = $totalMessages > 0 ? min(100, ($processedMessages / $totalMessages) * 100) : 0)
                <div class="subsection"><strong>MQTT processing</strong><span>{{ number_format($totalMessages) }} messages</span></div>
                <div class="progress-track"><div class="progress-fill" style="width:{{ $mqttRate }}%"></div></div>
                <div class="progress-caption">
                    <span>{{ number_format($processedMessages) }} processed</span>
                    <span>{{ number_format($mqttRate, 1) }}%</span>
                </div>

                <div class="subsection"><strong>Recent MQTT messages</strong><span>Latest 6</span></div>
                @forelse ($recentMessages as $message)
                    <div class="mqtt-row">
                        <div style="min-width:0">
                            <div class="mqtt-topic">{{ \Illuminate\Support\Str::limit($message->topic, 38) }}</div>
                            <div class="mqtt-meta">{{ $message->device_id }} · {{ $message->received_at ? \Illuminate\Support\Carbon::parse($message->received_at, 'UTC')->setTimezone(config('app.timezone'))->format('d M H:i:s') : '—' }} WIB</div>
                        </div>
                        <span class="mqtt-status {{ $message->processing_status }}">{{ $message->processing_status }}</span>
                    </div>
                @empty
                    <div class="empty">No MQTT messages recorded.</div>
                @endforelse
            </article>
        </section>

        <footer>
            <span>A.T.O.M. · Access Tracking and Operational Monitoring</span>
            <span>Source: atomic_access · Read-only monitoring · Asia/Jakarta</span>
        </footer>
    </main>
</div>
<script>
    // Read-only dashboard refresh. No source database write operations are performed.
    window.setInterval(() => {
        if (!document.hidden) window.location.reload();
    }, 30000);
</script>
</body>
</html>
