@extends('layouts.app')
@section('title','Auditor Dashboard')
@section('page_title','Auditor Dashboard')
@section('page_subtitle','System monitoring & audit overview')
@section('sidebar_nav') @include('auditor.partials.sidebar') @endsection

@section('content')

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-green-soft"><i class="fas fa-calendar-day"></i></div>
            <div class="stat-value text-green">{{ $todayTotal }}</div>
            <div class="stat-label">Transactions Today</div>
            <div class="stat-change" style="color:#6b7280;font-size:.72rem;margin-top:6px">
                <i class="fas fa-book me-1" style="color:var(--green-main)"></i>{{ $todayJournals }} journals &nbsp;
                <i class="fas fa-file-invoice-dollar me-1" style="color:#0369a1"></i>{{ $todayInvoices }} inv &nbsp;
                <i class="fas fa-receipt me-1" style="color:#d97706"></i>{{ $todayBills }} bills
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-red-soft"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-value text-red">{{ $abnormalCount }}</div>
            <div class="stat-label">Abnormal Activities</div>
            <div class="stat-change text-red"><i class="fas fa-flag me-1"></i>High-value transactions</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-amber-soft"><i class="fas fa-times-circle"></i></div>
            <div class="stat-value text-amber">{{ $failedLogins }}</div>
            <div class="stat-label">Failed Logins Today</div>
            <div class="stat-change text-amber"><i class="fas fa-lock me-1"></i>Unauthorized attempts</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-blue-soft"><i class="fas fa-users"></i></div>
            <div class="stat-value" style="color:#0369a1">{{ $mostActiveUsers->count() }}</div>
            <div class="stat-label">Active Users This Month</div>
            <div class="stat-change" style="color:#0369a1"><i class="fas fa-chart-line me-1"></i>By activity count</div>
        </div>
    </div>
</div>

{{-- Charts + Most Active --}}
<div class="row g-3 mb-4">
    {{-- 7-day activity chart --}}
    <div class="col-12 col-md-7">
        <div class="chart-card">
            <div class="card-header-custom">
                <span class="card-title">📊 System Activity — Last 7 Days</span>
            </div>
            <canvas id="activityChart" height="200"></canvas>
        </div>
    </div>

    {{-- Most Active Users --}}
    <div class="col-12 col-md-5">
        <div class="chart-card" style="height:100%">
            <div class="card-header-custom">
                <span class="card-title">🏆 Most Active Users (This Month)</span>
            </div>
            @forelse($mostActiveUsers as $i => $item)
            <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid #f3f4f6">
                <div style="width:26px;height:26px;border-radius:50%;background:{{ ['linear-gradient(135deg,#ffd700,#ffaa00)','linear-gradient(135deg,#c0c0c0,#a0a0a0)','linear-gradient(135deg,#cd7f32,#a0522d)','linear-gradient(135deg,var(--green-light),var(--green-main))','linear-gradient(135deg,#60a5fa,#0369a1)'][$i] }};display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.65rem;color:{{ $i<3?'white':'white' }};flex-shrink:0">
                    {{ $i + 1 }}
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-size:.82rem;font-weight:700;color:#1f2937;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $item->user->name ?? 'Unknown' }}</div>
                    <div style="font-size:.7rem;color:#9ca3af">{{ $item->user->role_label ?? '' }}</div>
                </div>
                <div style="text-align:right">
                    <div style="font-weight:800;color:var(--green-main);font-size:.9rem">{{ $item->activity_count }}</div>
                    <div style="font-size:.65rem;color:#9ca3af">actions</div>
                </div>
            </div>
            @empty
            <div class="text-center py-4" style="color:#9ca3af"><i class="fas fa-users fa-2x d-block mb-2"></i>No activity yet</div>
            @endforelse
        </div>
    </div>
</div>

{{-- Recent Audit Activity --}}
<div class="data-table">
    <div class="table-header">
        <h6><i class="fas fa-shoe-prints me-2" style="color:var(--green-main)"></i>Recent Audit Activity</h6>
        <a href="{{ route('auditor.audit_trail') }}" class="btn btn-sm btn-green px-3">View Full Trail</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr><th>User</th><th>Action</th><th>Module</th><th>Description</th><th>IP Address</th><th>Time</th></tr>
            </thead>
            <tbody>
                @forelse($recentAudit as $trail)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:28px;height:28px;border-radius:6px;background:linear-gradient(135deg,var(--green-main),var(--green-dark));display:flex;align-items:center;justify-content:center;color:white;font-size:.65rem;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($trail->user->name ?? '?', 0, 2)) }}
                            </div>
                            <small style="font-weight:600">{{ $trail->user->name ?? 'System' }}</small>
                        </div>
                    </td>
                    <td>
                        @php
                        $actionColors = ['login'=>['#d1fae5','#065f46'],'logout'=>['#f3f4f6','#374151'],'create'=>['#dbeafe','#1e40af'],'update'=>['#fef3c7','#92400e'],'delete'=>['#fee2e2','#991b1b'],'approve'=>['#d1fae5','#065f46'],'reject'=>['#fee2e2','#991b1b'],'submit'=>['#ede9fe','#5b21b6'],'generate_report'=>['#f0fdf4','#15803d']];
                        $ac = $actionColors[$trail->action] ?? ['#f3f4f6','#374151'];
                        @endphp
                        <span style="background:{{ $ac[0] }};color:{{ $ac[1] }};padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;text-transform:uppercase">{{ $trail->action }}</span>
                    </td>
                    <td><small class="text-muted">{{ str_replace('_',' ',ucfirst($trail->module)) }}</small></td>
                    <td><small>{{ Str::limit($trail->description, 45) }}</small></td>
                    <td><small class="text-muted">{{ $trail->ip_address }}</small></td>
                    <td><small class="text-muted">{{ $trail->created_at->diffForHumans() }}</small></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4" style="color:#9ca3af"><i class="fas fa-inbox fa-2x d-block mb-2"></i>No audit activity yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
new Chart(document.getElementById('activityChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($activityDays) !!},
        datasets: [{
            label: 'System Activities',
            data: {!! json_encode($activityData) !!},
            backgroundColor: 'rgba(26,122,87,0.75)',
            borderColor: '#1a7a57',
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Poppins', size: 10 } } },
            x: { ticks: { font: { family: 'Poppins', size: 10 } } }
        }
    }
});
</script>
@endpush
