@extends('layouts.app')
@section('title','Unusual Transactions')
@section('page_title','Unusual Transaction Detection')
@section('page_subtitle','Transactions flagged for review')
@section('sidebar_nav') @include('manager.partials.sidebar') @endsection
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-red-soft"><i class="fas fa-exclamation-triangle"></i></div><div class="stat-value text-red">{{ $unusual->total() }}</div><div class="stat-label">High-Value Transactions</div><div class="stat-change text-red">Above RM {{ number_format($threshold,0) }} threshold</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-amber-soft"><i class="fas fa-copy"></i></div><div class="stat-value text-amber">{{ $duplicateSuspects->count() }}</div><div class="stat-label">Duplicate Suspects</div><div class="stat-change text-amber">Multiple entries same day</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-icon bg-blue-soft"><i class="fas fa-calculator"></i></div><div class="stat-value" style="color:#0369a1">RM {{ number_format($avgAmount,0) }}</div><div class="stat-label">Average Entry Amount</div><div class="stat-change" style="color:#0369a1">Threshold = 3× average</div></div></div>
</div>

{{-- High Value --}}
<div class="data-table mb-4">
    <div class="table-header">
        <h6><i class="fas fa-exclamation-triangle me-2" style="color:#dc2626"></i>High-Value Transactions (above RM {{ number_format($threshold,2) }})</h6>
    </div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Entry No.</th><th>Date</th><th>Description</th><th>Created By</th><th>Debit (RM)</th><th>Status</th><th>Flag</th></tr></thead>
        <tbody>
            @forelse($unusual as $entry)
            <tr>
                <td><strong style="color:var(--green-main)">{{ $entry->entry_number }}</strong></td>
                <td>{{ $entry->entry_date->format('d/m/Y') }}</td>
                <td>{{ Str::limit($entry->description,40) }}</td>
                <td><small>{{ $entry->creator->name ?? '-' }}</small></td>
                <td><strong style="color:#dc2626">{{ number_format($entry->total_debit,2) }}</strong></td>
                <td>
                    @php $colors=['draft'=>'badge-draft','pending'=>'badge-pending','approved'=>'badge-approved','rejected'=>'badge-rejected']; @endphp
                    <span class="status-badge {{ $colors[$entry->status]??'badge-draft' }}">{{ ucfirst($entry->status) }}</span>
                </td>
                <td>
                    @if($entry->total_debit > $threshold * 2)
                        <span class="status-badge badge-rejected"><i class="fas fa-exclamation-triangle me-1"></i>CRITICAL</span>
                    @else
                        <span class="status-badge badge-pending"><i class="fas fa-exclamation-circle me-1"></i>HIGH</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4" style="color:#9ca3af"><i class="fas fa-shield-alt fa-2x d-block mb-2" style="color:var(--green-main)"></i>No unusual high-value transactions detected</td></tr>
            @endforelse
        </tbody>
    </table></div>
    @if($unusual->hasPages())<div class="p-3">{{ $unusual->links() }}</div>@endif
</div>

{{-- Duplicate Suspects --}}
@if($duplicateSuspects->count() > 0)
<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-copy me-2" style="color:#d97706"></i>Possible Duplicate Entries</h6></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>User</th><th>Date</th><th>Entry Count</th><th>Action Required</th></tr></thead>
        <tbody>
            @foreach($duplicateSuspects as $dup)
            <tr>
                <td><strong>{{ $dup->creator->name ?? 'Unknown' }}</strong></td>
                <td>{{ $dup->entry_date }}</td>
                <td><span class="status-badge badge-pending">{{ $dup->count }} entries</span></td>
                <td><a href="{{ route('manager.journal_monitor') }}?date_from={{ $dup->entry_date }}&date_to={{ $dup->entry_date }}" class="btn btn-sm px-2" style="background:#fef3c7;color:#92400e;border-radius:6px;font-size:.75rem;font-weight:600"><i class="fas fa-search me-1"></i>Investigate</a></td>
            </tr>
            @endforeach
        </tbody>
    </table></div>
</div>
@endif
@endsection
