@extends('layouts.app')

@section('title', 'Accountant Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Welcome back, ' . auth()->user()->name)

@section('sidebar_nav')
<span class="nav-section-title">Main Menu</span>
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link active">
    <span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard
</a>
<span class="nav-section-title">Transactions</span>
<a href="{{ route('accountant.journal_entries') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-book"></i></span> Journal Entries
</a>
<a href="{{ route('accountant.account_receivable') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span> Account Receivable
</a>
<a href="{{ route('accountant.account_payable') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-receipt"></i></span> Account Payable
</a>
<a href="{{ route('accountant.customers') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-users"></i></span> Customers</a>
<a href="{{ route('accountant.vendors') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-truck"></i></span> Vendors</a>
<a href="{{ route('accountant.chart_of_account') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-list-alt"></i></span> Chart of Accounts
</a>
<span class="nav-section-title">Reports</span>
<a href="{{ route('accountant.general_ledger') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-journal-whills"></i></span> General Ledger
</a>
<a href="{{ route('accountant.bank_reconciliation') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-university"></i></span> Bank Reconciliation
</a>
<a href="{{ route('accountant.fixed_asset') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-building"></i></span> Fixed Assets
</a>
<a href="{{ route('accountant.trial_balance') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-balance-scale"></i></span> Trial Balance
</a>
<a href="{{ route('accountant.profit_loss') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-chart-line"></i></span> Profit & Loss
</a>
<a href="{{ route('accountant.financial_position') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-file-alt"></i></span> Financial Position
</a>
<a href="{{ route('accountant.tax_calculations') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-percentage"></i></span> Tax Calculations
</a>
<a href="{{ route('accountant.financial_statements') }}" class="nav-item-link">
    <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> Financial Statements
</a>
@endsection

@section('content')

{{-- Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius:12px;border:none;background:#d1fae5;color:#065f46">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Quick Action Buttons --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('accountant.journal_entries.create') }}" class="btn w-100 py-3" style="background:linear-gradient(135deg,#1a7a57,#0d4f3c);color:white;border-radius:12px;font-weight:600;font-size:.85rem;text-decoration:none;display:flex;flex-direction:column;align-items:center;gap:6px">
            <i class="fas fa-plus-circle fa-lg"></i> New Journal Entry
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('accountant.account_receivable.create') }}" class="btn w-100 py-3" style="background:linear-gradient(135deg,#0369a1,#075985);color:white;border-radius:12px;font-weight:600;font-size:.85rem;text-decoration:none;display:flex;flex-direction:column;align-items:center;gap:6px">
            <i class="fas fa-file-invoice-dollar fa-lg"></i> Add Invoice (AR)
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('accountant.account_payable.create') }}" class="btn w-100 py-3" style="background:linear-gradient(135deg,#d97706,#b45309);color:white;border-radius:12px;font-weight:600;font-size:.85rem;text-decoration:none;display:flex;flex-direction:column;align-items:center;gap:6px">
            <i class="fas fa-receipt fa-lg"></i> Record Bill (AP)
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('accountant.customers') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-users"></i></span> Customers</a>
<a href="{{ route('accountant.vendors') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-truck"></i></span> Vendors</a>
<a href="{{ route('accountant.chart_of_account') }}" class="btn w-100 py-3" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);color:white;border-radius:12px;font-weight:600;font-size:.85rem;text-decoration:none;display:flex;flex-direction:column;align-items:center;gap:6px">
            <i class="fas fa-plus fa-lg"></i> Add New Account
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-green-soft"><i class="fas fa-file-alt"></i></div>
            <div class="stat-value text-green">{{ $totalDraft }}</div>
            <div class="stat-label">Total Draft Entries</div>
            <div class="stat-change text-green"><i class="fas fa-circle-dot me-1"></i>Awaiting submission</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-amber-soft"><i class="fas fa-clock"></i></div>
            <div class="stat-value text-amber">{{ $totalPending }}</div>
            <div class="stat-label">Pending Approval</div>
            <div class="stat-change text-amber"><i class="fas fa-hourglass-half me-1"></i>Waiting manager review</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-red-soft"><i class="fas fa-times-circle"></i></div>
            <div class="stat-value text-red">{{ $totalRejected }}</div>
            <div class="stat-label">Rejected Transactions</div>
            <div class="stat-change text-red"><i class="fas fa-exclamation-triangle me-1"></i>Needs revision</div>
        </div>
    </div>
</div>

{{-- Charts --}}
<div class="row g-3 mb-4">
    {{-- Pie Chart --}}
    <div class="col-12 col-md-4">
        <div class="chart-card">
            <div class="card-header-custom">
                <span class="card-title">📊 Transaction Status</span>
            </div>
            <canvas id="pieChart" height="220"></canvas>
        </div>
    </div>
    {{-- Bar Chart AR vs AP --}}
    <div class="col-12 col-md-8">
        <div class="chart-card">
            <div class="card-header-custom">
                <span class="card-title">📈 AR vs AP (Last 6 Months)</span>
            </div>
            <canvas id="barChart" height="220"></canvas>
        </div>
    </div>
</div>

{{-- Recent Transactions --}}
<div class="data-table">
    <div class="table-header">
        <h6><i class="fas fa-history me-2" style="color:var(--green-main)"></i>Recent Transactions</h6>
        <a href="{{ route('accountant.journal_entries') }}" class="btn btn-sm btn-green px-3">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Entry No.</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Debit (RM)</th>
                    <th>Credit (RM)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentJournals as $j)
                <tr>
                    <td><span style="font-weight:700;color:var(--green-main)">{{ $j->entry_number }}</span></td>
                    <td>{{ $j->entry_date->format('d M Y') }}</td>
                    <td>{{ Str::limit($j->description, 40) }}</td>
                    <td style="font-weight:600">RM {{ number_format($j->total_debit, 2) }}</td>
                    <td style="font-weight:600">RM {{ number_format($j->total_credit, 2) }}</td>
                    <td>{!! $j->status_badge !!}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4" style="color:#9ca3af">
                        <i class="fas fa-inbox fa-2x d-block mb-2"></i>No transactions yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Pie Chart
const pieCtx = document.getElementById('pieChart').getContext('2d');
new Chart(pieCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($pieData)) !!},
        datasets: [{
            data: {!! json_encode(array_values($pieData)) !!},
            backgroundColor: ['#e5e7eb','#fef3c7','#d1fae5','#fee2e2'],
            borderColor: ['#9ca3af','#d97706','#1a7a57','#dc2626'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { font: { family: 'Poppins', size: 11 } } } },
        cutout: '65%'
    }
});

// Bar Chart
const barCtx = document.getElementById('barChart').getContext('2d');
new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($months) !!},
        datasets: [
            { label: 'AR (Receivable)', data: {!! json_encode($arData) !!}, backgroundColor: 'rgba(26,122,87,0.7)', borderColor: '#1a7a57', borderWidth: 2, borderRadius: 6 },
            { label: 'AP (Payable)', data: {!! json_encode($apData) !!}, backgroundColor: 'rgba(217,119,6,0.7)', borderColor: '#d97706', borderWidth: 2, borderRadius: 6 }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top', labels: { font: { family: 'Poppins', size: 11 } } } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'RM ' + v.toLocaleString(), font: { family:'Poppins', size:10 } } },
            x: { ticks: { font: { family:'Poppins', size:10 } } }
        }
    }
});
</script>
@endpush
