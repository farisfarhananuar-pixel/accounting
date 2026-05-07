@extends('layouts.app')
@section('title','Approve / Reject')
@section('page_title','Approve / Reject Transactions')
@section('page_subtitle','Review and action pending submissions')
@section('sidebar_nav') @include('manager.partials.sidebar') @endsection

@section('content')
@if(session('success'))
<div class="alert alert-dismissible fade show" style="background:#d1fae5;color:#065f46;border:none;border-radius:12px" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Tabs --}}
<div class="chart-card mb-4">
    <ul class="nav nav-pills gap-2" id="approvalTabs">
        <li class="nav-item">
            <a class="nav-link {{ $type=='journal'?'active':'' }}" href="?type=journal"
               style="{{ $type=='journal' ? 'background:var(--green-main);color:white' : 'color:var(--green-main);background:var(--green-pale)' }};border-radius:8px;font-weight:600;font-size:.85rem">
                <i class="fas fa-book me-1"></i> Journal Entries
                <span class="badge" style="background:rgba(255,255,255,0.3);margin-left:4px">{{ $pendingJournals->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $type=='invoice'?'active':'' }}" href="?type=invoice"
               style="{{ $type=='invoice' ? 'background:#0369a1;color:white' : 'color:#0369a1;background:#eff6ff' }};border-radius:8px;font-weight:600;font-size:.85rem">
                <i class="fas fa-file-invoice-dollar me-1"></i> Invoices (AR)
                <span class="badge" style="background:rgba(255,255,255,0.3);margin-left:4px">{{ $pendingInvoices->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $type=='bill'?'active':'' }}" href="?type=bill"
               style="{{ $type=='bill' ? 'background:#d97706;color:white' : 'color:#d97706;background:#fef3c7' }};border-radius:8px;font-weight:600;font-size:.85rem">
                <i class="fas fa-receipt me-1"></i> Bills (AP)
                <span class="badge" style="background:rgba(255,255,255,0.3);margin-left:4px">{{ $pendingBills->count() }}</span>
            </a>
        </li>
    </ul>
</div>

