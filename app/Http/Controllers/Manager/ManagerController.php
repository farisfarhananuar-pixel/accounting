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
    public function dashboard()
    {
        $companyId = Auth::user()->company_id;
        $months = [];
        $revData = [];
        $expData = [];
        $cashInflow = [];
        $cashOutflow = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');
            $revData[] = 0;
            $expData[] = 0;
            $cashInflow[] = 0;
            $cashOutflow[] = 0;
        }

        $totalRevenue = 0;
        $totalExpenses = 0;
        $netProfit = $totalRevenue - $totalExpenses;
        $pendingCount = 0;

        $expenseAccounts = collect([]);

        return view('manager.dashboard', compact(
            'totalRevenue', 'totalExpenses', 'netProfit',
            'pendingCount', 'months', 'revData', 'expData',
            'cashInflow', 'cashOutflow', 'expenseAccounts'
        ));
    }

    public function approveReject() { return view('manager.approve_reject_transaction'); }
    public function approve(Request $request, $type, $id) { return back()->with('success', 'Approved.'); }
    public function reject(Request $request, $type, $id) { return back()->with('success', 'Rejected.'); }
    public function approvalQueue() { return view('manager.approval_queue'); }
    public function unusualTransactions() { return view('manager.unusual_transaction_detect'); }
    public function reports() { return view('manager.manager_reports_monitor'); }
    public function journalMonitor() { return view('manager.manger_journal_monitor'); }
    public function arMonitor() { return view('manager.manager_account_receivable_monitor'); }
    public function apMonitor() { return view('manager.manager_account_payable_monitor'); }
    public function chartOfAccount() { return view('manager.manager_chart_of_account'); }
    public function createRoles() { return view('manager.create_roles'); }
    public function storeRole(Request $request) { return back()->with('success', 'Role created.'); }
    public function rejectionReasons() { return view('manager.rejection_reasons'); }
}
