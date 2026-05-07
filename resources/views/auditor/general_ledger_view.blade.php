@extends('layouts.app')
@section('title','General Ledger') @section('page_title','General Ledger View') @section('page_subtitle','Read-only ledger view')
@section('sidebar_nav') @include('auditor.partials.sidebar') @endsection
@section('content')
<div class="chart-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-6">
            <label class="form-label" style="font-size:.82rem;font-weight:600">Select Account</label>
            <select name="account_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- Choose an Account --</option>
                @foreach($accounts->groupBy('account_type') as $type => $accs)
                <optgroup label="{{ strtoupper($type) }}">
                    @foreach($accs as $acc)<option value="{{ $acc->id }}" {{ $selectedAccount==$acc->id?'selected':'' }}>{{ $acc->account_code }} — {{ $acc->account_name }}</option>@endforeach
                </optgroup>
                @endforeach
            </select>
        </div>
    </form>
</div>
@if($selectedAccount && $entries->count())
<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-journal-whills me-2" style="color:var(--green-main)"></i>Ledger Entries (Read Only)</h6></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Date</th><th>Journal No.</th><th>Description</th><th class="text-end">Debit (RM)</th><th class="text-end">Credit (RM)</th></tr></thead>
        <tbody>
            @php $runningBalance = 0; @endphp
            @foreach($entries as $line)
            @php $runningBalance += $line->debit - $line->credit; @endphp
            <tr>
                <td>{{ $line->journalEntry->entry_date->format('d/m/Y') }}</td>
                <td><strong style="color:var(--green-main)">{{ $line->journalEntry->entry_number }}</strong></td>
                <td>{{ $line->description ?: $line->journalEntry->description }}</td>
                <td class="text-end">{{ $line->debit > 0 ? number_format($line->debit,2) : '—' }}</td>
                <td class="text-end">{{ $line->credit > 0 ? number_format($line->credit,2) : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#f9fafb">
                <td colspan="3" style="font-weight:700;font-size:.85rem;color:var(--green-dark)">Running Balance</td>
                <td colspan="2" class="text-end" style="font-weight:800;color:var(--green-main)">RM {{ number_format($runningBalance,2) }}</td>
            </tr>
        </tfoot>
    </table></div>
    @if($entries->hasPages())<div class="p-3">{{ $entries->withQueryString()->links() }}</div>@endif
</div>
@elseif($selectedAccount)
<div class="chart-card text-center py-5" style="color:#9ca3af"><i class="fas fa-inbox fa-2x d-block mb-2"></i>No approved entries for this account</div>
@else
<div class="chart-card text-center py-5" style="color:#9ca3af"><i class="fas fa-hand-point-up fa-2x d-block mb-2" style="color:var(--green-main)"></i><p>Select an account above to view ledger entries</p></div>
@endif
@endsection
