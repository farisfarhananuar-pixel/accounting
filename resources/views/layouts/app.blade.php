<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Account Easy</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --green-darkest: #083929;
            --green-dark: #0d4f3c;
            --green-main: #1a7a57;
            --green-mid: #22a06b;
            --green-light: #4cde9e;
            --green-pale: #e8f8f1;
            --green-paler: #f0fdf9;

            --sidebar-width: 260px;
            --header-height: 65px;
            --accent-blue: #0369a1;
            --accent-amber: #d97706;
            --accent-red: #dc2626;
            --accent-purple: #7c3aed;
            --accent-teal: #0ea5a0;
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            color: #1f2937;
            min-height: 100vh;
        }

        /* =========== SIDEBAR =========== */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--green-darkest) 0%, var(--green-dark) 40%, #0f6348 100%);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius:4px; }

        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--green-light), var(--green-mid));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .sidebar-brand-text h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
            line-height: 1.1;
        }

        .sidebar-brand-text span {
            font-size: 0.65rem;
            color: rgba(255,255,255,0.6);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* User info in sidebar */
        .sidebar-user {
            padding: 14px 20px;
            background: rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--green-light), var(--accent-teal));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--green-dark);
            flex-shrink: 0;
        }

        .user-info h6 {
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            margin-bottom: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 160px;
        }

        .user-role-badge {
            font-size: 0.62rem;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        /* Company badge */
        .company-badge {
            padding: 10px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .company-badge .badge-inner {
            background: rgba(76,222,158,0.12);
            border: 1px solid rgba(76,222,158,0.25);
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 0.72rem;
            color: rgba(255,255,255,0.85);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .company-badge .badge-inner i { color: var(--green-light); }

        /* Navigation */
        .sidebar-nav {
            flex: 1;
            padding: 10px 0;
        }

        .nav-section-title {
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            padding: 10px 20px 5px;
        }

        .nav-item-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 0.83rem;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            position: relative;
        }

        .nav-item-link:hover {
            color: white;
            background: rgba(255,255,255,0.07);
        }

        .nav-item-link.active {
            color: var(--green-light);
            background: rgba(76,222,158,0.1);
            border-left-color: var(--green-light);
            font-weight: 600;
        }

        .nav-item-link .nav-icon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            background: rgba(255,255,255,0.08);
            flex-shrink: 0;
            transition: background 0.2s;
        }

        .nav-item-link.active .nav-icon,
        .nav-item-link:hover .nav-icon {
            background: rgba(76,222,158,0.2);
        }

        .nav-badge {
            margin-left: auto;
            background: var(--accent-red);
            color: white;
            font-size: 0.6rem;
            padding: 2px 6px;
            border-radius: 20px;
            font-weight: 700;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 14px 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 9px 14px;
            background: rgba(220,38,38,0.15);
            border: 1px solid rgba(220,38,38,0.25);
            border-radius: 9px;
            color: #fca5a5;
            font-size: 0.83rem;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: rgba(220,38,38,0.25);
            color: white;
        }

        /* =========== MAIN CONTENT =========== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        /* Top Header */
        .top-header {
            position: sticky;
            top: 0;
            z-index: 900;
            height: var(--header-height);
            background: white;
            box-shadow: 0 1px 10px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .toggle-sidebar-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            color: #6b7280;
            font-size: 1.1rem;
            display: none;
        }

        .page-title h5 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--green-dark);
            margin: 0;
        }

        .page-title p {
            font-size: 0.72rem;
            color: #9ca3af;
            margin: 0;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-btn {
            position: relative;
            background: none;
            border: none;
            width: 38px;
            height: 38px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1rem;
        }

        .header-btn:hover {
            background: var(--green-pale);
            color: var(--green-main);
        }

        .header-btn .badge-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: var(--accent-red);
            border-radius: 50%;
            border: 2px solid white;
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 5px 12px 5px 5px;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .header-user:hover { background: var(--green-pale); }

        .header-avatar {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--green-main), var(--green-dark));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            color: white;
        }

        .header-user-info { line-height: 1.2; }
        .header-user-info .name { font-size: 0.82rem; font-weight: 600; color: #1f2937; }
        .header-user-info .role { font-size: 0.7rem; color: #9ca3af; }

        /* Notification dropdown */
        .notif-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 320px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            border: 1px solid #e5e7eb;
            z-index: 9999;
            display: none;
        }
        .notif-dropdown.show { display: block; }
        .notif-header { padding: 14px 18px; border-bottom: 1px solid #f3f4f6; display:flex; justify-content:space-between; align-items:center; }
        .notif-header h6 { margin:0; font-size:.85rem; font-weight:700; color:var(--green-dark); }
        .notif-item { padding: 12px 18px; border-bottom: 1px solid #f9fafb; display:flex; gap:12px; align-items:flex-start; transition:background .15s; }
        .notif-item:hover { background:#f9fafb; }
        .notif-item:last-child { border-bottom: none; }
        .notif-icon { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.85rem; flex-shrink:0; }
        .notif-text { font-size:.78rem; color:#374151; line-height:1.4; }
        .notif-time { font-size:.7rem; color:#9ca3af; margin-top:2px; }
        .notif-empty { padding:28px; text-align:center; color:#9ca3af; font-size:.82rem; }

        /* Profile dropdown */
        .profile-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 220px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            border: 1px solid #e5e7eb;
            z-index: 9999;
            display: none;
        }
        .profile-dropdown.show { display: block; }
        .profile-dropdown-header { padding:16px; border-bottom:1px solid #f3f4f6; text-align:center; }
        .profile-dropdown-avatar { width:56px; height:56px; border-radius:12px; margin:0 auto 8px; overflow:hidden; }
        .profile-dropdown-avatar img { width:100%; height:100%; object-fit:cover; }
        .profile-dropdown-avatar .initials { width:100%; height:100%; background:linear-gradient(135deg,var(--green-main),var(--green-dark)); display:flex; align-items:center; justify-content:center; font-size:1.1rem; font-weight:700; color:white; }
        .profile-dropdown-name { font-size:.85rem; font-weight:700; color:#1f2937; }
        .profile-dropdown-role { font-size:.72rem; color:#9ca3af; }
        .profile-dropdown-item { padding:10px 16px; display:flex; align-items:center; gap:10px; font-size:.82rem; color:#374151; text-decoration:none; transition:background .15s; cursor:pointer; border:none; background:none; width:100%; text-align:left; font-family:'Poppins',sans-serif; }
        .profile-dropdown-item:hover { background:#f9fafb; color:var(--green-dark); }
        .profile-dropdown-item i { width:18px; color:#9ca3af; }
        .profile-dropdown-item:hover i { color:var(--green-main); }
        .profile-dropdown-divider { height:1px; background:#f3f4f6; margin:4px 0; }

        /* Header avatar with photo */
        .header-avatar img { width:100%; height:100%; object-fit:cover; border-radius:8px; }
        .sidebar-user-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }

        /* =========== PAGE CONTENT =========== */
        .page-content {
            flex: 1;
            padding: 24px;
        }

        /* =========== CARDS =========== */
        .stat-card {
            background: white;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 14px;
        }

        .stat-value {
            font-size: 1.7rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 500;
        }

        .stat-change {
            font-size: 0.72rem;
            margin-top: 8px;
            font-weight: 600;
        }

        /* Color variants */
        .bg-green-soft { background: var(--green-pale); color: var(--green-main); }
        .bg-amber-soft { background: #fef3c7; color: var(--accent-amber); }
        .bg-red-soft { background: #fef2f2; color: var(--accent-red); }
        .bg-blue-soft { background: #eff6ff; color: var(--accent-blue); }
        .bg-purple-soft { background: #f5f3ff; color: var(--accent-purple); }
        .bg-teal-soft { background: #f0fdf4; color: var(--accent-teal); }

        .text-green { color: var(--green-main); }
        .text-amber { color: var(--accent-amber); }
        .text-red { color: var(--accent-red); }

        /* Chart card */
        .chart-card {
            background: white;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
            height: 100%;
        }

        .chart-card .card-header-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .chart-card .card-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--green-dark);
        }

        /* Table styles */
        .data-table {
            background: white;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .data-table .table-header {
            padding: 18px 22px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .data-table .table-header h6 {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--green-dark);
            margin: 0;
        }

        .table { margin: 0; }
        .table th {
            background: #f9fafb;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            padding: 11px 16px;
        }
        .table td {
            font-size: 0.83rem;
            padding: 11px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }
        .table tr:last-child td { border-bottom: none; }
        .table tr:hover td { background: #fafffe; }

        /* Buttons */
        .btn-green { background: var(--green-main); color: white; border: none; border-radius: 8px; font-family:'Poppins',sans-serif; font-weight:600; transition:all 0.2s; }
        .btn-green:hover { background: var(--green-dark); color: white; transform: translateY(-1px); }
        .btn-outline-green { border: 2px solid var(--green-main); color: var(--green-main); background: transparent; border-radius: 8px; font-family:'Poppins',sans-serif; font-weight:600; transition:all 0.2s; }
        .btn-outline-green:hover { background: var(--green-main); color: white; }

        /* Status badges */
        .badge-approved { background:#d1fae5; color:#065f46; }
        .badge-pending { background:#fef3c7; color:#92400e; }
        .badge-rejected { background:#fee2e2; color:#991b1b; }
        .badge-draft { background:#f3f4f6; color:#374151; }
        .badge-paid { background:#dbeafe; color:#1e40af; }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        /* =========== MOBILE =========== */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-overlay.show { display: block; }
            .main-wrapper { margin-left: 0; }
            .toggle-sidebar-btn { display: flex; }
        }

        @media (max-width: 576px) {
            .page-content { padding: 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- ====== SIDEBAR ====== -->
    <aside class="sidebar" id="mainSidebar">

        <!-- Brand -->
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">💼</div>
            <div class="sidebar-brand-text">
                <h3>Account<span style="color:var(--green-light)">Easy</span></h3>
                <span>Accounting System</span>
            </div>
        </div>

        <!-- User Info -->
        <div class="sidebar-user">
            <div class="user-avatar" style="overflow:hidden">
                @if(auth()->user()->profile_photo)
                    <img src="{{ auth()->user()->profile_photo }}" alt="avatar" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                @endif
            </div>
            <div class="user-info">
                <h6>{{ auth()->user()->name }}</h6>
                @php
                    $roleColors = [
                        'admin' => 'background:#fee2e2;color:#991b1b',
                        'manager' => 'background:#fef3c7;color:#92400e',
                        'executive_accountant' => 'background:#d1fae5;color:#065f46',
                        'auditor' => 'background:#dbeafe;color:#1e40af',
                    ];
                    $roleStyle = $roleColors[auth()->user()->role] ?? 'background:#f3f4f6;color:#374151';
                @endphp
                <span class="user-role-badge" style="{{ $roleStyle }}">{{ auth()->user()->role_label }}</span>
            </div>
        </div>

        <!-- Company Badge -->
        <div class="company-badge">
            <div class="badge-inner">
                <i class="fas fa-building"></i>
                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ auth()->user()->company->name }}</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            @yield('sidebar_nav')
        </nav>

        <!-- Footer -->
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ====== MAIN WRAPPER ====== -->
    <div class="main-wrapper">

        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="toggle-sidebar-btn" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="page-title">
                    <h5>@yield('page_title', 'Dashboard')</h5>
                    <p>@yield('page_subtitle', date('l, d F Y'))</p>
                </div>
            </div>
            <div class="header-right">
                <!-- Notification Bell -->
                <div style="position:relative">
                    <button class="header-btn" id="notifBtn" title="Notifications" onclick="toggleNotif(event)">
                        <i class="fas fa-bell"></i>
                        <span class="badge-dot" id="notifDot"></span>
                    </button>
                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <h6><i class="fas fa-bell me-2"></i>Notifications</h6>
                            <span style="font-size:.72rem;color:var(--green-main);cursor:pointer" onclick="markAllRead()">Mark all read</span>
                        </div>
                        <div id="notifList">
                            <div class="notif-empty"><i class="fas fa-check-circle fa-2x d-block mb-2" style="color:#d1fae5"></i>All caught up!</div>
                        </div>
                    </div>
                </div>

                <!-- Profile -->
                <div style="position:relative">
                    <div class="header-user" id="profileBtn" onclick="toggleProfile(event)">
                        <div class="header-avatar" style="overflow:hidden">
                            @if(auth()->user()->profile_photo)
                                <img src="{{ auth()->user()->profile_photo }}" alt="avatar">
                            @else
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            @endif
                        </div>
                        <div class="header-user-info d-none d-sm-block">
                            <div class="name">{{ explode(' ', auth()->user()->name)[0] }}</div>
                            <div class="role">{{ auth()->user()->role_label }}</div>
                        </div>
                    </div>
                    <div class="profile-dropdown" id="profileDropdown">
                        <div class="profile-dropdown-header">
                            <div class="profile-dropdown-avatar">
                                @if(auth()->user()->profile_photo)
                                    <img src="{{ auth()->user()->profile_photo }}" alt="avatar">
                                @else
                                    <div class="initials">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                                @endif
                            </div>
                            <div class="profile-dropdown-name">{{ auth()->user()->name }}</div>
                            <div class="profile-dropdown-role">{{ auth()->user()->role_label }}</div>
                        </div>
                        <button class="profile-dropdown-item" onclick="openPhotoModal()">
                            <i class="fas fa-camera"></i> Change Photo
                        </button>
                        <div class="profile-dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="profile-dropdown-item" style="color:#dc2626">
                                <i class="fas fa-sign-out-alt" style="color:#dc2626"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="page-content">
            @yield('content')
        </main>
    </div>

    <!-- Profile Photo Upload Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
            <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.15)">
                <div class="modal-header" style="border-bottom:1px solid #f3f4f6;padding:18px 22px">
                    <h5 class="modal-title" style="font-size:.95rem;font-weight:700;color:var(--green-dark)">
                        <i class="fas fa-camera me-2"></i>Update Profile Photo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Preview -->
                    <div class="text-center mb-4">
                        <div id="photoPreviewWrap" style="width:100px;height:100px;border-radius:50%;margin:0 auto;overflow:hidden;background:linear-gradient(135deg,var(--green-main),var(--green-dark));display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:white;border:3px solid var(--green-pale)">
                            @if(auth()->user()->profile_photo)
                                <img id="photoPreview" src="{{ auth()->user()->profile_photo }}" style="width:100%;height:100%;object-fit:cover">
                            @else
                                <span id="photoInitials">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" id="photoUploadForm">
                        @csrf
                        @method('POST')
                        <div class="mb-3">
                            <label class="form-label" style="font-size:.82rem;font-weight:600;color:#374151">Choose from Gallery</label>
                            <input type="file" name="photo" id="photoInput" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewPhoto(this)" required>
                            <div style="font-size:.72rem;color:#9ca3af;margin-top:6px">JPG, PNG, GIF or WEBP. Max 2MB.</div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-green flex-fill py-2">
                                <i class="fas fa-upload me-2"></i>Upload Photo
                            </button>
                            @if(auth()->user()->profile_photo)
                            <button type="button" class="btn btn-outline-secondary px-3 py-2" onclick="removePhoto()">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove Photo Form (hidden) -->
    <form method="POST" action="{{ route('profile.photo.remove') }}" id="removePhotoForm" style="display:none">
        @csrf
        @method('DELETE')
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        }

        // Active nav item
        document.querySelectorAll('.nav-item-link').forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('active');
            }
        });

        // Notification dropdown
        function toggleNotif(e) {
            e.stopPropagation();
            document.getElementById('profileDropdown').classList.remove('show');
            document.getElementById('notifDropdown').classList.toggle('show');
        }

        // Profile dropdown
        function toggleProfile(e) {
            e.stopPropagation();
            document.getElementById('notifDropdown').classList.remove('show');
            document.getElementById('profileDropdown').classList.toggle('show');
        }

        // Close dropdowns on outside click
        document.addEventListener('click', function() {
            document.getElementById('notifDropdown').classList.remove('show');
            document.getElementById('profileDropdown').classList.remove('show');
        });

        // Mark all notifications as read
        function markAllRead() {
            document.getElementById('notifDot').style.display = 'none';
            document.getElementById('notifList').innerHTML = '<div class="notif-empty"><i class="fas fa-check-circle fa-2x d-block mb-2" style="color:#d1fae5"></i>All caught up!</div>';
        }

        // Profile photo modal
        function openPhotoModal() {
            document.getElementById('profileDropdown').classList.remove('show');
            new bootstrap.Modal(document.getElementById('photoModal')).show();
        }

        // Preview selected photo
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const wrap = document.getElementById('photoPreviewWrap');
                    wrap.innerHTML = `<img id="photoPreview" src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Remove photo
        function removePhoto() {
            if (confirm('Remove profile photo?')) {
                document.getElementById('removePhotoForm').submit();
            }
        }

        // Load notifications (pending approvals etc)
        @if(auth()->check())
        fetch('{{ route("notifications.get") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                if (data.notifications && data.notifications.length > 0) {
                    document.getElementById('notifDot').style.display = 'block';
                    let html = '';
                    data.notifications.forEach(n => {
                        html += `<div class="notif-item">
                            <div class="notif-icon" style="background:${n.bg}"><i class="fas ${n.icon}" style="color:${n.color}"></i></div>
                            <div>
                                <div class="notif-text">${n.message}</div>
                                <div class="notif-time">${n.time}</div>
                            </div>
                        </div>`;
                    });
                    document.getElementById('notifList').innerHTML = html;
                } else {
                    document.getElementById('notifDot').style.display = 'none';
                }
            }).catch(() => {
                document.getElementById('notifDot').style.display = 'none';
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
