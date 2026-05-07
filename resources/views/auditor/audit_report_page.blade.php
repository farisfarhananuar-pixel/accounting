@extends('layouts.app')
@section('title','Audit Report') @section('page_title','Generate Audit Report') @section('page_subtitle','Create comprehensive audit reports')
@section('sidebar_nav') @include('auditor.partials.sidebar') @endsection
@section('content')
<div class="row justify-content-center">
<div class="col-12 col-md-8">
<div class="chart-card">
    <h5 style="color:var(--green-dark);font-weight:800;margin-bottom:6px"><i class="fas fa-file-contract me-2" style="color:var(--green-main)"></i>Generate Audit Report</h5>
    <p class="text-muted" style="font-size:.85rem;margin-bottom:28px">Select report type and date range to generate a comprehensive audit report.</p>

    <form method="POST" action="{{ route('auditor.audit_report.generate') }}">
        @csrf
        <div class="mb-4">
            <label class="form-label" style="font-size:.85rem;font-weight:700">Report Type <span class="text-danger">*</span></label>
            <div class="row g-2">
                @php
                $types = [
                    ['full_audit','Full Audit Report','fas fa-clipboard-list','#1a7a57','#d1fae5','All journals, AR, AP and user activity'],
                    ['journal_audit','Journal Entry Audit','fas fa-book','#0369a1','#dbeafe','All journal entries with approval status'],
                    ['ar_audit','AR Audit','fas fa-file-invoice-dollar','#7c3aed','#ede9fe','All invoices and receivable transactions'],
                    ['ap_audit','AP Audit','fas fa-receipt','#d97706','#fef3c7','All bills and payable transactions'],
                    ['user_activity','User Activity Report','fas fa-users','#0ea5a0','#f0fdfa','System activity by user — actions taken'],
                    ['financial_integrity','Financial Integrity Check','fas fa-shield-alt','#dc2626','#fee2e2','Check for unbalanced entries and anomalies'],
                ];
                @endphp
                @foreach($types as $t)
                <div class="col-6 col-md-4">
                    <label style="cursor:pointer;display:block">
                        <input type="radio" name="report_type" value="{{ $t[0] }}" class="d-none report-radio">
                        <div class="report-type-card p-3" style="border:2px solid #e5e7eb;border-radius:12px;transition:all .2s;text-align:center">
                            <i class="{{ $t[2] }} fa-lg mb-2 d-block" style="color:{{ $t[3] }}"></i>
                            <div style="font-size:.78rem;font-weight:700;color:#374151;margin-bottom:3px">{{ $t[1] }}</div>
                            <div style="font-size:.68rem;color:#9ca3af">{{ $t[5] }}</div>
                        </div>
                    </label>
                </div>
                @endforeach
            </div>
            @error('report_type')<div style="color:#dc2626;font-size:.8rem;margin-top:6px">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label" style="font-size:.85rem;font-weight:700">Date From <span class="text-danger">*</span></label>
                <input type="date" name="date_from" class="form-control" value="{{ old('date_from', now()->startOfMonth()->format('Y-m-d')) }}" required>
                @error('date_from')<div style="color:#dc2626;font-size:.8rem">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" style="font-size:.85rem;font-weight:700">Date To <span class="text-danger">*</span></label>
                <input type="date" name="date_to" class="form-control" value="{{ old('date_to', now()->format('Y-m-d')) }}" required>
                @error('date_to')<div style="color:#dc2626;font-size:.8rem">{{ $message }}</div>@enderror
            </div>
        </div>

        <button type="submit" class="btn btn-green w-100 py-3" style="font-size:.95rem">
            <i class="fas fa-file-contract me-2"></i> Generate Report
        </button>
    </form>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.report-radio').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.report-type-card').forEach(c => {
            c.style.borderColor = '#e5e7eb';
            c.style.background = 'white';
        });
        const card = radio.nextElementSibling;
        card.style.borderColor = 'var(--green-main)';
        card.style.background = 'var(--green-pale)';
    });
});
</script>
@endpush
