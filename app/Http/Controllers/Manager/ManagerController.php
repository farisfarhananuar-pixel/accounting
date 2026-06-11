<?php
namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Invoice;
use App\Models\Bill;
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
        $invoices = Invoice::where('company_id',$cid)->where('status','pending')->get();
        $bills = Bill::where('company_id',$cid)->where('status','pending')->get();
        $total = $journals->count() + $invoices->count() + $bills->count();
        return view('manager.approval_queue', compact('journals','invoices','bills','total'));
    }

    public function approveReject(Request $request)
    {
        $cid = $this->companyId();
        $type = $request->get('type','journal');
        $pendingJournals = JournalEntry::where('company_id',$cid)->where('status','pending')->get();
        $pendingInvoices = Invoice::where('company_id',$cid)->where('status','pending')->get();
        $pendingBills = Bill::where('company_id',$cid)->where('status','pending')->get();
        return view('manager.approve_reject_transaction', compact('type','pendingJournals','pendingInvoices','pendingBills'));
    }

    public function approve(Request $request, $type, $id)
    {
        $cid = $this->companyId();

        if ($type === 'journal') {
            $journal = JournalEntry::where('company_id',$cid)->with('lines')->findOrFail($id);
            $journal->update(['status'=>'approved','approved_by'=>Auth::id(),'approved_at'=>now()]);

            // Update current_balance on each affected chart of account
            foreach ($journal->lines as $line) {
                $account = ChartOfAccount::find($line->account_id);
                if ($account) {
                    if (in_array($account->account_type, ['asset', 'expense'])) {
                        $account->increment('current_balance', $line->debit - $line->credit);
                    } else {
                        $account->increment('current_balance', $line->credit - $line->debit);
                    }
                }
            }
        }

        elseif ($type === 'invoice') {
            $invoice = Invoice::where('company_id',$cid)->with('lines')->findOrFail($id);

            // Find AR and Revenue accounts for this company
            $arAccount = ChartOfAccount::where('company_id',$cid)
                ->where('account_type','asset')
                ->where(function($q){ $q->where('account_name','like','%Receivable%')->orWhere('account_name','like','%Accounts Receivable%'); })
                ->first();
            $revenueAccount = ChartOfAccount::where('company_id',$cid)
                ->where('account_type','revenue')
                ->first();

            // Auto-create journal entry if both accounts exist
            if ($arAccount && $revenueAccount) {
                $year = now()->year;
                $prefix = $cid.'-JE-'.$year.'-';
                $last = JournalEntry::where('company_id',$cid)
                    ->whereYear('created_at',$year)
                    ->where('entry_number','like','JE-'.$year.'-%')
                    ->orderByRaw('CAST(SUBSTRING_INDEX(entry_number, "-", -1) AS UNSIGNED) DESC')
                    ->value('entry_number');
                $seq = $last ? ((int) substr($last, strrpos($last,'-')+1)) + 1 : 1;
                $entryNum = 'JE-'.$year.'-'.str_pad($seq,4,'0',STR_PAD_LEFT);

                $je = JournalEntry::create([
                    'company_id'   => $cid,
                    'entry_number' => $entryNum,
                    'entry_date'   => $invoice->invoice_date,
                    'description'  => 'Auto: Invoice '.$invoice->invoice_number.' approved',
                    'reference'    => $invoice->invoice_number,
                    'status'       => 'approved',
                    'created_by'   => Auth::id(),
                    'approved_by'  => Auth::id(),
                    'approved_at'  => now(),
                    'total_debit'  => $invoice->total_amount,
                    'total_credit' => $invoice->total_amount,
                ]);

                // DR Accounts Receivable
                JournalEntryLine::create([
                    'journal_entry_id' => $je->id,
                    'account_id'       => $arAccount->id,
                    'description'      => 'Accounts Receivable - '.$invoice->invoice_number,
                    'debit'            => $invoice->total_amount,
                    'credit'           => 0,
                ]);
                // CR Revenue
                JournalEntryLine::create([
                    'journal_entry_id' => $je->id,
                    'account_id'       => $revenueAccount->id,
                    'description'      => 'Revenue - '.$invoice->invoice_number,
                    'debit'            => 0,
                    'credit'           => $invoice->total_amount,
                ]);

                // Update account balances
                $arAccount->increment('current_balance', $invoice->total_amount);      // Asset DR+
                $revenueAccount->increment('current_balance', $invoice->total_amount); // Revenue CR+

                $invoice->update(['status'=>'approved','approved_by'=>Auth::id(),'journal_entry_id'=>$je->id]);
            } else {
                $invoice->update(['status'=>'approved','approved_by'=>Auth::id()]);
            }
        }

        elseif ($type === 'bill') {
            $bill = Bill::where('company_id',$cid)->with('lines')->findOrFail($id);

            // Find AP and Expense accounts for this company
            $apAccount = ChartOfAccount::where('company_id',$cid)
                ->where('account_type','liability')
                ->where(function($q){ $q->where('account_name','like','%Payable%')->orWhere('account_name','like','%Accounts Payable%'); })
                ->first();
            $expenseAccount = ChartOfAccount::where('company_id',$cid)
                ->where('account_type','expense')
                ->first();

            if ($apAccount && $expenseAccount) {
                $year = now()->year;
                $last = JournalEntry::where('company_id',$cid)
                    ->whereYear('created_at',$year)
                    ->where('entry_number','like','JE-'.$year.'-%')
                    ->orderByRaw('CAST(SUBSTRING_INDEX(entry_number, "-", -1) AS UNSIGNED) DESC')
                    ->value('entry_number');
                $seq = $last ? ((int) substr($last, strrpos($last,'-')+1)) + 1 : 1;
                $entryNum = 'JE-'.$year.'-'.str_pad($seq,4,'0',STR_PAD_LEFT);

                $je = JournalEntry::create([
                    'company_id'   => $cid,
                    'entry_number' => $entryNum,
                    'entry_date'   => $bill->bill_date,
                    'description'  => 'Auto: Bill '.$bill->bill_number.' approved',
                    'reference'    => $bill->bill_number,
                    'status'       => 'approved',
                    'created_by'   => Auth::id(),
                    'approved_by'  => Auth::id(),
                    'approved_at'  => now(),
                    'total_debit'  => $bill->total_amount,
                    'total_credit' => $bill->total_amount,
                ]);

                // DR Expense
                JournalEntryLine::create([
                    'journal_entry_id' => $je->id,
                    'account_id'       => $expenseAccount->id,
                    'description'      => 'Expense - '.$bill->bill_number,
                    'debit'            => $bill->total_amount,
                    'credit'           => 0,
                ]);
                // CR Accounts Payable
                JournalEntryLine::create([
                    'journal_entry_id' => $je->id,
                    'account_id'       => $apAccount->id,
                    'description'      => 'Accounts Payable - '.$bill->bill_number,
                    'debit'            => 0,
                    'credit'           => $bill->total_amount,
                ]);

                // Update account balances
                $expenseAccount->increment('current_balance', $bill->total_amount);    // Expense DR+
                $apAccount->increment('current_balance', $bill->total_amount);         // Liability CR+

                $bill->update(['status'=>'approved','approved_by'=>Auth::id(),'journal_entry_id'=>$je->id]);
            } else {
                $bill->update(['status'=>'approved','approved_by'=>Auth::id()]);
            }
        }

        return back()->with('success', ucfirst($type).' approved.');
    }

    public function reject(Request $request, $type, $id)
    {
        $cid = $this->companyId();
        $reason = $request->input('rejection_reason','No reason given');
        if ($type === 'journal') JournalEntry::where('company_id',$cid)->findOrFail($id)->update(['status'=>'rejected','rejected_by'=>Auth::id(),'rejected_at'=>now(),'rejection_reason'=>$reason]);
        elseif ($type === 'invoice') Invoice::where('company_id',$cid)->findOrFail($id)->update(['status'=>'rejected']);
        elseif ($type === 'bill') Bill::where('company_id',$cid)->findOrFail($id)->update(['status'=>'rejected']);
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
        $totalInvoices = Invoice::where('company_id',$cid)->sum('total_amount');
        $totalBills = Bill::where('company_id',$cid)->sum('total_amount');
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
        $invoices = Invoice::where('company_id',$cid)->paginate(20);
        $totalAR = Invoice::where('company_id',$cid)->sum('balance_due');
        return view('manager.manager_account_receivable_monitor', compact('invoices','totalAR'));
    }

    public function apMonitor()
    {
        $cid = $this->companyId();
        $bills = Bill::where('company_id',$cid)->paginate(20);
        $totalAP = Bill::where('company_id',$cid)->sum('balance_due');
        return view('manager.manager_account_payable_monitor', compact('bills','totalAP'));
    }

    public function chartOfAccount()
    {
        $cid = $this->companyId();
        $accounts = ChartOfAccount::where('company_id',$cid)->paginate(20);
        return view('manager.manager_chart_of_account', compact('accounts'));
    }

    public function createRoles()
    {
        $users = \App\Models\User::where('company_id', $this->companyId())->get();
        return view('manager.create_roles', compact('users'));
    }
    public function storeRole(Request $request) { return back()->with('success','Role created.'); }
}
