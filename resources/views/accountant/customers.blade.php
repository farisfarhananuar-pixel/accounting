@extends('layouts.app')
@section('title','Customers')
@section('page_title','Customers')
@section('page_subtitle','Manage your customer directory')

@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.journal_entries') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-book"></i></span> Journal Entries</a>
<a href="{{ route('accountant.account_receivable') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span> Account Receivable</a>
<a href="{{ route('accountant.account_payable') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-receipt"></i></span> Account Payable</a>
<a href="{{ route('accountant.customers') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-users"></i></span> Customers</a>
<a href="{{ route('accountant.vendors') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-truck"></i></span> Vendors</a>
<a href="{{ route('accountant.customers') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-users"></i></span> Customers</a>
<a href="{{ route('accountant.vendors') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-truck"></i></span> Vendors</a>
<a href="{{ route('accountant.chart_of_account') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-list-alt"></i></span> Chart of Accounts</a>
@endsection

@section('content')
@if(session('success'))
<div class="alert alert-dismissible fade show" style="background:#d1fae5;color:#065f46;border:none;border-radius:12px" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-dismissible fade show" style="background:#fee2e2;color:#991b1b;border:none;border-radius:12px" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    {{-- Add Customer Form --}}
    <div class="col-12 col-md-4">
        <div class="chart-card">
            <h6 style="color:var(--green-dark);font-weight:700;margin-bottom:20px"><i class="fas fa-user-plus me-2" style="color:var(--green-main)"></i>Add New Customer</h6>
            @if($errors->any())
            <div class="alert" style="background:#fef2f2;color:#dc2626;border-radius:8px;padding:10px 14px;font-size:.82rem;margin-bottom:16px">
                <i class="fas fa-exclamation-circle me-1"></i>{{ $errors->first() }}
            </div>
            @endif
            <form method="POST" action="{{ route('accountant.customers.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Customer Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Syarikat ABC Sdn Bhd" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="customer@email.com" value="{{ old('email') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Phone</label>
                    <input type="text" name="phone" class="form-control" placeholder="e.g. 03-1234 5678" value="{{ old('phone') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Address</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Full address...">{{ old('address') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Tax Number (SST/GST)</label>
                    <input type="text" name="tax_number" class="form-control" placeholder="e.g. W10-1234-56789012" value="{{ old('tax_number') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" style="font-size:.82rem;font-weight:600">Credit Limit (RM)</label>
                    <input type="number" name="credit_limit" class="form-control" placeholder="0.00" step="0.01" min="0" value="{{ old('credit_limit', 0) }}">
                </div>
                <button type="submit" class="btn btn-green w-100"><i class="fas fa-plus me-2"></i> Add Customer</button>
            </form>
        </div>
    </div>

    {{-- Customer List --}}
    <div class="col-12 col-md-8">
        <div class="data-table">
            <div class="table-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6><i class="fas fa-users me-2" style="color:var(--green-main)"></i>Customers ({{ $customers->total() }})</h6>
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name / email / code..." value="{{ request('search') }}" style="min-width:200px">
                    <select name="status" class="form-select form-select-sm" style="width:auto">
                        <option value="">All</option>
                        <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                        <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
                    </select>
                    <button class="btn btn-green btn-sm">Filter</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Email / Phone</th>
                            <th>Credit Limit</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $c)
                        <tr>
                            <td><strong style="color:var(--green-main)">{{ $c->customer_code }}</strong></td>
                            <td>{{ $c->name }}</td>
                            <td>
                                @if($c->email)<div style="font-size:.82rem">{{ $c->email }}</div>@endif
                                @if($c->phone)<div style="font-size:.78rem;color:#6b7280">{{ $c->phone }}</div>@endif
                            </td>
                            <td>RM {{ number_format($c->credit_limit, 2) }}</td>
                            <td>
                                @if($c->is_active)
                                    <span class="badge" style="background:#d1fae5;color:#065f46">Active</span>
                                @else
                                    <span class="badge" style="background:#fee2e2;color:#991b1b">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm" style="background:#f0fdf4;color:var(--green-dark);border:1px solid #bbf7d0"
                                    onclick="openEditCustomer({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ $c->email }}', '{{ $c->phone }}', '{{ addslashes($c->address ?? '') }}', '{{ $c->tax_number }}', {{ $c->credit_limit }}, {{ $c->is_active ? 1 : 0 }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="{{ route('accountant.customers.delete', $c->id) }}" style="display:inline" onsubmit="return confirm('Delete customer {{ addslashes($c->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center" style="color:#9ca3af;padding:40px 0">
                            <i class="fas fa-users fa-2x mb-2" style="display:block;opacity:.3"></i>No customers found. Add your first customer!
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $customers->withQueryString()->links() }}</div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:16px;border:none">
            <div class="modal-header" style="border-bottom:1px solid #e5e7eb">
                <h6 class="modal-title" style="font-weight:700;color:var(--green-dark)"><i class="fas fa-edit me-2"></i>Edit Customer</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editCustomerForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.82rem;font-weight:600">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.82rem;font-weight:600">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.82rem;font-weight:600">Phone</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.82rem;font-weight:600">Address</label>
                        <textarea name="address" id="edit_address" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.82rem;font-weight:600">Tax Number</label>
                        <input type="text" name="tax_number" id="edit_tax_number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:.82rem;font-weight:600">Credit Limit (RM)</label>
                        <input type="number" name="credit_limit" id="edit_credit_limit" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                            <label class="form-check-label" for="edit_is_active" style="font-size:.82rem;font-weight:600">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e5e7eb">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-green btn-sm"><i class="fas fa-save me-1"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditCustomer(id, name, email, phone, address, tax, credit, active) {
    document.getElementById('editCustomerForm').action = '/accountant/customers/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_address').value = address;
    document.getElementById('edit_tax_number').value = tax;
    document.getElementById('edit_credit_limit').value = credit;
    document.getElementById('edit_is_active').checked = active == 1;
    new bootstrap.Modal(document.getElementById('editCustomerModal')).show();
}
</script>
@endsection
