{{-- resources/views/auditor/partials/sidebar.blade.php --}}
<span class="nav-section-title">Main Menu</span>
<a href="{{ route('auditor.dashboard') }}" class="nav-item-link {{ request()->routeIs('auditor.dashboard')?'active':'' }}">
    <span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard
</a>

<span class="nav-section-title">Audit & Logs</span>
<a href="{{ route('auditor.view_logs') }}" class="nav-item-link {{ request()->routeIs('auditor.view_logs')?'active':'' }}">
    <span class="nav-icon"><i class="fas fa-list-ul"></i></span> View Logs
</a>
<a href="{{ route('auditor.audit_trail') }}" class="nav-item-link {{ request()->routeIs('auditor.audit_trail')?'active':'' }}">
    <span class="nav-icon"><i class="fas fa-shoe-prints"></i></span> Audit Trail
</a>
<a href="{{ route('auditor.audit_financial_report') }}" class="nav-item-link {{ request()->routeIs('auditor.audit_financial_report')?'active':'' }}">
    <span class="nav-icon"><i class="fas fa-shield-alt"></i></span> Financial Audit
</a>

<span class="nav-section-title">View Records</span>
<a href="{{ route('auditor.journal_entries') }}" class="nav-item-link {{ request()->routeIs('auditor.journal_entries')?'active':'' }}">
    <span class="nav-icon"><i class="fas fa-book"></i></span> Journal Entries
</a>
<a href="{{ route('auditor.general_ledger') }}" class="nav-item-link {{ request()->routeIs('auditor.general_ledger')?'active':'' }}">
    <span class="nav-icon"><i class="fas fa-journal-whills"></i></span> General Ledger
</a>
<a href="{{ route('auditor.ar_ap') }}" class="nav-item-link {{ request()->routeIs('auditor.ar_ap')?'active':'' }}">
    <span class="nav-icon"><i class="fas fa-exchange-alt"></i></span> AR / AP View
</a>
<a href="{{ route('auditor.payment_history') }}" class="nav-item-link {{ request()->routeIs('auditor.payment_history')?'active':'' }}">
    <span class="nav-icon"><i class="fas fa-money-check-alt"></i></span> Payment History
</a>

<span class="nav-section-title">Reports</span>
<a href="{{ route('auditor.audit_report') }}" class="nav-item-link {{ request()->routeIs('auditor.audit_report')?'active':'' }}">
    <span class="nav-icon"><i class="fas fa-file-contract"></i></span> Generate Report
</a>
