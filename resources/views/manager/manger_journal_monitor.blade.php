@extends('layouts.app')
@section('title','Journal Monitor')
@section('page_title','Journal Monitor')
@section('page_subtitle','View all journal entries (read-only)')
@section('sidebar_nav') @include('manager.partials.sidebar') @endsection
@section('content')
<div class="chart-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><select name="status" class="form-select form-select-sm"><option value="">All Status</option><option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option><option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option><option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option><option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option></select></div>
        <div class="col-md-2"><input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}"></div>
        <div class="col-md-2"><input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-green btn-sm w-100"><i class="fas fa-search me-1"></i> Filter</button></div>
    </form>
</div>
<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-book me-2" style="color:var(--green-main)"></i>Journal Entries ({{ $entries->total() }})</h6></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Entry No.</th><th>Date</th><th>Description</th><th>Submitted By</th><th>Debit (RM)</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($entries as $e)
            <tr><td><strong style="color:var(--green-main)">{{ $e->entry_number }}</strong></td><td>{{ $e->entry_date->format('d/m/Y') }}</td><td>{{ Str::limit($e->description,40) }}</td><td><small>{{ $e->creator->name ?? '-' }}</small></td><td><strong>{{ number_format($e->total_debit,2) }}</strong></td><td>{!! $e->status_badge !!}</td></tr>
            @empty
            <tr><td colspan="6" class="text-center py-4" style="color:#9ca3af">No entries found</td></tr>
            @endforelse
        </tbody>
    </table></div>
    @if($entries->hasPages())<div class="p-3">{{ $entries->withQueryString()->links() }}</div>@endif
</div>
@endsection
