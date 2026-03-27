<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'FinTrack — Income Dashboard')</title>

    {{-- SVG Favicon — inline data URI, no CDN needed --}}
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%2300d68f'/><path d='M8 22 L13 15 L18 18 L24 10' stroke='white' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round' fill='none'/><circle cx='24' cy='10' r='2' fill='white'/></svg>" />

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    {{-- Font Awesome Icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        /* =============================================
           THEME VARIABLES — DARK (default)
        ============================================= */
        [data-theme="dark"] {
            --bg-primary:    #0b0f1a;
            --bg-secondary:  #111827;
            --bg-card:       #161d2e;
            --bg-card-hover: #1c2540;
            --topbar-bg:     rgba(11,15,26,0.88);
            --accent-green:  #00d68f;
            --accent-blue:   #4f8ef7;
            --accent-purple: #8b5cf6;
            --accent-red:    #f87171;
            --accent-yellow: #fbbf24;
            --text-primary:  #f0f4ff;
            --text-muted:    #6b7a99;
            --text-dim:      #3d4966;
            --border-color:  #1e2a42;
            --shadow:        0 4px 24px rgba(0,0,0,0.45);
            --input-bg:      #0b0f1a;
            --progress-bg:   #0b0f1a;
            --toggle-track:  #1e2a42;
            --toggle-thumb:  #6b7a99;
            --scrollbar-track: #0b0f1a;
            --scrollbar-thumb: #1e2a42;
            --table-row:     #161d2e;
            --table-hover:   #1c2540;
        }

        /* =============================================
           THEME VARIABLES — LIGHT
        ============================================= */
        [data-theme="light"] {
            --bg-primary:    #f0f4f8;
            --bg-secondary:  #ffffff;
            --bg-card:       #ffffff;
            --bg-card-hover: #f5f8ff;
            --topbar-bg:     rgba(255,255,255,0.92);
            --accent-green:  #00b377;
            --accent-blue:   #2f72e8;
            --accent-purple: #7c3aed;
            --accent-red:    #dc4545;
            --accent-yellow: #d97706;
            --text-primary:  #0f172a;
            --text-muted:    #475569;
            --text-dim:      #94a3b8;
            --border-color:  #e2e8f0;
            --shadow:        0 4px 24px rgba(0,0,0,0.08);
            --input-bg:      #f8fafc;
            --progress-bg:   #e2e8f0;
            --toggle-track:  #cbd5e1;
            --toggle-thumb:  #ffffff;
            --scrollbar-track: #f0f4f8;
            --scrollbar-thumb: #cbd5e1;
            --table-row:     #ffffff;
            --table-hover:   #f8fafc;
        }

        /* =============================================
           BASE RESET
        ============================================= */
        :root {
            --sidebar-width: 260px;
            --radius-lg:     16px;
            --radius-md:     10px;
            --radius-sm:     6px;
            --transition:    0.25s ease;
            --font-main:     'Sora', sans-serif;
            --font-mono:     'JetBrains Mono', monospace;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-main);
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background 0.35s ease, color 0.35s ease;
        }

        /* =============================================
           DARK / LIGHT TOGGLE BUTTON
        ============================================= */
        .theme-toggle {
            position: relative;
            width: 52px;
            height: 28px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .theme-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }

        .toggle-track {
            position: absolute;
            inset: 0;
            background: var(--toggle-track);
            border-radius: 20px;
            border: 1px solid var(--border-color);
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 5px;
        }

        .toggle-track .t-icon {
            font-size: 11px;
            line-height: 1;
            transition: opacity 0.3s;
        }

        .toggle-track .t-moon { color: #a5b4fc; }
        .toggle-track .t-sun  { color: #fbbf24; }

        .toggle-thumb {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            background: var(--accent-green);
            border-radius: 50%;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1), background 0.3s;
            box-shadow: 0 1px 4px rgba(0,0,0,0.3);
        }

        [data-theme="light"] .toggle-thumb {
            transform: translateX(24px);
            background: var(--accent-yellow);
        }

        /* =============================================
           SIDEBAR
        ============================================= */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform var(--transition), background 0.35s, border-color 0.35s;
        }

        .sidebar-brand {
            padding: 24px 20px 18px;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-brand .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        /* SVG icon brand mark */
        .brand-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .brand-icon svg { width: 40px; height: 40px; }

        .brand-text {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.3px;
        }

        .brand-sub {
            font-size: 10px;
            color: var(--text-muted);
            font-family: var(--font-mono);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 10px;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-dim);
            padding: 8px 12px 6px;
            margin-top: 6px;
        }

        .nav-item { margin: 2px 0; }

        .nav-link-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all var(--transition);
            position: relative;
            overflow: hidden;
        }

        .nav-link-item::before {
            content: '';
            position: absolute;
            left: 0; top: 25%; bottom: 25%;
            width: 3px;
            background: var(--accent-green);
            border-radius: 0 3px 3px 0;
            transform: scaleY(0);
            transition: transform var(--transition);
        }

        .nav-link-item:hover { background: var(--bg-card); color: var(--text-primary); }

        .nav-link-item.active {
            background: rgba(0, 214, 143, 0.1);
            color: var(--accent-green);
            font-weight: 600;
        }

        [data-theme="light"] .nav-link-item.active {
            background: rgba(0, 179, 119, 0.1);
        }

        .nav-link-item.active::before { transform: scaleY(1); }

        .nav-link-item .nav-icon {
            width: 20px;
            text-align: center;
            font-size: 14px;
        }

        .sidebar-footer {
            padding: 14px 18px;
            border-top: 1px solid var(--border-color);
        }

        .sidebar-footer small {
            font-size: 10px;
            color: var(--text-dim);
            font-family: var(--font-mono);
        }

        /* =============================================
           MAIN WRAPPER
        ============================================= */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* =============================================
           TOPBAR
        ============================================= */
        .topbar {
            position: sticky;
            top: 0;
            background: var(--topbar-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            padding: 0 28px;
            height: 62px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 900;
            transition: background 0.35s, border-color 0.35s;
        }

        .topbar-left .page-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .topbar-left .page-subtitle {
            font-size: 11.5px;
            color: var(--text-muted);
            font-family: var(--font-mono);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topbar-date {
            font-size: 11.5px;
            color: var(--text-muted);
            font-family: var(--font-mono);
        }

        .topbar-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple));
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
            cursor: pointer;
            flex-shrink: 0;
            user-select: none;
        }

        /* ── User Dropdown ── */
        .user-menu-wrap { position: relative; }

        .user-menu-trigger {
            display: flex; align-items: center; gap: 9px;
            cursor: pointer;
            padding: 4px 8px 4px 4px;
            border-radius: 30px;
            border: 1px solid transparent;
            transition: all var(--transition);
        }
        .user-menu-trigger:hover {
            background: var(--bg-card);
            border-color: var(--border-color);
        }
        .user-menu-trigger .user-name {
            font-size: 12.5px; font-weight: 600;
            color: var(--text-primary);
            max-width: 120px; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis;
        }
        .user-menu-trigger .chevron {
            color: var(--text-muted);
            transition: transform 0.25s ease;
            flex-shrink: 0;
        }
        .user-menu-wrap.open .chevron { transform: rotate(180deg); }

        .user-dropdown {
            position: absolute;
            top: calc(100% + 10px); right: 0;
            width: 220px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 6px;
            z-index: 9999;
            opacity: 0;
            transform: translateY(-8px) scale(0.97);
            pointer-events: none;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .user-menu-wrap.open .user-dropdown {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: all;
        }
        .user-dropdown-header {
            padding: 10px 12px 9px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 5px;
        }
        .user-dropdown-header .ud-name {
            font-size: 13px; font-weight: 700;
            color: var(--text-primary);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .user-dropdown-header .ud-email {
            font-size: 11px; color: var(--text-muted);
            font-family: var(--font-mono);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            margin-top: 2px;
        }
        .ud-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-muted); text-decoration: none;
            font-size: 13px; font-weight: 500;
            transition: all var(--transition);
            cursor: pointer; width: 100%;
            background: none; border: none;
            text-align: left; font-family: var(--font-main);
        }
        .ud-item:hover { background: var(--bg-card-hover); color: var(--text-primary); }
        .ud-divider { height: 1px; background: var(--border-color); margin: 4px 0; }
        .ud-item.logout { color: var(--accent-red); }
        .ud-item.logout:hover { background: rgba(248,113,113,0.08); color: var(--accent-red); }

        .sidebar-toggle {
            display: none;
            background: none;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            border-radius: var(--radius-sm);
            padding: 6px 10px;
            cursor: pointer;
            font-size: 15px;
            margin-right: 10px;
        }

        /* =============================================
           PAGE CONTENT
        ============================================= */
        .page-content {
            flex: 1;
            padding: 28px;
            animation: fadeInUp 0.35s ease;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* =============================================
           CARDS
        ============================================= */
        .card-custom {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 22px;
            transition: all var(--transition);
        }

        .card-custom:hover {
            border-color: var(--accent-blue);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* =============================================
           STAT CARDS
        ============================================= */
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 20px 22px;
            transition: all var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 80px; height: 80px;
            border-radius: 50%;
            opacity: 0.07;
            transform: translate(20px,-20px);
        }

        .stat-card.green::after  { background: var(--accent-green); }
        .stat-card.blue::after   { background: var(--accent-blue); }
        .stat-card.purple::after { background: var(--accent-purple); }
        .stat-card.red::after    { background: var(--accent-red); }

        .stat-card:hover { border-color: #2a3655; transform: translateY(-3px); box-shadow: var(--shadow); }

        [data-theme="light"] .stat-card:hover { border-color: #bfdbfe; }

        .stat-icon {
            width: 40px; height: 40px;
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            font-size: 17px;
            margin-bottom: 14px;
        }

        .stat-icon.green  { background: rgba(0,214,143,.15);  color: var(--accent-green); }
        .stat-icon.blue   { background: rgba(79,142,247,.15); color: var(--accent-blue); }
        .stat-icon.purple { background: rgba(139,92,246,.15); color: var(--accent-purple); }
        .stat-icon.red    { background: rgba(248,113,113,.15);color: var(--accent-red); }
        .stat-icon.yellow { background: rgba(251,191,36,.15); color: var(--accent-yellow); }

        .stat-label {
            font-size: 11px;
            color: var(--text-muted);
            font-family: var(--font-mono);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.5px;
            font-family: var(--font-mono);
        }

        .stat-value.green  { color: var(--accent-green); }
        .stat-value.red    { color: var(--accent-red); }

        .stat-sub { font-size: 11.5px; color: var(--text-muted); margin-top: 4px; }

        /* =============================================
           BUTTONS
        ============================================= */
        .btn-primary-custom {
            background: var(--accent-green);
            color: #0b0f1a;
            border: none;
            border-radius: var(--radius-md);
            padding: 10px 22px;
            font-size: 13.5px;
            font-weight: 600;
            font-family: var(--font-main);
            cursor: pointer;
            transition: all var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary-custom:hover {
            background: #00f5a4;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0,214,143,.3);
            color: #0b0f1a;
        }

        .btn-outline-custom {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 9px 18px;
            font-size: 13.5px;
            font-weight: 500;
            font-family: var(--font-main);
            cursor: pointer;
            transition: all var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-outline-custom:hover { border-color: var(--accent-blue); color: var(--accent-blue); }

        .btn-danger-custom {
            background: rgba(248,113,113,.12);
            color: var(--accent-red);
            border: 1px solid rgba(248,113,113,.2);
            border-radius: var(--radius-sm);
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
            font-family: var(--font-main);
            cursor: pointer;
            transition: all var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-danger-custom:hover { background: rgba(248,113,113,.22); color: var(--accent-red); }

        .btn-edit-custom {
            background: rgba(79,142,247,.12);
            color: var(--accent-blue);
            border: 1px solid rgba(79,142,247,.2);
            border-radius: var(--radius-sm);
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 500;
            font-family: var(--font-main);
            cursor: pointer;
            transition: all var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit-custom:hover { background: rgba(79,142,247,.22); color: var(--accent-blue); }

        /* =============================================
           FORM STYLES
        ============================================= */
        .form-label-custom {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 7px;
            display: block;
            font-family: var(--font-mono);
        }

        .form-control-custom {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 11px 15px;
            font-size: 14px;
            color: var(--text-primary);
            font-family: var(--font-main);
            transition: all var(--transition);
            outline: none;
            -webkit-appearance: none;
        }

        .form-control-custom:focus {
            border-color: var(--accent-green);
            box-shadow: 0 0 0 3px rgba(0,214,143,.1);
        }

        .form-control-custom::placeholder { color: var(--text-dim); }

        /* Native date picker styling */
        input[type="date"].form-control-custom::-webkit-calendar-picker-indicator {
            filter: invert(0.5) sepia(1) saturate(3) hue-rotate(100deg);
            cursor: pointer;
            opacity: 0.7;
        }

        [data-theme="light"] input[type="date"].form-control-custom::-webkit-calendar-picker-indicator {
            filter: none;
            opacity: 0.6;
        }

        .input-prefix { position: relative; }

        .input-prefix .prefix-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 13px;
            font-family: var(--font-mono);
            pointer-events: none;
        }

        .input-prefix .form-control-custom { padding-left: 34px; }

        /* =============================================
           TABLE
        ============================================= */
        .table-custom {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 4px;
        }

        .table-custom thead th {
            font-size: 10.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            padding: 10px 14px;
            border-bottom: 1px solid var(--border-color);
            font-family: var(--font-mono);
            white-space: nowrap;
        }

        .table-custom tbody tr { background: var(--table-row); transition: background var(--transition); }
        .table-custom tbody tr:hover { background: var(--table-hover); }

        .table-custom tbody td {
            padding: 12px 14px;
            font-size: 13px;
            color: var(--text-primary);
            vertical-align: middle;
            border-top: 1px solid var(--border-color);
        }

        .table-custom tbody td:first-child { border-radius: var(--radius-md) 0 0 var(--radius-md); }
        .table-custom tbody td:last-child  { border-radius: 0 var(--radius-md) var(--radius-md) 0; }

        /* Clickable row highlight */
        .table-custom tbody tr.clickable-row { cursor: pointer; }
        .table-custom tbody tr.clickable-row:hover { background: var(--bg-card-hover); }

        /* =============================================
           ALERTS
        ============================================= */
        .alert-custom {
            padding: 13px 18px;
            border-radius: var(--radius-md);
            font-size: 13.5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: rgba(0,214,143,.1);
            border: 1px solid rgba(0,214,143,.22);
            color: var(--accent-green);
        }

        .alert-error {
            background: rgba(248,113,113,.1);
            border: 1px solid rgba(248,113,113,.22);
            color: var(--accent-red);
        }

        /* =============================================
           BADGE
        ============================================= */
        .badge-custom {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 600;
            font-family: var(--font-mono);
        }

        .badge-green { background: rgba(0,214,143,.13); color: var(--accent-green); }
        .badge-red   { background: rgba(248,113,113,.13); color: var(--accent-red); }

        /* =============================================
           SECTION HEADER
        ============================================= */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .section-title { font-size: 15px; font-weight: 600; color: var(--text-primary); }
        .section-sub { font-size: 11.5px; color: var(--text-muted); font-family: var(--font-mono); margin-top: 2px; }

        /* =============================================
           PROGRESS BAR
        ============================================= */
        .progress-custom {
            background: var(--progress-bg);
            border-radius: 20px;
            height: 6px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 20px;
            transition: width 0.8s cubic-bezier(.4,0,.2,1);
        }

        /* =============================================
           EMPTY STATE
        ============================================= */
        .empty-state { text-align: center; padding: 56px 20px; }
        .empty-icon { font-size: 44px; color: var(--text-dim); margin-bottom: 14px; }
        .empty-title { font-size: 17px; font-weight: 600; color: var(--text-muted); margin-bottom: 8px; }
        .empty-sub { font-size: 13px; color: var(--text-dim); margin-bottom: 22px; }

        /* =============================================
           RESPONSIVE
        ============================================= */
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .sidebar-toggle { display: inline-flex; align-items: center; }
            .page-content { padding: 18px 14px; }
        }

        @media (max-width: 576px) {
            .stat-value { font-size: 19px; }
            .topbar { padding: 0 14px; }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 999;
        }

        .sidebar-overlay.active { display: block; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: var(--scrollbar-track); }
        ::-webkit-scrollbar-thumb { background: var(--scrollbar-thumb); border-radius: 3px; }
    </style>
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    {{-- ======================== SIDEBAR ======================== --}}
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}" class="brand-logo">
                {{-- Inline SVG brand icon — no CDN --}}
                <div class="brand-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40">
                        <rect width="40" height="40" rx="10" fill="url(#bg)"/>
                        <defs>
                            <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#00d68f"/>
                                <stop offset="100%" stop-color="#4f8ef7"/>
                            </linearGradient>
                        </defs>
                        <polyline points="7,28 14,18 21,22 29,12" fill="none" stroke="white" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="29" cy="12" r="2.5" fill="white"/>
                        <line x1="7" y1="31" x2="33" y2="31" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <div class="brand-text">FinTrack</div>
                    <div class="brand-sub">Income Dashboard</div>
                </div>
            </a>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section-label">Main</div>
            <div class="nav-item">
                <a href="{{ route('dashboard') }}"
                   class="nav-link-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M2 2h5v5H2zm0 7h5v5H2zm7-7h5v5H9zm0 7h5v5H9z" opacity=".85"/>
                        </svg>
                    </span>
                    Dashboard
                </a>
            </div>

            <div class="nav-section-label">Records</div>

            <div class="nav-item">
                <a href="{{ route('income.create') }}"
                   class="nav-link-item {{ request()->routeIs('income.create') || request()->routeIs('income.edit') ? 'active' : '' }}">
                    <span class="nav-icon">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 1a7 7 0 100 14A7 7 0 008 1zm1 10H7V9H5V7h2V5h2v2h2v2H9v2z"/>
                        </svg>
                    </span>
                    Add Monthly Data
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('history') }}"
                   class="nav-link-item {{ request()->routeIs('history') || request()->routeIs('income.show') ? 'active' : '' }}">
                    <span class="nav-icon">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 1a7 7 0 110 14A7 7 0 018 1zm.75 3.5h-1.5v4.25l3.25 1.95.75-1.25-2.5-1.5V4.5z"/>
                        </svg>
                    </span>
                    History
                </a>
            </div>
        </div>

        <div class="sidebar-footer">
            <small>js_education · Laravel {{ app()->version() }}</small>
        </div>
    </nav>

    {{-- ======================== MAIN WRAPPER ======================== --}}
    <div class="main-wrapper">

        {{-- TOPBAR --}}
        <header class="topbar">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="currentColor">
                        <rect x="1" y="3" width="16" height="2" rx="1"/>
                        <rect x="1" y="8" width="16" height="2" rx="1"/>
                        <rect x="1" y="13" width="16" height="2" rx="1"/>
                    </svg>
                </button>
                <div class="topbar-left">
                    <div class="page-title">@yield('page-title', 'Dashboard')</div>
                    <div class="page-subtitle">@yield('page-subtitle', 'Income Tracking System')</div>
                </div>
            </div>

            <div class="topbar-right">
                <span class="topbar-date d-none d-md-block" id="currentDate"></span>

                {{-- Dark / Light Mode Toggle --}}
                <label class="theme-toggle" title="Toggle dark/light mode">
                    <input type="checkbox" id="themeToggle" onchange="toggleTheme(this)"/>
                    <div class="toggle-track">
                        <span class="t-icon t-moon">
                            <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor">
                                <path d="M9 6.5A4 4 0 014.5 1a4.5 4.5 0 100 9A4 4 0 019 6.5z"/>
                            </svg>
                        </span>
                        <span class="t-icon t-sun">
                            <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor">
                                <circle cx="5" cy="5" r="2"/>
                                <line x1="5" y1="1" x2="5" y2="0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                                <line x1="5" y1="9" x2="5" y2="10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                                <line x1="1" y1="5" x2="0" y2="5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                                <line x1="9" y1="5" x2="10" y2="5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                            </svg>
                        </span>
                    </div>
                    <div class="toggle-thumb"></div>
                </label>

                {{-- User Dropdown --}}
                <div class="user-menu-wrap" id="userMenuWrap">
                    <div class="user-menu-trigger" onclick="toggleUserMenu()" id="userMenuTrigger">
                        <div class="topbar-avatar" title="{{ Auth::user()->name }}">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', trim(Auth::user()->name))[1] ?? '', 0, 1)) }}
                        </div>
                        <span class="user-name d-none d-md-block">{{ Auth::user()->name }}</span>
                        <svg class="chevron" width="13" height="13" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M2 4l4 4 4-4"/>
                        </svg>
                    </div>

                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-dropdown-header">
                            <div class="ud-name">{{ Auth::user()->name }}</div>
                            <div class="ud-email">{{ Auth::user()->email }}</div>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="ud-item">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8 8a3 3 0 100-6 3 3 0 000 6zm-5 6a5 5 0 0110 0H3z"/>
                            </svg>
                            My Profile
                        </a>

                        <a href="{{ route('dashboard') }}" class="ud-item">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M2 2h5v5H2zm0 7h5v5H2zm7-7h5v5H9zm0 7h5v5H9z" opacity=".85"/>
                            </svg>
                            Dashboard
                        </a>

                        <div class="ud-divider"></div>

                        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="ud-item logout">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M6 2H3a1 1 0 00-1 1v10a1 1 0 001 1h3M10 11l3-3-3-3M7 8h6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="page-content">
            @if(session('success'))
                <div class="alert-custom alert-success">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 0a8 8 0 100 16A8 8 0 008 0zm3.78 6.28l-4.5 4.5a.75.75 0 01-1.06 0l-2-2a.75.75 0 011.06-1.06L6.75 9.19l3.97-3.97a.75.75 0 011.06 1.06z"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert-custom alert-error">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 0a8 8 0 100 16A8 8 0 008 0zm.75 4v4.75h-1.5V4h1.5zm0 6.5v1.5h-1.5v-1.5h1.5z"/>
                    </svg>
                    <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ─── User Dropdown ────────────────────────────
        function toggleUserMenu() {
            document.getElementById('userMenuWrap').classList.toggle('open');
        }
        // Close when clicking outside
        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('userMenuWrap');
            if (wrap && !wrap.contains(e.target)) {
                wrap.classList.remove('open');
            }
        });

        // ─── Date in topbar ───────────────────────────────
        const dateEl = document.getElementById('currentDate');
        if (dateEl) {
            dateEl.textContent = new Date().toLocaleDateString('en-IN', {
                weekday: 'short', year: 'numeric', month: 'short', day: 'numeric'
            });
        }

        // ─── Sidebar toggle ───────────────────────────────
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }

        // ─── Theme toggle (Dark / Light) ──────────────────
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('fintrack-theme', theme);
            // Sync checkbox state
            const cb = document.getElementById('themeToggle');
            if (cb) cb.checked = (theme === 'light');
        }

        function toggleTheme(checkbox) {
            applyTheme(checkbox.checked ? 'light' : 'dark');
        }

        // On page load — restore saved theme
        (function() {
            const saved = localStorage.getItem('fintrack-theme') || 'dark';
            applyTheme(saved);
        })();

        // ─── Auto-dismiss alerts ──────────────────────────
        setTimeout(() => {
            document.querySelectorAll('.alert-custom').forEach(el => {
                el.style.transition = 'opacity 0.4s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 400);
            });
        }, 4000);
    </script>

    @yield('scripts')

</body>
</html>