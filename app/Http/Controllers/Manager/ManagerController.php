<?php
namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    public function dashboard() { return view('manager.dashboard'); }
    public function approveReject() { return view('manager.approve_reject'); }
    public function approve(Request $request, $type, $id) { return back()->with('success', 'Approved.'); }
    public function reject(Request $request, $type, $id) { return back()->with('success', 'Rejected.'); }
    public function approvalQueue() { return view('manager.approval_queue'); }
    public function unusualTransactions() { return view('manager.unusual_transactions'); }
    public function reports() { return view('manager.reports'); }
    public function journalMonitor() { return view('manager.journal_monitor'); }
    public function arMonitor() { return view('manager.ar_monitor'); }
    public function apMonitor() { return view('manager.ap_monitor'); }
    public function chartOfAccount() { return view('manager.chart_of_account'); }
    public function createRoles() { return view('manager.create_roles'); }
    public function storeRole(Request $request) { return back()->with('success', 'Role created.'); }
    public function rejectionReasons() { return view('manager.rejection_reasons'); }
}
