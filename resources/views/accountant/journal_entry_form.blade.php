@extends('layouts.app')
@section('title','New Journal Entry')
@section('page_title','New Journal Entry')
@section('page_subtitle','Create a balanced journal entry')

@section('sidebar_nav')
<a href="{{ route('accountant.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('accountant.journal_entries') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-book"></i></span> Journal Entries</a>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-xl-10">

<div class="chart-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 style="color:var(--green-dark);font-weight:700;margin:0"><i class="fas fa-plus-circle me-2" style="color:var(--green-main)"></i>New Journal Entry</h5>
        <a href="{{ route('accountant.journal_entries') }}" class="btn btn-sm btn-outline-secondary px-3">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    @if($errors->any())
    <div class="alert" style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#dc2626;padding:12px 16px;margin-bottom:20px">
        <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('accountant.journal_entries.store') }}" id="journalForm">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label" style="font-size:.82rem;font-weight:600">Entry Number</label>
                <input type="text" class="form-control" value="{{ $nextNumber }}" readonly style="background:#f9fafb;font-weight:700;color:var(--green-main)">
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size:.82rem;font-weight:600">Entry Date <span class="text-danger">*</span></label>
                <input type="date" name="entry_date" class="form-control" value="{{ old('entry_date', date('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size:.82rem;font-weight:600">Reference</label>
                <input type="text" name="reference" class="form-control" placeholder="e.g. Receipt No. / Cheque No." value="{{ old('reference') }}">
            </div>
            <div class="col-12">
                <label class="form-label" style="font-size:.82rem;font-weight:600">Description <span class="text-danger">*</span></label>
                <input type="text" name="description" class="form-control" placeholder="Describe this journal entry..." value="{{ old('description') }}" required>
            </div>
        </div>

        {{-- Journal Lines --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 style="color:var(--green-dark);font-weight:700;margin:0">Journal Lines</h6>
                <button type="button" class="btn btn-sm btn-green px-3" onclick="addLine()">
                    <i class="fas fa-plus me-1"></i> Add Line
                </button>
            </div>

            <div class="table-responsive">
                <table class="table" style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden">
                    <thead>
                        <tr>
                            <th style="width:35%">Account</th>
                            <th>Description</th>
                            <th style="width:15%">Debit (RM)</th>
                            <th style="width:15%">Credit (RM)</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="journalLines">
                        <tr class="journal-line">
                            <td>
                                <select name="lines[0][account_id]" class="form-select form-select-sm" required>
                                    <option value="">-- Select Account --</option>
                                    @foreach($accounts->groupBy('account_type') as $type => $accs)
                                    <optgroup label="{{ strtoupper($type) }}">
                                        @foreach($accs as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="lines[0][description]" class="form-control form-control-sm" placeholder="Optional note"></td>
                            <td><input type="number" name="lines[0][debit]" class="form-control form-control-sm debit-input" step="0.01" min="0" value="0" oninput="updateTotals()"></td>
                            <td><input type="number" name="lines[0][credit]" class="form-control form-control-sm credit-input" step="0.01" min="0" value="0" oninput="updateTotals()"></td>
                            <td><button type="button" class="btn btn-sm px-2" style="background:#fee2e2;color:#dc2626;border-radius:6px" onclick="removeLine(this)"><i class="fas fa-times"></i></button></td>
                        </tr>
                        <tr class="journal-line">
                            <td>
                                <select name="lines[1][account_id]" class="form-select form-select-sm" required>
                                    <option value="">-- Select Account --</option>
                                    @foreach($accounts->groupBy('account_type') as $type => $accs)
                                    <optgroup label="{{ strtoupper($type) }}">
                                        @foreach($accs as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="lines[1][description]" class="form-control form-control-sm" placeholder="Optional note"></td>
                            <td><input type="number" name="lines[1][debit]" class="form-control form-control-sm debit-input" step="0.01" min="0" value="0" oninput="updateTotals()"></td>
                            <td><input type="number" name="lines[1][credit]" class="form-control form-control-sm credit-input" step="0.01" min="0" value="0" oninput="updateTotals()"></td>
                            <td><button type="button" class="btn btn-sm px-2" style="background:#fee2e2;color:#dc2626;border-radius:6px" onclick="removeLine(this)"><i class="fas fa-times"></i></button></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background:#f9fafb">
                            <td colspan="2" style="font-weight:700;font-size:.85rem;padding:12px 16px">TOTAL</td>
                            <td style="font-weight:800;color:var(--green-main);padding:12px 16px" id="totalDebit">RM 0.00</td>
                            <td style="font-weight:800;color:var(--green-main);padding:12px 16px" id="totalCredit">RM 0.00</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Balance indicator --}}
            <div id="balanceAlert" class="mt-2 p-2 rounded" style="font-size:.82rem;font-weight:600;display:none"></div>
        </div>

        <div class="d-flex gap-2 justify-content-end mt-4">
            <a href="{{ route('accountant.journal_entries') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-green px-4" id="submitBtn">
                <i class="fas fa-save me-2"></i> Save as Draft
            </button>
        </div>
    </form>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
let lineCount = 2;
const accountOptions = `@foreach($accounts->groupBy('account_type') as $type => $accs)<optgroup label="{{ strtoupper($type) }}">@foreach($accs as $acc)<option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>@endforeach</optgroup>@endforeach`;

function addLine() {
    const tbody = document.getElementById('journalLines');
    const tr = document.createElement('tr');
    tr.className = 'journal-line';
    tr.innerHTML = `
        <td><select name="lines[${lineCount}][account_id]" class="form-select form-select-sm" required><option value="">-- Select Account --</option>${accountOptions}</select></td>
        <td><input type="text" name="lines[${lineCount}][description]" class="form-control form-control-sm" placeholder="Optional note"></td>
        <td><input type="number" name="lines[${lineCount}][debit]" class="form-control form-control-sm debit-input" step="0.01" min="0" value="0" oninput="updateTotals()"></td>
        <td><input type="number" name="lines[${lineCount}][credit]" class="form-control form-control-sm credit-input" step="0.01" min="0" value="0" oninput="updateTotals()"></td>
        <td><button type="button" class="btn btn-sm px-2" style="background:#fee2e2;color:#dc2626;border-radius:6px" onclick="removeLine(this)"><i class="fas fa-times"></i></button></td>
    `;
    tbody.appendChild(tr);
    lineCount++;
}

function removeLine(btn) {
    const lines = document.querySelectorAll('.journal-line');
    if (lines.length <= 2) { alert('Minimum 2 lines required.'); return; }
    btn.closest('tr').remove();
    updateTotals();
}

function updateTotals() {
    let debit = 0, credit = 0;
    document.querySelectorAll('.debit-input').forEach(i => debit += parseFloat(i.value)||0);
    document.querySelectorAll('.credit-input').forEach(i => credit += parseFloat(i.value)||0);
    document.getElementById('totalDebit').textContent  = 'RM ' + debit.toFixed(2);
    document.getElementById('totalCredit').textContent = 'RM ' + credit.toFixed(2);

    const alert = document.getElementById('balanceAlert');
    const diff = Math.abs(debit - credit);
    if (diff < 0.01) {
        alert.style.display = 'block';
        alert.style.background = '#d1fae5'; alert.style.color = '#065f46';
        alert.innerHTML = '<i class="fas fa-check-circle me-2"></i>Journal is balanced ✓';
    } else {
        alert.style.display = 'block';
        alert.style.background = '#fee2e2'; alert.style.color = '#991b1b';
        alert.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>Not balanced — difference: RM ${diff.toFixed(2)}`;
    }
}

updateTotals();
</script>
@endpush
