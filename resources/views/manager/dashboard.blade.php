@extends('layouts.app')
@section('title','Manager Dashboard')
@section('page_title','Manager Dashboard')
@section('page_subtitle', 'Welcome back, ' . auth()->user()->name)

@section('sidebar_nav')
<span class="nav-section-title">Main Menu</span>
<a href="{{ route('manager.dashboard') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<span class="nav-section-title">Approvals</span>
<a href="{{ route('manager.approval_queue') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-inbox"></i></span> Approval Queue
    @if(isset($pendingCount) && $pendingCount > 0)<span class="nav-badge">{{ $pendingCount }}</span>@endif
</a>
<a href="{{ route('manager.approve_reject') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-check-double"></i></span> Approve / Reject</a>
<a href="{{ route('manager.rejection_reasons') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-times-circle"></i></span> Rejection Reasons</a>
<a href="{{ route('manager.unusual_transactions') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-exclamation-triangle"></i></span> Unusual Transactions</a>
<span class="nav-section-title">Monitor</span>
<a href="{{ route('manager.reports') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-chart-bar"></i></span> Reports</a>
<a href="{{ route('manager.journal_monitor') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-book"></i></span> Journal Monitor</a>
<a href="{{ route('manager.ar_monitor') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span> AR Monitor</a>
<a href="{{ route('manager.ap_monitor') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-receipt"></i></span> AP Monitor</a>
<a href="{{ route('manager.chart_of_account') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-list-alt"></i></span> Chart of Accounts</a>
<span class="nav-section-title">Administration</span>
<a href="{{ route('manager.create_roles') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-user-plus"></i></span> Create Roles</a>
@endsection

@section('content')
{{-- Alert --}}
@if(session('success'))
<div class="alert alert-dismissible fade show" style="background:#d1fae5;color:#065f46;border:none;border-radius:12px" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-green-soft"><i class="fas fa-arrow-up"></i></div>
            <div class="stat-value text-green">RM {{ number_format($totalRevenue/1000,1) }}K</div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-change text-green"><i class="fas fa-trending-up me-1"></i>Approved entries</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-red-soft"><i class="fas fa-arrow-down"></i></div>
            <div class="stat-value text-red">RM {{ number_format($totalExpenses/1000,1) }}K</div>
            <div class="stat-label">Total Expenses</div>
            <div class="stat-change text-red"><i class="fas fa-trending-down me-1"></i>All categories</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon {{ $netProfit >= 0 ? 'bg-green-soft' : 'bg-red-soft' }}"><i class="fas fa-chart-line"></i></div>
            <div class="stat-value" style="color:{{ $netProfit >= 0 ? 'var(--green-main)' : '#dc2626' }}">
                RM {{ number_format(abs($netProfit)/1000, 1) }}K
            </div>
            <div class="stat-label">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</div>
            <div class="stat-change" style="color:{{ $netProfit >= 0 ? 'var(--green-main)' : '#dc2626' }}">
                {{ $netProfit >= 0 ? 'Positive performance' : 'Review expenses' }}
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-amber-soft"><i class="fas fa-clock"></i></div>
            <div class="stat-value text-amber">{{ $pendingCount }}</div>
            <div class="stat-label">Pending Approvals</div>
            @if($pendingCount > 0)
            <a href="{{ route('manager.approval_queue') }}" style="font-size:.72rem;color:#d97706;text-decoration:none;font-weight:600;display:block;margin-top:6px">
                <i class="fas fa-arrow-right me-1"></i>Review now
            </a>
            @endif
        </div>
    </div>
</div>

