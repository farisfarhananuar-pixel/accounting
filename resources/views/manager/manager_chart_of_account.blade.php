@extends('layouts.app')
@section('title','Chart of Accounts') @section('page_title','Chart of Accounts') @section('page_subtitle','View only')
@section('sidebar_nav') @include('manager.partials.sidebar') @endsection
@section('content')
<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-list-alt me-2" style="color:var(--green-main)"></i>Chart of Accounts ({{ $accounts->total() }})</h6></div>
    <div class="table-responsive"><table class="table"><thead><tr><th>Code</th><th>Account Name</th><th>Type</th><th>Category</th><th>Balance (RM)</th><th>Status</th></tr></thead>
    <tbody>@forelse($accounts as $acc)
    <tr><td><strong style="color:var(--green-main)">{{ $acc->account_code }}</strong></td><td>{{ $acc->account_name }}</td>
    <td>@php $tc=['asset'=>'badge-approved','liability'=>'badge-rejected','equity'=>'badge-pending','revenue'=>'badge-paid','expense'=>'badge-draft']; @endphp
    <span class="status-badge {{ $tc[$acc->account_type]??'badge-draft' }}">{{ ucfirst($acc->account_type) }}</span></td>
    <td><small class="text-muted">{{ str_replace('_',' ',ucfirst($acc->account_category)) }}</small></td>
    <td>{{ number_format($acc->current_balance,2) }}</td>
    <td><span class="status-badge {{ $acc->is_active?'badge-approved':'badge-rejected' }}">{{ $acc->is_active?'Active':'Inactive' }}</span></td>
    </tr>
    @empty<tr><td colspan="6" class="text-center py-4" style="color:#9ca3af">No accounts</td></tr>@endforelse</tbody></table></div>
    @if($accounts->hasPages())<div class="p-3">{{ $accounts->links() }}</div>@endif
</div>
@endsection
