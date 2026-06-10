<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\ChartOfAccount;
use App\Models\Invoice;
use App\Models\Bill;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountantController extends Controller
{
    private function companyId()
    {
        return auth()->user()->company_id;
    }

    // ===================== DASHBOARD =====================
    public function dashboard()
    {
        $cid = $this->companyId();

        $totalDraft    = JournalEntry::where('company_id', $cid)->where('status', 'draft')->count();
        $totalPending  = JournalEntry::where('company_id', $cid)->where('status', 'pending')->count();
        $totalRejected = JournalEntry::where('company_id', $cid)->where('status', 'rejected')->count();

        // Pie chart data
        $approved = JournalEntry::where('company_id', $cid)->where('status', 'approved')->count();
        $pieData  = ['Draft' => $totalDraft, 'Pending' => $totalPending, 'Approved' => $approved, 'Rejected' => $totalRejected];

        // AR vs AP bar chart (last 6 months)
        $arData = []; $apData = []; $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $months[] = $m->format('M Y');
            $arData[] = Invoice::where('company_id', $cid)
                ->whereYear('invoice_date', $m->year)->whereMonth('invoice_date', $m->month)
                ->sum('total_amount');
            $apData[] = Bill::where('company_id', $cid)
                ->whereYear('bill_date', $m->year)->whereMonth('bill_date', $m->month)
                ->sum('total_amount');
        }

        // Recent transactions (latest 10)
        $recentJournals = JournalEntry::where('company_id', $cid)
            ->with('creator')->latest()->take(10)->get();

        return view('accountant.dashboard', compact(
            'totalDraft', 'totalPending', 'totalRejected',
            'pieData', 'arData', 'apData', 'months', 'recentJournals'
        ));
    }

    // ===================== JOURNAL ENTRIES =====================
    public function journalEntries(Request $request)
    {
        $cid   = $this->companyId();
        $query = JournalEntry::where('company_id', $cid)->with('creator');

        if ($request->status)     $query->where('status', $request->status);
        if ($request->date_from)  $query->whereDate('entry_date', '>=', $request->date_from);
        if ($request->date_to)    $query->whereDate('entry_date', '<=', $request->date_to);
        if ($request->search)     $query->where(function($q) use ($request) {
            $q->where('entry_number', 'like', '%'.$request->search.'%')
              ->orWhere('description', 'like', '%'.$request->search.'%');
        });

        $entries = $query->latest()->paginate(15);
        return view('accountant.journal_entries', compact('entries'));
    }

    public function createJournal()
    {
        $accounts = ChartOfAccount::where('company_id', $this->companyId())
            ->where('is_active', true)->orderBy('account_code')->get();
        $nextNumber = $this->generateNumber('JE');
        return view('accountant.journal_entry_form', compact('accounts', 'nextNumber'));
    }

    public function storeJournal(Request $request)
    {
        $request->validate([
            'entry_date'              => 'required|date',
            'description'             => 'required|string|max:255',
            'lines'                   => 'required|array|min:2',
            'lines.*.account_id'      => 'required|exists:chart_of_accounts,id',
            'lines.*.debit'           => 'nullable|numeric|min:0',
            'lines.*.credit'          => 'nullable|numeric|min:0',
        ]);

        $totalDebit  = collect($request->lines)->sum('debit');
        $totalCredit = collect($request->lines)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors(['lines' => 'Total debit must equal total credit (journal must balance).'])->withInput();
        }

        DB::transaction(function () use ($request, $totalDebit, $totalCredit) {
            $entry = JournalEntry::create([
                'company_id'   => $this->companyId(),
                'entry_number' => $this->generateNumber('JE'),
                'entry_date'   => $request->entry_date,
                'description'  => $request->description,
                'reference'    => $request->reference,
                'status'       => 'draft',
                'created_by'   => auth()->id(),
                'total_debit'  => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            foreach ($request->lines as $line) {
                if (($line['debit'] ?? 0) > 0 || ($line['credit'] ?? 0) > 0) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id'       => $line['account_id'],
                        'description'      => $line['description'] ?? null,
                        'debit'            => $line['debit'] ?? 0,
                        'credit'           => $line['credit'] ?? 0,
                    ]);
                }
            }

            AuditTrail::log('create', 'journal_entry', $entry->id, [], $entry->toArray(), "Created journal entry {$entry->entry_number}");
        });

        return redirect()->route('accountant.journal_entries')->with('success', 'Journal entry created successfully!');
    }

    public function submitJournal($id)
    {
        $entry = JournalEntry::where('company_id', $this->companyId())->findOrFail($id);

        if (!in_array($entry->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Only draft or rejected entries can be submitted.');
        }

        $old = $entry->toArray();
        $entry->update(['status' => 'pending']);
        AuditTrail::log('submit', 'journal_entry', $entry->id, $old, $entry->fresh()->toArray(), "Submitted journal entry {$entry->entry_number} for approval");

        return back()->with('success', 'Journal entry submitted for approval!');
    }

    public function deleteJournal($id)
    {
        $entry = JournalEntry::where('company_id', $this->companyId())
            ->where('created_by', auth()->id())
            ->where('status', 'draft')
            ->findOrFail($id);

        AuditTrail::log('delete', 'journal_entry', $entry->id, $entry->toArray(), [], "Deleted journal entry {$entry->entry_number}");
        $entry->delete();

        return back()->with('success', 'Journal entry deleted.');
    }

    // ===================== ACCOUNT RECEIVABLE =====================
    public function accountReceivable(Request $request)
    {
        $cid   = $this->companyId();
        $query = Invoice::where('company_id', $cid)->with('customer', 'creator');

        if ($request->status)    $query->where('status', $request->status);
        if ($request->search)    $query->whereHas('customer', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'))
                                        ->orWhere('invoice_number', 'like', '%'.$request->search.'%');

        $invoices      = $query->latest()->paginate(15);
        $totalAR       = Invoice::where('company_id', $cid)->whereNotIn('status', ['paid'])->sum('balance_due');
        $overdueCount  = Invoice::where('company_id', $cid)->where('due_date', '<', now())->whereNotIn('status', ['paid', 'draft'])->count();
        $paidThisMonth = Invoice::where('company_id', $cid)->where('status', 'paid')
                            ->whereMonth('updated_at', now()->month)->sum('total_amount');

        return view('accountant.account_receivable', compact('invoices', 'totalAR', 'overdueCount', 'paidThisMonth'));
    }

    public function createInvoice()
    {
        $customers  = Customer::where('company_id', $this->companyId())->where('is_active', true)->get();
        $nextNumber = $this->generateNumber('INV');
        return view('accountant.invoice_form', compact('customers', 'nextNumber'));
    }

    public function storeInvoice(Request $request)
    {
        $request->validate([
            'customer_id'        => 'required|exists:customers,id',
            'invoice_date'       => 'required|date',
            'due_date'           => 'required|date|after_or_equal:invoice_date',
            'lines'              => 'required|array|min:1',
            'lines.*.description'=> 'required|string',
            'lines.*.quantity'   => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $subtotal = 0; $taxAmount = 0;
            foreach ($request->lines as $line) {
                $lineAmt  = $line['quantity'] * $line['unit_price'];
                $lineTax  = $lineAmt * (($line['tax_rate'] ?? 0) / 100);
                $subtotal += $lineAmt;
                $taxAmount+= $lineTax;
            }
            $total = $subtotal + $taxAmount;

            $invoice = Invoice::create([
                'company_id'     => $this->companyId(),
                'customer_id'    => $request->customer_id,
                'invoice_number' => $this->generateNumber('INV'),
                'invoice_date'   => $request->invoice_date,
                'due_date'       => $request->due_date,
                'subtotal'       => $subtotal,
                'tax_amount'     => $taxAmount,
                'total_amount'   => $total,
                'balance_due'    => $total,
                'status'         => 'pending',
                'notes'          => $request->notes,
                'created_by'     => auth()->id(),
            ]);

            foreach ($request->lines as $line) {
                $lineAmt = $line['quantity'] * $line['unit_price'];
                $invoice->lines()->create([
                    'description' => $line['description'],
                    'quantity'    => $line['quantity'],
                    'unit_price'  => $line['unit_price'],
                    'tax_rate'    => $line['tax_rate'] ?? 0,
                    'amount'      => $lineAmt,
                ]);
            }

            AuditTrail::log('create', 'invoice', $invoice->id, [], $invoice->toArray(), "Created invoice {$invoice->invoice_number}");
        });

        return redirect()->route('accountant.account_receivable')->with('success', 'Invoice created successfully!');
    }

    public function showInvoice($id)
    {
        $invoice = Invoice::where('company_id', $this->companyId())
            ->with(['customer', 'lines', 'creator'])
            ->findOrFail($id);
        return view('accountant.invoice_show', compact('invoice'));
    }

    // ===================== ACCOUNT PAYABLE =====================
    public function accountPayable(Request $request)
    {
        $cid   = $this->companyId();
        $query = Bill::where('company_id', $cid)->with('vendor', 'creator');

        if ($request->status) $query->where('status', $request->status);

        $bills        = $query->latest()->paginate(15);
        $totalAP      = Bill::where('company_id', $cid)->whereNotIn('status', ['paid'])->sum('balance_due');
        $overdueCount = Bill::where('company_id', $cid)->where('due_date', '<', now())->whereNotIn('status', ['paid', 'draft'])->count();

        return view('accountant.account_payable', compact('bills', 'totalAP', 'overdueCount'));
    }

    public function createBill()
    {
        $vendors    = Vendor::where('company_id', $this->companyId())->where('is_active', true)->get();
        $nextNumber = $this->generateNumber('BILL');
        return view('accountant.bill_form', compact('vendors', 'nextNumber'));
    }

    public function storeBill(Request $request)
    {
        $request->validate([
            'vendor_id'          => 'required|exists:vendors,id',
            'bill_date'          => 'required|date',
            'due_date'           => 'required|date|after_or_equal:bill_date',
            'lines'              => 'required|array|min:1',
            'lines.*.description'=> 'required|string',
            'lines.*.quantity'   => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $subtotal = 0; $taxAmount = 0;
            foreach ($request->lines as $line) {
                $lineAmt   = $line['quantity'] * $line['unit_price'];
                $lineTax   = $lineAmt * (($line['tax_rate'] ?? 0) / 100);
                $subtotal += $lineAmt;
                $taxAmount+= $lineTax;
            }
            $total = $subtotal + $taxAmount;

            $bill = Bill::create([
                'company_id'           => $this->companyId(),
                'vendor_id'            => $request->vendor_id,
                'bill_number'          => $this->generateNumber('BILL'),
                'vendor_invoice_number'=> $request->vendor_invoice_number,
                'bill_date'            => $request->bill_date,
                'due_date'             => $request->due_date,
                'subtotal'             => $subtotal,
                'tax_amount'           => $taxAmount,
                'total_amount'         => $total,
                'balance_due'          => $total,
                'status'               => 'pending',
                'notes'                => $request->notes,
                'created_by'           => auth()->id(),
            ]);

            foreach ($request->lines as $line) {
                $lineAmt = $line['quantity'] * $line['unit_price'];
                $bill->lines()->create([
                    'description' => $line['description'],
                    'quantity'    => $line['quantity'],
                    'unit_price'  => $line['unit_price'],
                    'tax_rate'    => $line['tax_rate'] ?? 0,
                    'amount'      => $lineAmt,
                ]);
            }

            AuditTrail::log('create', 'bill', $bill->id, [], $bill->toArray(), "Created bill {$bill->bill_number}");
        });

        return redirect()->route('accountant.account_payable')->with('success', 'Bill recorded successfully!');
    }

    public function showBill($id)
    {
        $bill = Bill::where('company_id', $this->companyId())
            ->with(['vendor', 'lines', 'creator'])
            ->findOrFail($id);
        return view('accountant.bill_show', compact('bill'));
    }

    public function quickAddCustomer(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
        ]);

        $cid   = $this->companyId();
        $count = Customer::where('company_id', $cid)->count() + 1;
        $code  = 'CUST-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $customer = Customer::create([
            'company_id'    => $cid,
            'customer_code' => $code,
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'credit_limit'  => 0,
            'is_active'     => true,
        ]);

        AuditTrail::log('create', 'customer', $customer->id, [], $customer->toArray(), "Quick-added customer {$customer->name}");

        return response()->json(['id' => $customer->id, 'name' => $customer->name, 'code' => $customer->customer_code]);
    }

    public function quickAddVendor(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
        ]);

        $cid   = $this->companyId();
        $count = Vendor::where('company_id', $cid)->count() + 1;
        $code  = 'VND-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $vendor = Vendor::create([
            'company_id'  => $cid,
            'vendor_code' => $code,
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'is_active'   => true,
        ]);

        AuditTrail::log('create', 'vendor', $vendor->id, [], $vendor->toArray(), "Quick-added vendor {$vendor->name}");

        return response()->json(['id' => $vendor->id, 'name' => $vendor->name, 'code' => $vendor->vendor_code]);
    }

    // ===================== CUSTOMERS =====================
    public function customers(Request $request)
    {
        $cid   = $this->companyId();
        $query = Customer::where('company_id', $cid);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('customer_code', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->status === 'active')   $query->where('is_active', true);
        if ($request->status === 'inactive') $query->where('is_active', false);

        $customers = $query->latest()->paginate(15);
        return view('accountant.customers', compact('customers'));
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:30',
            'address'      => 'nullable|string|max:500',
            'tax_number'   => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $cid    = $this->companyId();
        $count  = Customer::where('company_id', $cid)->count() + 1;
        $code   = 'CUST-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $customer = Customer::create([
            'company_id'   => $cid,
            'customer_code'=> $code,
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'address'      => $request->address,
            'tax_number'   => $request->tax_number,
            'credit_limit' => $request->credit_limit ?? 0,
            'is_active'    => true,
        ]);

        AuditTrail::log('create', 'customer', $customer->id, [], $customer->toArray(), "Added customer {$customer->name}");
        return back()->with('success', "Customer '{$customer->name}' added successfully!");
    }

    public function updateCustomer(Request $request, $id)
    {
        $customer = Customer::where('company_id', $this->companyId())->findOrFail($id);

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'nullable|email|max:255',
            'phone'        => 'nullable|string|max:30',
            'address'      => 'nullable|string|max:500',
            'tax_number'   => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $old = $customer->toArray();
        $customer->update([
            'name'         => $request->name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'address'      => $request->address,
            'tax_number'   => $request->tax_number,
            'credit_limit' => $request->credit_limit ?? 0,
            'is_active'    => $request->boolean('is_active', true),
        ]);

        AuditTrail::log('update', 'customer', $customer->id, $old, $customer->fresh()->toArray(), "Updated customer {$customer->name}");
        return back()->with('success', "Customer '{$customer->name}' updated successfully!");
    }

    public function deleteCustomer($id)
    {
        $customer = Customer::where('company_id', $this->companyId())->findOrFail($id);

        if ($customer->invoices()->exists()) {
            return back()->with('error', "Cannot delete '{$customer->name}' — customer has existing invoices.");
        }

        AuditTrail::log('delete', 'customer', $customer->id, $customer->toArray(), [], "Deleted customer {$customer->name}");
        $customer->delete();
        return back()->with('success', "Customer deleted successfully.");
    }

    // ===================== VENDORS =====================
    public function vendors(Request $request)
    {
        $cid   = $this->companyId();
        $query = Vendor::where('company_id', $cid);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('vendor_code', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->status === 'active')   $query->where('is_active', true);
        if ($request->status === 'inactive') $query->where('is_active', false);

        $vendors = $query->latest()->paginate(15);
        return view('accountant.vendors', compact('vendors'));
    }

    public function storeVendor(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:30',
            'address'    => 'nullable|string|max:500',
            'tax_number' => 'nullable|string|max:50',
        ]);

        $cid   = $this->companyId();
        $count = Vendor::where('company_id', $cid)->count() + 1;
        $code  = 'VND-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $vendor = Vendor::create([
            'company_id'  => $cid,
            'vendor_code' => $code,
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'tax_number'  => $request->tax_number,
            'is_active'   => true,
        ]);

        AuditTrail::log('create', 'vendor', $vendor->id, [], $vendor->toArray(), "Added vendor {$vendor->name}");
        return back()->with('success', "Vendor '{$vendor->name}' added successfully!");
    }

    public function updateVendor(Request $request, $id)
    {
        $vendor = Vendor::where('company_id', $this->companyId())->findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'nullable|email|max:255',
            'phone'      => 'nullable|string|max:30',
            'address'    => 'nullable|string|max:500',
            'tax_number' => 'nullable|string|max:50',
        ]);

        $old = $vendor->toArray();
        $vendor->update([
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'tax_number' => $request->tax_number,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        AuditTrail::log('update', 'vendor', $vendor->id, $old, $vendor->fresh()->toArray(), "Updated vendor {$vendor->name}");
        return back()->with('success', "Vendor '{$vendor->name}' updated successfully!");
    }

    public function deleteVendor($id)
    {
        $vendor = Vendor::where('company_id', $this->companyId())->findOrFail($id);

        if ($vendor->bills()->exists()) {
            return back()->with('error', "Cannot delete '{$vendor->name}' — vendor has existing bills.");
        }

        AuditTrail::log('delete', 'vendor', $vendor->id, $vendor->toArray(), [], "Deleted vendor {$vendor->name}");
        $vendor->delete();
        return back()->with('success', "Vendor deleted successfully.");
    }

    // ===================== CHART OF ACCOUNTS =====================
    public function chartOfAccount()
    {
        $accounts = ChartOfAccount::where('company_id', $this->companyId())
            ->orderBy('account_code')->paginate(20);
        return view('accountant.chart_of_account', compact('accounts'));
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'account_code' => 'required|string|max:20',
            'account_name' => 'required|string|max:100',
            'account_type' => 'required|in:asset,liability,equity,revenue,expense',
            'account_category' => 'required|string',
        ]);

        $exists = ChartOfAccount::where('company_id', $this->companyId())
            ->where('account_code', $request->account_code)->exists();
        if ($exists) return back()->withErrors(['account_code' => 'Account code already exists.'])->withInput();

        $account = ChartOfAccount::create([
            'company_id'       => $this->companyId(),
            'account_code'     => $request->account_code,
            'account_name'     => $request->account_name,
            'account_type'     => $request->account_type,
            'account_category' => $request->account_category,
            'description'      => $request->description,
            'is_active'        => true,
        ]);

        AuditTrail::log('create', 'chart_of_account', $account->id, [], $account->toArray(), "Added account {$account->account_code}");
        return back()->with('success', 'Account added successfully!');
    }

    // ===================== REPORTS =====================
    public function generalLedger(Request $request)
    {
        $cid      = $this->companyId();
        $accounts = ChartOfAccount::where('company_id', $cid)->where('is_active', true)->orderBy('account_code')->get();
        $selectedAccount = $request->account_id;
        $entries  = collect();

        if ($selectedAccount) {
            $entries = JournalEntryLine::whereHas('journalEntry', fn($q) =>
                $q->where('company_id', $cid)->where('status', 'approved')
            )->where('account_id', $selectedAccount)
             ->with('journalEntry', 'account')
             ->latest()->paginate(20);
        }

        return view('accountant.general_ledger', compact('accounts', 'entries', 'selectedAccount'));
    }

    public function trialBalance()
    {
        $cid      = $this->companyId();
        $accounts = ChartOfAccount::where('company_id', $cid)
            ->where('is_active', true)
            ->orderBy('account_type')->orderBy('account_code')
            ->get()
            ->map(function ($acc) use ($cid) {
                $debits  = JournalEntryLine::where('account_id', $acc->id)
                    ->whereHas('journalEntry', fn($q) => $q->where('company_id', $cid)->where('status', 'approved'))
                    ->sum('debit');
                $credits = JournalEntryLine::where('account_id', $acc->id)
                    ->whereHas('journalEntry', fn($q) => $q->where('company_id', $cid)->where('status', 'approved'))
                    ->sum('credit');
                $acc->total_debit  = $acc->opening_balance > 0 ? $acc->opening_balance + $debits : $debits;
                $acc->total_credit = $acc->opening_balance < 0 ? abs($acc->opening_balance) + $credits : $credits;
                return $acc;
            });

        $totalDebit  = $accounts->sum('total_debit');
        $totalCredit = $accounts->sum('total_credit');

        return view('accountant.trial_balance', compact('accounts', 'totalDebit', 'totalCredit'));
    }

    public function profitLoss(Request $request)
    {
        $cid   = $this->companyId();
        $year  = $request->year ?? now()->year;
        $month = $request->month;

        $revenue  = $this->getAccountBalance($cid, 'revenue', $year, $month);
        $expenses = $this->getAccountBalance($cid, 'expense', $year, $month);
        $netProfit= $revenue['total'] - $expenses['total'];

        return view('accountant.profit_or_loss', compact('revenue', 'expenses', 'netProfit', 'year', 'month'));
    }

    public function financialPosition()
    {
        $cid     = $this->companyId();
        $assets  = $this->getAccountBalance($cid, 'asset');
        $liabilities = $this->getAccountBalance($cid, 'liability');
        $equity  = $this->getAccountBalance($cid, 'equity');

        return view('accountant.financial_position', compact('assets', 'liabilities', 'equity'));
    }

    public function financialStatements()
    {
        $cid   = $this->companyId();
        $year  = now()->year;
        $revenue   = $this->getAccountBalance($cid, 'revenue', $year);
        $expenses  = $this->getAccountBalance($cid, 'expense', $year);
        $netProfit = $revenue['total'] - $expenses['total'];
        $assets    = $this->getAccountBalance($cid, 'asset');
        $liabilities = $this->getAccountBalance($cid, 'liability');
        $equity    = $this->getAccountBalance($cid, 'equity');

        return view('accountant.view_financial_statement', compact(
            'revenue', 'expenses', 'netProfit', 'assets', 'liabilities', 'equity', 'year'
        ));
    }

    public function taxCalculations()
    {
        $cid     = $this->companyId();
        $year    = now()->year;
        $revenue = $this->getAccountBalance($cid, 'revenue', $year);
        $expenses= $this->getAccountBalance($cid, 'expense', $year);
        $netProfit = $revenue['total'] - $expenses['total'];

        // SST (6%)
        $sstBase   = Invoice::where('company_id', $cid)->whereYear('invoice_date', $year)->where('status', 'approved')->sum('subtotal');
        $sstAmount = $sstBase * 0.06;

        // Corporate tax (24% on chargeable income > 600k, 17% below)
        $taxRate   = $netProfit > 600000 ? 0.24 : 0.17;
        $corpTax   = max(0, $netProfit * $taxRate);

        return view('accountant.tax_calculations', compact('netProfit', 'sstBase', 'sstAmount', 'corpTax', 'taxRate', 'year'));
    }

    public function bankReconciliation()
    {
        $cid      = $this->companyId();
        $bankAccs = ChartOfAccount::where('company_id', $cid)
            ->where('account_category', 'current_asset')
            ->where('account_name', 'like', '%Bank%')
            ->get()
            ->map(function ($acc) use ($cid) {
                $debits  = JournalEntryLine::where('account_id', $acc->id)
                    ->whereHas('journalEntry', fn($q) => $q->where('company_id', $cid)->where('status', 'approved'))
                    ->sum('debit');
                $credits = JournalEntryLine::where('account_id', $acc->id)
                    ->whereHas('journalEntry', fn($q) => $q->where('company_id', $cid)->where('status', 'approved'))
                    ->sum('credit');
                // For asset accounts: balance = opening + debits - credits
                $acc->computed_balance = $acc->opening_balance + $debits - $credits;

                // Latest 10 approved transactions for this bank account
                $acc->recent_transactions = JournalEntryLine::where('account_id', $acc->id)
                    ->whereHas('journalEntry', fn($q) => $q->where('company_id', $cid)->where('status', 'approved'))
                    ->with('journalEntry')
                    ->latest()
                    ->take(10)
                    ->get();
                return $acc;
            });

        return view('accountant.bank_reconcilation', compact('bankAccs'));
    }

    public function fixedAsset()
    {
        $cid    = $this->companyId();
        $assets = ChartOfAccount::where('company_id', $cid)
            ->where('account_category', 'fixed_asset')
            ->where('is_active', true)
            ->get();
        return view('accountant.fixed_asset_management', compact('assets'));
    }

    // ===================== HELPERS =====================
    private function generateNumber(string $prefix): string
    {
        $cid  = $this->companyId();
        $year = now()->year;
        $map  = [
            'JE'   => [JournalEntry::class, 'entry_number'],
            'INV'  => [Invoice::class, 'invoice_number'],
            'BILL' => [Bill::class, 'bill_number'],
        ];
        [$model, $column] = $map[$prefix] ?? [JournalEntry::class, 'entry_number'];

        // Use MAX on the numeric suffix to avoid duplicates when rows are deleted
        $pattern = "{$prefix}-{$year}-%";
        $last = $model::where('company_id', $cid)
            ->where($column, 'like', $pattern)
            ->orderByRaw("CAST(SUBSTRING_INDEX({$column}, '-', -1) AS UNSIGNED) DESC")
            ->value($column);

        $next = $last ? ((int) substr($last, strrpos($last, '-') + 1)) + 1 : 1;

        // Safety loop: retry if number already exists (race condition guard)
        $candidate = "{$prefix}-{$year}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
        while ($model::where('company_id', $cid)->where($column, $candidate)->exists()) {
            $next++;
            $candidate = "{$prefix}-{$year}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }

    private function getAccountBalance(int $cid, string $type, ?int $year = null, ?string $month = null): array
    {
        $accounts = ChartOfAccount::where('company_id', $cid)
            ->where('account_type', $type)->where('is_active', true)
            ->orderBy('account_category')->orderBy('account_code')
            ->get()
            ->map(function ($acc) use ($cid, $year, $month) {
                $query = JournalEntryLine::where('account_id', $acc->id)
                    ->whereHas('journalEntry', function ($q) use ($cid, $year, $month) {
                        $q->where('company_id', $cid)->where('status', 'approved');
                        if ($year)  $q->whereYear('entry_date', $year);
                        if ($month) $q->whereMonth('entry_date', $month);
                    });
                $acc->balance = $query->sum('debit') - $query->sum('credit') + $acc->opening_balance;
                return $acc;
            });

        return ['accounts' => $accounts, 'total' => $accounts->sum('balance')];
    }
}