{{-- Charts Row 1: Bar + Pie --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-7">
        <div class="chart-card">
            <div class="card-header-custom">
                <span class="card-title">📊 Revenue vs Expenses (Last 6 Months)</span>
            </div>
            <canvas id="revExpChart" height="220"></canvas>
        </div>
    </div>
    <div class="col-12 col-md-5">
        <div class="chart-card">
            <div class="card-header-custom">
                <span class="card-title">🥧 Expense Breakdown</span>
            </div>
            <canvas id="expPieChart" height="220"></canvas>
        </div>
    </div>
</div>

{{-- Chart Row 2: Cash Flow Line --}}
<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="chart-card">
            <div class="card-header-custom">
                <span class="card-title">📈 Cash Flow — Inflow vs Outflow (Last 6 Months)</span>
            </div>
            <canvas id="cashFlowChart" height="120"></canvas>
        </div>
    </div>
</div>

{{-- Pending Approvals Quick View --}}
@if($pendingCount > 0)
<div class="data-table">
    <div class="table-header">
        <h6><i class="fas fa-clock me-2" style="color:#d97706"></i>Pending Approvals</h6>
        <a href="{{ route('manager.approval_queue') }}" class="btn btn-sm px-3" style="background:#d97706;color:white;border-radius:8px;font-weight:600">
            View All ({{ $pendingCount }})
        </a>
    </div>
    <div class="p-4 text-center" style="color:#6b7280">
        <i class="fas fa-inbox fa-2x d-block mb-2" style="color:#d97706"></i>
        <p class="mb-2">You have <strong style="color:#d97706">{{ $pendingCount }}</strong> items waiting for your review.</p>
        <a href="{{ route('manager.approval_queue') }}" class="btn btn-sm px-4" style="background:#d97706;color:white;border-radius:8px;font-weight:600">
            <i class="fas fa-check-double me-2"></i>Go to Approval Queue
        </a>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// Revenue vs Expenses Bar Chart
new Chart(document.getElementById('revExpChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($months) !!},
        datasets: [
            { label: 'Revenue', data: {!! json_encode($revData) !!}, backgroundColor: 'rgba(26,122,87,0.75)', borderColor: '#1a7a57', borderWidth: 2, borderRadius: 6 },
            { label: 'Expenses', data: {!! json_encode($expData) !!}, backgroundColor: 'rgba(220,38,38,0.65)', borderColor: '#dc2626', borderWidth: 2, borderRadius: 6 }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position:'top', labels:{ font:{ family:'Poppins', size:11 } } } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'RM '+(v/1000).toFixed(0)+'K', font:{ family:'Poppins', size:10 } } },
            x: { ticks: { font:{ family:'Poppins', size:10 } } }
        }
    }
});

// Expense Breakdown Pie
const expLabels = {!! json_encode($expenseAccounts->pluck('account_name')->values()) !!};
const expValues = {!! json_encode($expenseAccounts->pluck('balance')->values()) !!};
new Chart(document.getElementById('expPieChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: expLabels.length ? expLabels : ['No Data'],
        datasets: [{ data: expValues.length ? expValues : [1], backgroundColor: ['#1a7a57','#d97706','#dc2626','#0369a1','#7c3aed','#0ea5a0'], borderWidth: 2 }]
    },
    options: { responsive: true, plugins: { legend: { position:'bottom', labels:{ font:{ family:'Poppins', size:10 }, boxWidth:12 } } }, cutout:'60%' }
});

// Cash Flow Line Chart
new Chart(document.getElementById('cashFlowChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: {!! json_encode($months) !!},
        datasets: [
            { label: 'Cash Inflow', data: {!! json_encode($cashInflow) !!}, borderColor: '#1a7a57', backgroundColor: 'rgba(26,122,87,0.1)', tension: 0.4, fill: true, pointBackgroundColor:'#1a7a57', pointRadius: 5 },
            { label: 'Cash Outflow', data: {!! json_encode($cashOutflow) !!}, borderColor: '#dc2626', backgroundColor: 'rgba(220,38,38,0.1)', tension: 0.4, fill: true, pointBackgroundColor:'#dc2626', pointRadius: 5 }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position:'top', labels:{ font:{ family:'Poppins', size:11 } } } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'RM '+(v/1000).toFixed(0)+'K', font:{ family:'Poppins', size:10 } } },
            x: { ticks: { font:{ family:'Poppins', size:10 } } }
        }
    }
});
</script>
@endpush