{{-- Journal Entries Tab --}}
@if($type == 'journal')
<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-book me-2" style="color:var(--green-main)"></i>Pending Journal Entries ({{ $pendingJournals->count() }})</h6></div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Entry No.</th><th>Date</th><th>Description</th><th>Submitted By</th><th>Debit (RM)</th><th>Credit (RM)</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($pendingJournals as $j)
                <tr>
                    <td><strong style="color:var(--green-main)">{{ $j->entry_number }}</strong></td>
                    <td>{{ $j->entry_date->format('d/m/Y') }}</td>
                    <td>{{ Str::limit($j->description, 40) }}</td>
                    <td><small>{{ $j->creator->name ?? '-' }}</small></td>
                    <td><strong>{{ number_format($j->total_debit,2) }}</strong></td>
                    <td><strong>{{ number_format($j->total_credit,2) }}</strong></td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            {{-- Approve --}}
                            <form method="POST" action="{{ route('manager.approve', ['type'=>'journal','id'=>$j->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm px-3" style="background:#d1fae5;color:#065f46;border-radius:8px;font-weight:700;font-size:.78rem"
                                    onclick="return confirm('Approve this journal entry?')">
                                    <i class="fas fa-check me-1"></i> Approve
                                </button>
                            </form>
                            {{-- Reject --}}
                            <button type="button" class="btn btn-sm px-3" style="background:#fee2e2;color:#991b1b;border-radius:8px;font-weight:700;font-size:.78rem"
                                onclick="showRejectModal('journal','{{ $j->id }}','{{ $j->entry_number }}')">
                                <i class="fas fa-times me-1"></i> Reject
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-5" style="color:#9ca3af"><i class="fas fa-check-circle fa-2x d-block mb-2" style="color:var(--green-main)"></i>No pending journal entries</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Invoice Tab --}}
@elseif($type == 'invoice')
<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-file-invoice-dollar me-2" style="color:#0369a1"></i>Pending Invoices ({{ $pendingInvoices->count() }})</h6></div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Invoice No.</th><th>Customer</th><th>Date</th><th>Due Date</th><th>Amount (RM)</th><th>Submitted By</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($pendingInvoices as $inv)
                <tr>
                    <td><strong style="color:#0369a1">{{ $inv->invoice_number }}</strong></td>
                    <td>{{ $inv->customer->name ?? '-' }}</td>
                    <td>{{ $inv->invoice_date->format('d/m/Y') }}</td>
                    <td>{{ $inv->due_date->format('d/m/Y') }}</td>
                    <td><strong>{{ number_format($inv->total_amount,2) }}</strong></td>
                    <td><small>{{ $inv->creator->name ?? '-' }}</small></td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="POST" action="{{ route('manager.approve', ['type'=>'invoice','id'=>$inv->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm px-3" style="background:#d1fae5;color:#065f46;border-radius:8px;font-weight:700;font-size:.78rem" onclick="return confirm('Approve this invoice?')">
                                    <i class="fas fa-check me-1"></i> Approve
                                </button>
                            </form>
                            <button type="button" class="btn btn-sm px-3" style="background:#fee2e2;color:#991b1b;border-radius:8px;font-weight:700;font-size:.78rem"
                                onclick="showRejectModal('invoice','{{ $inv->id }}','{{ $inv->invoice_number }}')">
                                <i class="fas fa-times me-1"></i> Reject
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-5" style="color:#9ca3af"><i class="fas fa-check-circle fa-2x d-block mb-2" style="color:#0369a1"></i>No pending invoices</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Bills Tab --}}
@else
<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-receipt me-2" style="color:#d97706"></i>Pending Bills ({{ $pendingBills->count() }})</h6></div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Bill No.</th><th>Vendor</th><th>Bill Date</th><th>Due Date</th><th>Amount (RM)</th><th>Submitted By</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($pendingBills as $bill)
                <tr>
                    <td><strong style="color:#d97706">{{ $bill->bill_number }}</strong></td>
                    <td>{{ $bill->vendor->name ?? '-' }}</td>
                    <td>{{ $bill->bill_date->format('d/m/Y') }}</td>
                    <td>{{ $bill->due_date->format('d/m/Y') }}</td>
                    <td><strong>{{ number_format($bill->total_amount,2) }}</strong></td>
                    <td><small>{{ $bill->creator->name ?? '-' }}</small></td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="POST" action="{{ route('manager.approve', ['type'=>'bill','id'=>$bill->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm px-3" style="background:#d1fae5;color:#065f46;border-radius:8px;font-weight:700;font-size:.78rem" onclick="return confirm('Approve this bill?')">
                                    <i class="fas fa-check me-1"></i> Approve
                                </button>
                            </form>
                            <button type="button" class="btn btn-sm px-3" style="background:#fee2e2;color:#991b1b;border-radius:8px;font-weight:700;font-size:.78rem"
                                onclick="showRejectModal('bill','{{ $bill->id }}','{{ $bill->bill_number }}')">
                                <i class="fas fa-times me-1"></i> Reject
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-5" style="color:#9ca3af"><i class="fas fa-check-circle fa-2x d-block mb-2" style="color:#d97706"></i>No pending bills</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;box-shadow:0 20px 60px rgba(0,0,0,0.2)">
            <div class="modal-header" style="border:none;padding:20px 24px 0">
                <h5 class="modal-title" style="color:var(--green-dark);font-weight:700"><i class="fas fa-times-circle me-2" style="color:#dc2626"></i>Reject Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-body" style="padding:20px 24px">
                    <p style="color:#6b7280;font-size:.85rem">You are rejecting: <strong id="rejectRef" style="color:var(--green-dark)"></strong></p>
                    <label class="form-label" style="font-size:.82rem;font-weight:700">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Explain why this transaction is being rejected..." required style="resize:none;border-radius:10px;border:2px solid #e5e7eb"></textarea>
                    <small class="text-muted">Min. 5 characters. This reason will be visible to the accountant.</small>
                </div>
                <div class="modal-footer" style="border:none;padding:0 24px 20px;gap:10px">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn px-4" style="background:#dc2626;color:white;border-radius:8px;font-weight:600">
                        <i class="fas fa-times me-2"></i>Confirm Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showRejectModal(type, id, ref) {
    document.getElementById('rejectRef').textContent = ref;
    document.getElementById('rejectForm').action = `/manager/reject/${type}/${id}`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush
