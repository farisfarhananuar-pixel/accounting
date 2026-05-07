<?php
namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JournalEntry;
use App\Models\AccountReceivable;
use App\Models\AccountPayable;
use App\Models\ChartOfAccount;
use Carbon\Carbon;

class ManagerController extends Controller
{
    private function companyId() { return Auth::user()->company_id; }

    public function dashboard()
    {
        $cid = $this->companyId();
        $months = []; $revData = []; $expData = []; $cashInflow = []; $cashOutflow = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');
            $revData[] = 0; $expData[] = 0; $cashInflow[] = 0; $cashOutflow[] = 0;
        }
        $totalRevenue = 0; $totalExpenses = 0;
        $netProfit = 0; $pendingCount = 0;
        $expenseAccounts = collect([]);
        return view('manager.dashboard', compact('totalRevenue','totalExpenses','netProfit','pendingCount','months','revData','expData','cashInflow','cashOutflow','expenseAccounts'));
    }

    public function approvalQueue()
    {
        $cid = $this->companyId();
        $journals = JournalEntry::where('company_id',$cid)->where('status','pending')->get();
        $invoices = AccountReceivable::where('company_id',$cid)->where('status','pending')->get();
        $bills = AccountPayable::where('company_id',$cid)->where('status','pending')->get();
        $total = $journals->count() + $invoices->count() + $bills->count();
        return view('manager.approval_queue', compact('journals','invoices','bills','total'));
    }

    public function approveReject(Request $request)
    {
        $cid = $this->companyId();
        $type = $request->get('type','journal');
        $pendingJournals = JournalEntry::where('company_id',$cid)->where('status','pending')->get();
        $pendingInvoices = AccountReceivable::where('company_id',$cid)->where('status','pending')->get();
        $pendingBills = AccountPayable::where('company_id',$cid)->where('status','pending')->get();
        return view('manager.approve_reject_transaction', compact('type','pendingJournals','pendingInvoices','pendingBills'));
    }

    public function approve(Request $request, $type, $id)
    {
        $cid = $this->companyId();
        if ($type === 'journal') JournalEntry::where('company_id',$cid)->findOrFail($id)->update(['status'=>'approved','approved_by'=>Auth::id(),'approved_at'=>now()]);
        elseif ($type === 'invoice') AccountReceivable::where('company_id',$cid)->findOrFail($id)->update(['status'=>'approved']);
        elseif ($type === 'bill') AccountPayable::where('company_id',$cid)->findOrFail($id)->update(['status'=>'approved']);
        return back()->with('success', ucfirst($type).' approved.');
    }

    public function reject(Request $request, $type, $id)
    {
        $cid = $this->companyId();
        $reason = $request->input('rejection_reason','No reason given');
        if ($type === 'journal') JournalEntry::where('company_id',$cid)->findOrFail($id)->update(['status'=>'rejected','rejected_by'=>Auth::id(),'rejected_at'=>now(),'rejection_reason'=>$reason]);
        elseif ($type === 'invoice') AccountReceivable::where('company_id',$cid)->findOrFail($id)->update(['status'=>'rejected']);
        elseif ($type === 'bill') AccountPayable::where('company_id',$cid)->findOrFail($id)->update(['status'=>'rejected']);
        return back()->with('success', ucfirst($type).' rejected.');
    }

    public function rejectionReasons()
    {
        $cid = $this->companyId();
        $rejectedJournals = JournalEntry::where('company_id',$cid)->where('status','rejected')->paginate(20);
        return view('manager.rejection_reasons', compact('rejectedJournals'));
    }

    public function unusualTransactions()
    {
        $cid = $this->companyId();
        $avg = JournalEntry::where('company_id',$cid)->where('status','approved')->avg('total_debit') ?? 0;
        $threshold = max($avg * 3, 10000);
        $avgAmount = $avg;
        $unusual = JournalEntry::where('company_id',$cid)->where('total_debit','>',$threshold)->paginate(20);
        $duplicateSuspects = JournalEntry::where('company_id',$cid)
            ->selectRaw('entry_date, created_by, COUNT(*) as count')
            ->groupBy('entry_date','created_by')
            ->having('count','>',1)
            ->get();
        return view('manager.unusual_transaction_detect', compact('unusual','threshold','avgAmount','duplicateSuspects'));
    }

    public function reports()
    {
        $cid = $this->companyId();
        $totalRevenue = 0; $totalExpenses = 0; $netProfit = 0;
        $approvedJournals = JournalEntry::where('company_id',$cid)->where('status','approved')->count();
        $totalInvoices = AccountReceivable::where('company_id',$cid)->sum('total_amount');
        $totalBills = AccountPayable::where('company_id',$cid)->sum('total_amount');
        return view('manager.manager_reports_monitor', compact('totalRevenue','totalExpenses','netProfit','approvedJournals','totalInvoices','totalBills'));
    }

    public function journalMonitor(Request $request)
    {
        $cid = $this->companyId();
        $q = JournalEntry::where('company_id',$cid);
        if ($request->status) $q->where('status',$request->status);
        if ($request->date_from) $q->whereDate('entry_date','>=',$request->date_from);
        if ($request->date_to) $q->whereDate('entry_date','<=',$request->date_to);
        $entries = $q->orderByDesc('entry_date')->paginate(20);
        return view('manager.manger_journal_monitor', compact('entries'));
    }

    public function arMonitor()
    {
        $cid = $this->companyId();
        $invoices = AccountReceivable::where('company_id',$cid)->paginate(20);
        $totalAR = AccountReceivable::where('company_id',$cid)->sum('balance_due');
        return view('manager.manager_account_receivable_monitor', compact('invoices','totalAR'));
    }

    public function apMonitor()
    {
        $cid = $this->companyId();
        $bills = AccountPayable::where('company_id',$cid)->paginate(20);
        $totalAP = AccountPayable::where('company_id',$cid)->sum('balance_due');
        return view('manager.manager_account_payable_monitor', compact('bills','totalAP'));
    }

    public function chartOfAccount()
    {
        $cid = $this->companyId();
        $accounts = ChartOfAccount::where('company_id',$cid)->paginate(20);
        return view('manager.manager_chart_of_account', compact('accounts'));
    }

    public function createRoles() { return view('manager.create_roles'); }
    public function storeRole(Request $request) { return back()->with('success','Role created.'); }
}
