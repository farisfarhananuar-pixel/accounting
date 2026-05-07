@extends('layouts.app')
@section('title','Rejection Reasons') @section('page_title','Rejection Reasons') @section('page_subtitle','All rejected transactions with reasons')
@section('sidebar_nav') @include('manager.partials.sidebar') @endsection
@section('content')
<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-times-circle me-2" style="color:#dc2626"></i>Rejected Journal Entries ({{ $rejectedJournals->total() }})</h6></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Entry No.</th><th>Date</th><th>Submitted By</th><th>Rejected By</th><th>Rejected At</th><th>Reason</th></tr></thead>
        <tbody>
        @forelse($rejectedJournals as $j)
        <tr>
            <td><strong style="color:#dc2626">{{ $j->entry_number }}</strong></td>
            <td>{{ $j->entry_date->format('d/m/Y') }}</td>
            <td><small>{{ $j->creator->name ?? '-' }}</small></td>
            <td><small>{{ $j->rejecter->name ?? '-' }}</small></td>
            <td><small class="text-muted">{{ $j->rejected_at ? $j->rejected_at->format('d/m/Y H:i') : '-' }}</small></td>
            <td>
                <span style="background:#fee2e2;color:#991b1b;padding:4px 10px;border-radius:6px;font-size:.78rem;font-weight:500">
                    {{ $j->rejection_reason ?? 'No reason given' }}
                </span>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center py-5" style="color:#9ca3af"><i class="fas fa-check-circle fa-2x d-block mb-2" style="color:var(--green-main)"></i>No rejected transactions</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($rejectedJournals->hasPages())<div class="p-3">{{ $rejectedJournals->links() }}</div>@endif
</div>
@endsection
