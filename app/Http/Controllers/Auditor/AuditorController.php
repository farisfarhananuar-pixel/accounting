<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Invoice;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\ChartOfAccount;
use App\Models\LoginLog;
use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditorController extends Controller
{
    private function cid()
    {
        return auth()->user()->company_id;
    }

    // ===================== DASHBOARD =====================
    public function dashboard()
    {
        $cid = $this->cid();

        // Daily transactions recorded today
        $todayJournals  = JournalEntry::where('company_id', $cid)->whereDate('created_at', today())->count();
        $todayInvoices  = Invoice::where('company_id', $cid)->whereDate('created_at', today())->count();
        $todayBills     = Bill::where('company_id', $cid)->whereDate('created_at', today())->count();
        $todayTotal     = $todayJournals + $todayInvoices + $todayBills;

        // Abnormal / unusual activities
        $avgJournal  = JournalEntry::where('company_id', $cid)->avg('total_debit') ?? 0;
        $avgInvoice  = Invoice::where('company_id', $cid)->avg('total_amount') ?? 0;
        $avgBill     = Bill::where('company_id', $cid)->avg('total_amount') ?? 0;

        $journalThreshold = max($avgJournal * 3, 10000);
        $invoiceThreshold = max($avgInvoice * 3, 10000);
        $billThreshold    = max($avgBill * 3, 10000);

        $abnormalJournals = JournalEntry::where('company_id', $cid)
            ->where(fn($q) => $q->where('total_debit', '>', $journalThreshold)->orWhere('total_credit', '>', $journalThreshold))
            ->count();

        $abnormalInvoices = Invoice::where('company_id', $cid)
            ->where('total_amount', '>', $invoiceThreshold)
            ->count();

        $abnormalBills = Bill::where('company_id', $cid)
            ->where('total_amount', '>', $billThreshold)
            ->count();

        $abnormalCount = $abnormalJournals + $abnormalInvoices + $abnormalBills;

        // Failed login attempts today
        $failedLogins = LoginLog::where('company_id', $cid)
            ->where('status', 'failed')
            ->whereDate('created_at', today())
            ->count();

        // Most active users (by audit trail entries this month)
        $mostActiveUsers = AuditTrail::where('company_id', $cid)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->select('user_id', DB::raw('COUNT(*) as activity_count'))
            ->groupBy('user_id')
            ->orderByDesc('activity_count')
            ->with('user')
            ->take(5)
            ->get();

        // Recent audit activities (last 10)
        $recentAudit = AuditTrail::where('company_id', $cid)
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        // Activity chart - last 7 days
        $activityData = [];
        $activityDays = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $activityDays[]  = $d->format('d M');
            $activityData[]  = AuditTrail::where('company_id', $cid)->whereDate('created_at', $d)->count();
        }

        return view('auditor.dashboard', compact(
            'todayTotal', 'todayJournals', 'todayInvoices', 'todayBills',
            'abnormalCount', 'failedLogins',
            'mostActiveUsers', 'recentAudit',
            'activityData', 'activityDays'
        ));
    }

    // ===================== VIEW LOGS =====================
    public function viewLogs(Request $request)
    {
        $cid   = $this->cid();
        $query = LoginLog::where('company_id', $cid)->with('user');

        if ($request->status)    $query->where('status', $request->status);
        if ($request->role)      $query->where('role', $request->role);
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->search)    $query->where('username_attempted', 'like', '%'.$request->search.'%');

        $logs = $query->latest()->paginate(20);

        $totalLogins  = LoginLog::where('company_id', $cid)->where('status', 'success')->count();
        $failedToday  = LoginLog::where('company_id', $cid)->where('status', 'failed')->whereDate('created_at', today())->count();
        $uniqueIPs    = LoginLog::where('company_id', $cid)->distinct('ip_address')->count('ip_address');

        return view('auditor.view_logs', compact('logs', 'totalLogins', 'failedToday', 'uniqueIPs'));
    }

    // ===================== AUDIT TRAIL =====================
    public function auditTrail(Request $request)
    {
        $cid   = $this->cid();
        $query = AuditTrail::where('company_id', $cid)->with('user');

        if ($request->action)    $query->where('action', $request->action);
        if ($request->module)    $query->where('module', $request->module);
        if ($request->user_id)   $query->where('user_id', $request->user_id);
        if ($request->date_from) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->date_to)   $query->whereDate('created_at', '<=', $request->date_to);

        $trails = $query->latest()->paginate(25);
        $users  = User::where('company_id', $cid)->get();

        return view('auditor.audit_trail_page', compact('trails', 'users'));
    }

    // ===================== AUDIT FINANCIAL REPORT =====================
    public function auditFinancialReport()
    {
        $cid = $this->cid();

        $totalApproved   = JournalEntry::where('company_id', $cid)->where('status', 'approved')->count();
        $totalPending    = JournalEntry::where('company_id', $cid)->where('status', 'pending')->count();
        $totalRejected   = JournalEntry::where('company_id', $cid)->where('status', 'rejected')->count();
        $totalJournalAmt = JournalEntry::where('company_id', $cid)->where('status', 'approved')->sum('total_debit');

        $totalInvoices   = Invoice::where('company_id', $cid)->count();
        $invoiceAmt      = Invoice::where('company_id', $cid)->where('status', 'approved')->sum('total_amount');
        $invoicePaid     = Invoice::where('company_id', $cid)->where('status', 'paid')->sum('total_amount');

        $totalBills      = Bill::where('company_id', $cid)->count();
        $billAmt         = Bill::where('company_id', $cid)->where('status', 'approved')->sum('total_amount');
        $billPaid        = Bill::where('company_id', $cid)->where('status', 'paid')->sum('total_amount');

        // Unbalanced entries (debit != credit) - data integrity check
        $unbalanced = JournalEntry::where('company_id', $cid)
            ->whereRaw('ABS(total_debit - total_credit) > 0.01')
            ->count();

        return view('auditor.audit_financial_report', compact(
            'totalApproved', 'totalPending', 'totalRejected', 'totalJournalAmt',
            'totalInvoices', 'invoiceAmt', 'invoicePaid',
            'totalBills', 'billAmt', 'billPaid', 'unbalanced'
        ));
    }

    // ===================== JOURNAL ENTRY VIEW =====================
    public function journalEntries(Request $request)
    {
        $cid   = $this->cid();
        $query = JournalEntry::where('company_id', $cid)->with('creator', 'approver', 'rejecter');

        if ($request->status)     $query->where('status', $request->status);
        if ($request->user_id)    $query->where('created_by', $request->user_id);
        if ($request->date_from)  $query->whereDate('entry_date', '>=', $request->date_from);
        if ($request->date_to)    $query->whereDate('entry_date', '<=', $request->date_to);
        if ($request->min_amount) $query->where('total_debit', '>=', $request->min_amount);
        if ($request->max_amount) $query->where('total_debit', '<=', $request->max_amount);
        if ($request->search)     $query->where(fn($q) =>
            $q->where('entry_number', 'like', '%'.$request->search.'%')
              ->orWhere('description', 'like', '%'.$request->search.'%')
        );

        $entries = $query->latest()->paginate(20);
        $users   = User::where('company_id', $cid)->get();

        return view('auditor.journal_entry_view', compact('entries', 'users'));
    }

    // ===================== GENERAL LEDGER VIEW =====================
    public function generalLedger(Request $request)
    {
        $cid      = $this->cid();
        $accounts = ChartOfAccount::where('company_id', $cid)->where('is_active', true)->orderBy('account_code')->get();
        $selectedAccount = $request->account_id;
        $entries  = collect();

        if ($selectedAccount) {
            $entries = JournalEntryLine::whereHas('journalEntry', fn($q) =>
                $q->where('company_id', $cid)->where('status', 'approved')
            )->where('account_id', $selectedAccount)
             ->with('journalEntry', 'account')
             ->latest()
             ->paginate(25);
        }

        return view('auditor.general_ledger_view', compact('accounts', 'entries', 'selectedAccount'));
    }

    // ===================== AR / AP VIEW =====================
    public function arAp(Request $request)
    {
        $cid  = $this->cid();
        $tab  = $request->tab ?? 'ar';

        $invoices = Invoice::where('company_id', $cid)
            ->with('customer', 'creator')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()->paginate(15, ['*'], 'inv_page');

        $bills = Bill::where('company_id', $cid)
            ->with('vendor', 'creator')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()->paginate(15, ['*'], 'bill_page');

        $totalAR = Invoice::where('company_id', $cid)->whereNotIn('status', ['paid'])->sum('balance_due');
        $totalAP = Bill::where('company_id', $cid)->whereNotIn('status', ['paid'])->sum('balance_due');

        return view('auditor.ar_ap_view', compact('invoices', 'bills', 'totalAR', 'totalAP', 'tab'));
    }

    // ===================== PAYMENT HISTORY =====================
    public function paymentHistory(Request $request)
    {
        $cid = $this->cid();

        // Paid invoices
        $paidInvoices = Invoice::where('company_id', $cid)
            ->where('status', 'paid')
            ->with('customer', 'creator')
            ->when($request->date_from, fn($q) => $q->whereDate('updated_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('updated_at', '<=', $request->date_to))
            ->latest()->paginate(15, ['*'], 'inv_page');

        // Paid bills
        $paidBills = Bill::where('company_id', $cid)
            ->where('status', 'paid')
            ->with('vendor', 'creator')
            ->when($request->date_from, fn($q) => $q->whereDate('updated_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('updated_at', '<=', $request->date_to))
            ->latest()->paginate(15, ['*'], 'bill_page');

        $totalPaidAR = Invoice::where('company_id', $cid)->where('status', 'paid')->sum('total_amount');
        $totalPaidAP = Bill::where('company_id', $cid)->where('status', 'paid')->sum('total_amount');

        return view('auditor.payment_history_view', compact('paidInvoices', 'paidBills', 'totalPaidAR', 'totalPaidAP'));
    }

    // ===================== AUDIT REPORT =====================
    public function auditReport()
    {
        $cid = $this->cid();
        $users = User::where('company_id', $cid)->get();
        return view('auditor.audit_report_page', compact('users'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:full_audit,journal_audit,ar_audit,ap_audit,user_activity,financial_integrity',
            'date_from'   => 'required|date',
            'date_to'     => 'required|date|after_or_equal:date_from',
        ]);

        $cid      = $this->cid();
        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;
        $type     = $request->report_type;

        $data = [];

        if (in_array($type, ['full_audit', 'journal_audit'])) {
            $data['journals'] = JournalEntry::where('company_id', $cid)
                ->whereBetween('entry_date', [$dateFrom, $dateTo])
                ->with('creator', 'approver', 'rejecter')
                ->latest()->get();
        }

        if (in_array($type, ['full_audit', 'ar_audit'])) {
            $data['invoices'] = Invoice::where('company_id', $cid)
                ->whereBetween('invoice_date', [$dateFrom, $dateTo])
                ->with('customer', 'creator')
                ->latest()->get();
        }

        if (in_array($type, ['full_audit', 'ap_audit'])) {
            $data['bills'] = Bill::where('company_id', $cid)
                ->whereBetween('bill_date', [$dateFrom, $dateTo])
                ->with('vendor', 'creator')
                ->latest()->get();
        }

        if (in_array($type, ['full_audit', 'user_activity'])) {
            $data['auditTrails'] = AuditTrail::where('company_id', $cid)
                ->whereBetween('created_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59'])
                ->with('user')
                ->latest()->get();
        }

        if ($type === 'financial_integrity') {
            $data['unbalanced'] = JournalEntry::where('company_id', $cid)
                ->whereRaw('ABS(total_debit - total_credit) > 0.01')
                ->with('creator')->get();
            $data['rejected'] = JournalEntry::where('company_id', $cid)
                ->where('status', 'rejected')
                ->whereBetween('entry_date', [$dateFrom, $dateTo])
                ->with('creator', 'rejecter')->get();
        }

        AuditTrail::log('generate_report', 'audit_report', null, [], ['type' => $type, 'from' => $dateFrom, 'to' => $dateTo], "Generated {$type} audit report");

        return view('auditor.audit_report_result', compact('data', 'type', 'dateFrom', 'dateTo'));
    }
}
