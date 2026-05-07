{{-- Reusable manager sidebar nav - include in each manager view --}}
{{-- Usage: @include('manager.partials.sidebar') --}}

<span class="nav-section-title">Main Menu</span>
<a href="{{ route('manager.dashboard') }}" class="nav-item-link {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
    <span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard
</a>

<span class="nav-section-title">Approvals</span>
<a href="{{ route('manager.approval_queue') }}" class="nav-item-link {{ request()->routeIs('manager.approval_queue') ? 'active' : '' }}">
    <span class="nav-icon"><i class="fas fa-inbox"></i></span> Approval Queue
</a>
<a href="{{ route('manager.approve_reject') }}" class="nav-item-link {{ request()->routeIs('manager.approve_reject') ? 'active' : '' }}">
    <span class="nav-icon"><i class="fas fa-check-double"></i></span> Approve / Reject
</a>
<a href="{{ route('manager.rejection_reasons') }}" class="nav-item-link {{ request()->routeIs('manager.rejection_reasons') ? 'active' : '' }}">
    <span class="nav-icon"><i class="fas fa-times-circle"></i></span> Rejection Reasons
</a>
<a href="{{ route('manager.unusual_transactions') }}" class="nav-item-link {{ request()->routeIs('manager.unusual_transactions') ? 'active' : '' }}">
    <span class="nav-icon"><i class="fas fa-exclamation-triangle"></i></span> Unusual Transactions
</a>

<span class="nav-section-title">Monitor</span>
<a href="{{ route('manager.reports') }}" class="nav-item-link {{ request()->routeIs('manager.reports') ? 'active' : '' }}">
    <span class="nav-icon"><i class="fas fa-chart-bar"></i></span> Reports
</a>
<a href="{{ route('manager.journal_monitor') }}" class="nav-item-link {{ request()->routeIs('manager.journal_monitor') ? 'active' : '' }}">
    <span class="nav-icon"><i class="fas fa-book"></i></span> Journal Monitor
</a>
<a href="{{ route('manager.ar_monitor') }}" class="nav-item-link {{ request()->routeIs('manager.ar_monitor') ? 'active' : '' }}">
    <span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span> AR Monitor
</a>
<a href="{{ route('manager.ap_monitor') }}" class="nav-item-link {{ request()->routeIs('manager.ap_monitor') ? 'active' : '' }}">
    <span class="nav-icon"><i class="fas fa-receipt"></i></span> AP Monitor
</a>
<a href="{{ route('manager.chart_of_account') }}" class="nav-item-link {{ request()->routeIs('manager.chart_of_account') ? 'active' : '' }}">
    <span class="nav-icon"><i class="fas fa-list-alt"></i></span> Chart of Accounts
</a>

<span class="nav-section-title">Administration</span>
<a href="{{ route('manager.create_roles') }}" class="nav-item-link {{ request()->routeIs('manager.create_roles') ? 'active' : '' }}">
    <span class="nav-icon"><i class="fas fa-user-plus"></i></span> Create Roles
</a>
