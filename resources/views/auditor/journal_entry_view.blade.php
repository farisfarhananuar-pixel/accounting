@extends('layouts.app')
@section('title','Journal Entry View') @section('page_title','Journal Entries') @section('page_subtitle','Filter by date, user or status — read only')
@section('sidebar_nav') @include('auditor.partials.sidebar') @endsection

@section('content')
<div class="chart-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label" style="font-size:.78rem;font-weight:600">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Entry no. or description..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:.78rem;font-weight:600">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:.78rem;font-weight:600">User</label>
            <select name="user_id" class="form-select form-select-sm">
                <option value="">All Users</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:.78rem;font-weight:600">Date From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label" style="font-size:.78rem;font-weight:600">Date To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-green btn-sm flex-fill"><i class="fas fa-search"></i></button>
            <a href="{{ route('auditor.journal_entries') }}" class="btn btn-sm btn-outline-secondary px-2">✕</a>
        </div>
    </form>
</div>

<div class="data-table">
    <div class="table-header">
        <h6><i class="fas fa-book me-2" style="color:var(--green-main)"></i>Journal Entries ({{ $entries->total() }}) <small class="text-muted" style="font-weight:400"> — Read Only</small></h6>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr><th>Entry No.</th><th>Date</th><th>Description</th><th>Submitted By</th><th>Approved By</th><th>Debit (RM)</th><th>Status</th><th>Detail</th></tr>
            </thead>
            <tbody>
                @forelse($entries as $e)
                <tr>
                    <td><strong style="color:var(--green-main)">{{ $e->entry_number }}</strong></td>
                    <td>{{ $e->entry_date->format('d/m/Y') }}</td>
                    <td>{{ Str::limit($e->description,35) }}</td>
                    <td><small>{{ $e->creator->name ?? '-' }}</small></td>
                    <td>
                        @if($e->approver)
                        <small style="color:var(--green-main)"><i class="fas fa-check me-1"></i>{{ $e->approver->name }}</small>
                        @elseif($e->rejecter)
                        <small style="color:#dc2626"><i class="fas fa-times me-1"></i>{{ $e->rejecter->name }}</small>
                        @else <small class="text-muted">—</small> @endif
                    </td>
                    <td><strong>{{ number_format($e->total_debit,2) }}</strong></td>
                    <td>{!! $e->status_badge !!}</td>
                    <td>
                        <button class="btn btn-sm px-2" style="background:#f0fdf9;color:var(--green-main);border-radius:6px;font-size:.72rem;border:1px solid #a7f3d0"
                            onclick="showEntryDetail({{ $e->id }},'{{ $e->entry_number }}','{{ addslashes($e->description) }}','{{ $e->entry_date->format('d/m/Y') }}','{{ number_format($e->total_debit,2) }}','{{ $e->status }}','{{ addslashes($e->rejection_reason ?? '') }}')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-5" style="color:#9ca3af"><i class="fas fa-book fa-2x d-block mb-2"></i>No entries found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($entries->hasPages())<div class="p-3">{{ $entries->withQueryString()->links() }}</div>@endif
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="entryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none">
            <div class="modal-header" style="border:none;padding:20px 24px 0">
                <h5 class="modal-title" style="color:var(--green-dark);font-weight:700"><i class="fas fa-book me-2" style="color:var(--green-main)"></i>Journal Entry Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:20px 24px">
                <table class="table table-sm">
                    <tr><td style="color:#6b7280;font-size:.82rem;width:40%">Entry Number</td><td id="d_number" style="font-weight:700;color:var(--green-main)"></td></tr>
                    <tr><td style="color:#6b7280;font-size:.82rem">Date</td><td id="d_date"></td></tr>
                    <tr><td style="color:#6b7280;font-size:.82rem">Description</td><td id="d_desc"></td></tr>
                    <tr><td style="color:#6b7280;font-size:.82rem">Amount (RM)</td><td id="d_amount" style="font-weight:700"></td></tr>
                    <tr><td style="color:#6b7280;font-size:.82rem">Status</td><td id="d_status"></td></tr>
                    <tr id="d_reason_row"><td style="color:#6b7280;font-size:.82rem">Rejection Reason</td><td id="d_reason" style="color:#dc2626"></td></tr>
                </table>
            </div>
            <div class="modal-footer" style="border:none;padding:0 24px 20px">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showEntryDetail(id, num, desc, date, amount, status, reason) {
    document.getElementById('d_number').textContent = num;
    document.getElementById('d_date').textContent   = date;
    document.getElementById('d_desc').textContent   = desc;
    document.getElementById('d_amount').textContent = 'RM ' + amount;
    document.getElementById('d_status').innerHTML   = `<span class="status-badge badge-${status==='approved'?'approved':status==='pending'?'pending':status==='rejected'?'rejected':'draft'}">${status.toUpperCase()}</span>`;
    document.getElementById('d_reason').textContent = reason || '—';
    document.getElementById('d_reason_row').style.display = status === 'rejected' ? '' : 'none';
    new bootstrap.Modal(document.getElementById('entryModal')).show();
}
</script>
@endpush
