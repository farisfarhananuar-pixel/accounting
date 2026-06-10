@extends('layouts.app')
@section('title','Manage Transactions') @section('page_title','Manage Transactions') @section('page_subtitle','Force delete erroneous transactions')
@section('sidebar_nav')
<span class="nav-section-title">Main Menu</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item-link {{ request()->routeIs('admin.dashboard')?'active':'' }}"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<span class="nav-section-title">User Management</span>
<a href="{{ route('admin.users') }}" class="nav-item-link {{ request()->routeIs('admin.users')?'active':'' }}"><span class="nav-icon"><i class="fas fa-users"></i></span> Manage Users</a>
<a href="{{ route('admin.create_roles') }}" class="nav-item-link {{ request()->routeIs('admin.create_roles')?'active':'' }}"><span class="nav-icon"><i class="fas fa-user-shield"></i></span> Create Roles</a>
<span class="nav-section-title">Data Management</span>
<a href="{{ route('admin.transactions') }}" class="nav-item-link {{ request()->routeIs('admin.transactions')?'active':'' }}"><span class="nav-icon"><i class="fas fa-trash-alt"></i></span> Manage Transactions</a>
@endsection

@section('content')
<div class="container-fluid px-0">

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Warning Banner --}}
    <div class="alert mb-4" style="background:#fff3cd;border:1px solid #ffc107;border-radius:10px;padding:14px 18px;">
        <i class="fas fa-exclamation-triangle me-2" style="color:#856404"></i>
        <strong style="color:#856404">Admin Override:</strong>
        <span style="color:#533f03"> Deletions here are permanent and bypass normal workflow restrictions. All actions are recorded in the audit trail.</span>
    </div>

    {{-- Tabs --}}
    <div class="d-flex gap-2 mb-4 flex-wrap">
        <a href="{{ route('admin.transactions', ['type'=>'journal']) }}"
           class="tab-btn {{ $type==='journal'?'tab-active':'' }}">
            <i class="fas fa-book me-1"></i> Journal Entries
            <span class="tab-count">{{ $journals->count() }}</span>
        </a>
        <a href="{{ route('admin.transactions', ['type'=>'invoice']) }}"
           class="tab-btn {{ $type==='invoice'?'tab-active':'' }}" style="--tab-color:#3b82f6">
            <i class="fas fa-file-invoice me-1"></i> Invoices (AR)
            <span class="tab-count">{{ $invoices->count() }}</span>
        </a>
        <a href="{{ route('admin.transactions', ['type'=>'bill']) }}"
           class="tab-btn {{ $type==='bill'?'tab-active':'' }}" style="--tab-color:#f59e0b">
            <i class="fas fa-receipt me-1"></i> Bills (AP)
            <span class="tab-count">{{ $bills->count() }}</span>
        </a>
    </div>

    {{-- Journal Entries Table --}}
    @if($type === 'journal')
    <div class="card-panel">
        <div class="table-header">
            <h6><i class="fas fa-book me-2" style="color:var(--green-main)"></i>Journal Entries</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Entry No.</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Created By</th>
                        <th>Status</th>
                        <th class="text-end">Debit (RM)</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($journals as $j)
                <tr>
                    <td><strong>{{ $j->entry_number }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($j->date)->format('d M Y') }}</td>
                    <td>{{ Str::limit($j->description, 50) }}</td>
                    <td>{{ $j->creator->name ?? '-' }}</td>
                    <td>{!! $j->status_badge !!}</td>
                    <td class="text-end">{{ number_format($j->total_debit, 2) }}</td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('admin.transactions.journal.delete', $j->id) }}"
                              onsubmit="return confirmDelete('journal entry {{ $j->entry_number }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#991b1b;border-radius:6px;font-size:.75rem;font-weight:600;padding:4px 10px">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-check-circle me-2 text-success"></i>No journal entries found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Invoices Table --}}
    @if($type === 'invoice')
    <div class="card-panel">
        <div class="table-header">
            <h6><i class="fas fa-file-invoice me-2" style="color:#3b82f6"></i>Invoices (AR)</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Invoice No.</th>
                        <th>Customer</th>
                        <th>Issue Date</th>
                        <th>Created By</th>
                        <th>Status</th>
                        <th class="text-end">Amount (RM)</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td><strong>{{ $inv->invoice_number }}</strong></td>
                    <td>{{ $inv->customer->company_name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($inv->issue_date)->format('d M Y') }}</td>
                    <td>{{ $inv->creator->name ?? '-' }}</td>
                    <td>{!! $inv->status_badge !!}</td>
                    <td class="text-end">{{ number_format($inv->total_amount, 2) }}</td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('admin.transactions.invoice.delete', $inv->id) }}"
                              onsubmit="return confirmDelete('invoice {{ $inv->invoice_number }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#991b1b;border-radius:6px;font-size:.75rem;font-weight:600;padding:4px 10px">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-check-circle me-2 text-success"></i>No invoices found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Bills Table --}}
    @if($type === 'bill')
    <div class="card-panel">
        <div class="table-header">
            <h6><i class="fas fa-receipt me-2" style="color:#f59e0b"></i>Bills (AP)</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Bill No.</th>
                        <th>Vendor</th>
                        <th>Bill Date</th>
                        <th>Created By</th>
                        <th>Status</th>
                        <th class="text-end">Amount (RM)</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($bills as $bill)
                <tr>
                    <td><strong>{{ $bill->bill_number }}</strong></td>
                    <td>{{ $bill->vendor->company_name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($bill->bill_date)->format('d M Y') }}</td>
                    <td>{{ $bill->creator->name ?? '-' }}</td>
                    <td>{!! $bill->status_badge !!}</td>
                    <td class="text-end">{{ number_format($bill->total_amount, 2) }}</td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('admin.transactions.bill.delete', $bill->id) }}"
                              onsubmit="return confirmDelete('bill {{ $bill->bill_number }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#991b1b;border-radius:6px;font-size:.75rem;font-weight:600;padding:4px 10px">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-check-circle me-2 text-success"></i>No bills found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

<style>
.tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: .85rem;
    font-weight: 600;
    text-decoration: none;
    background: #f1f5f9;
    color: #475569;
    border: 2px solid transparent;
    transition: all .2s;
}
.tab-btn:hover { background: #e2e8f0; color: #1e293b; }
.tab-active {
    background: #d1fae5 !important;
    color: var(--green-main, #065f46) !important;
    border-color: var(--green-main, #065f46) !important;
}
.tab-count {
    background: rgba(0,0,0,.1);
    border-radius: 20px;
    padding: 1px 8px;
    font-size: .75rem;
}
.card-panel {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,.08);
    overflow: hidden;
}
.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
}
.table-header h6 { margin: 0; font-weight: 700; }
.table thead th {
    background: #f8fafc;
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #64748b;
    padding: 10px 16px;
    border-bottom: 1px solid #e2e8f0;
}
.table tbody td { padding: 12px 16px; font-size: .875rem; }
</style>

<script>
function confirmDelete(label) {
    return confirm('⚠️ Permanently delete ' + label + '?\n\nThis cannot be undone. The action will be recorded in the audit trail.');
}
</script>
@endsection
