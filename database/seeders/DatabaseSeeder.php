<?php
// database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\User;
use App\Models\ChartOfAccount;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Bill;
use App\Models\BillLine;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ========================
        // COMPANY 1: ABC Trading Sdn Bhd (Demo / Trial)
        // ========================
        $company1 = Company::firstOrCreate(
            ['registration_number' => '202301001234'],
            [
                'name'                => 'ABC Trading Sdn Bhd',
                'address'             => 'No. 1, Jalan Ampang, 50450 Kuala Lumpur',
                'phone'               => '03-21234567',
                'email'               => 'info@abctrading.com',
                'subscription_status' => 'active',
            ]
        );

        // Users for Company 1
        $adminAbc = User::firstOrCreate(
            ['username' => 'admin_abc'],
            [
                'company_id' => $company1->id,
                'name'       => 'Ahmad Rizal bin Hassan',
                'email'      => 'admin@abctrading.com',
                'password'   => Hash::make('Admin@1234'),
                'role'       => 'admin',
                'is_active'  => true,
            ]
        );

        $managerAbc = User::firstOrCreate(
            ['username' => 'manager_abc'],
            [
                'company_id' => $company1->id,
                'name'       => 'Siti Nurhaliza binti Aziz',
                'email'      => 'manager@abctrading.com',
                'password'   => Hash::make('Manager@1234'),
                'role'       => 'manager',
                'is_active'  => true,
            ]
        );

        $accountantAbc = User::firstOrCreate(
            ['username' => 'accountant_abc'],
            [
                'company_id' => $company1->id,
                'name'       => 'Chong Wei Ming',
                'email'      => 'accountant@abctrading.com',
                'password'   => Hash::make('Account@1234'),
                'role'       => 'executive_accountant',
                'is_active'  => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'auditor_abc'],
            [
                'company_id' => $company1->id,
                'name'       => 'Rajendran s/o Muthu',
                'email'      => 'auditor@abctrading.com',
                'password'   => Hash::make('Auditor@1234'),
                'role'       => 'auditor',
                'is_active'  => true,
            ]
        );

        // ========================
        // COMPANY 2: XYZ Solutions Sdn Bhd
        // ========================
        $company2 = Company::firstOrCreate(
            ['registration_number' => '202301005678'],
            [
                'name'                => 'XYZ Solutions Sdn Bhd',
                'address'             => 'Unit 5-8, Menara KLCC, 50088 Kuala Lumpur',
                'phone'               => '03-23456789',
                'email'               => 'info@xyzsolutions.com',
                'subscription_status' => 'active',
            ]
        );

        User::firstOrCreate(
            ['username' => 'admin_xyz'],
            [
                'company_id' => $company2->id,
                'name'       => 'Lim Ah Kow',
                'email'      => 'admin@xyzsolutions.com',
                'password'   => Hash::make('Admin@5678'),
                'role'       => 'admin',
                'is_active'  => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'manager_xyz'],
            [
                'company_id' => $company2->id,
                'name'       => 'Farah binti Kamarudin',
                'email'      => 'manager@xyzsolutions.com',
                'password'   => Hash::make('Manager@5678'),
                'role'       => 'manager',
                'is_active'  => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'accountant_xyz'],
            [
                'company_id' => $company2->id,
                'name'       => 'Kevin Tan Boon Hock',
                'email'      => 'accountant@xyzsolutions.com',
                'password'   => Hash::make('Account@5678'),
                'role'       => 'executive_accountant',
                'is_active'  => true,
            ]
        );

        User::firstOrCreate(
            ['username' => 'auditor_xyz'],
            [
                'company_id' => $company2->id,
                'name'       => 'Priya a/p Subramaniam',
                'email'      => 'auditor@xyzsolutions.com',
                'password'   => Hash::make('Auditor@5678'),
                'role'       => 'auditor',
                'is_active'  => true,
            ]
        );

        // ========================
        // Chart of Accounts (both companies)
        // ========================
        $this->seedChartOfAccounts($company1->id);
        $this->seedChartOfAccounts($company2->id);

        // ========================
        // SAMPLE DATA for ABC Trading (Company 1 only)
        // ========================
        $this->seedSampleData($company1->id, $accountantAbc->id, $managerAbc->id, $adminAbc->id);

        // ========================
        // Summary output
        // ========================
        $this->command->info('');
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('=== LOGIN CREDENTIALS ===');
        $this->command->info('');
        $this->command->info('COMPANY 1: ABC Trading Sdn Bhd');
        $this->command->info('  Admin:       username: admin_abc      | password: Admin@1234');
        $this->command->info('  Manager:     username: manager_abc    | password: Manager@1234');
        $this->command->info('  Accountant:  username: accountant_abc | password: Account@1234');
        $this->command->info('  Auditor:     username: auditor_abc    | password: Auditor@1234');
        $this->command->info('');
        $this->command->info('COMPANY 2: XYZ Solutions Sdn Bhd');
        $this->command->info('  Admin:       username: admin_xyz      | password: Admin@5678');
        $this->command->info('  Manager:     username: manager_xyz    | password: Manager@5678');
        $this->command->info('  Accountant:  username: accountant_xyz | password: Account@5678');
        $this->command->info('  Auditor:     username: auditor_xyz    | password: Auditor@5678');
        $this->command->info('');
        $this->command->info('DEVELOPER PORTAL');
        $this->command->info('  Username: (set in .env DEV_USERNAME)');
        $this->command->info('  Password: (set in .env DEV_PASSWORD)');
        $this->command->info('');
        $this->command->info('  Default fallback if .env not set:');
        $this->command->info('  Username: developer | Password: (empty - set DEV_PASSWORD in .env!)');
    }

    // -------------------------------------------------------
    // SAMPLE DATA — Customers, Vendors, Invoices, Bills, JEs
    // -------------------------------------------------------
    private function seedSampleData(int $companyId, int $accountantId, int $managerId, int $adminId): void
    {
        // Get account IDs we'll need
        $getAcc = fn($code) => ChartOfAccount::where('company_id', $companyId)
                                             ->where('account_code', $code)
                                             ->value('id');

        // ---- Customers ----
        $customers = [
            [
                'company_id'    => $companyId,
                'customer_code' => 'CUST-001',
                'name'          => 'Mega Retail Sdn Bhd',
                'email'         => 'billing@megaretail.com',
                'phone'         => '03-51234567',
                'address'       => 'No. 15, Jalan PJU 5/8, Petaling Jaya, Selangor',
                'tax_number'    => 'A001-1234-5678',
                'credit_limit'  => 50000.00,
                'is_active'     => true,
            ],
            [
                'company_id'    => $companyId,
                'customer_code' => 'CUST-002',
                'name'          => 'Sunrise Supermarket Bhd',
                'email'         => 'accounts@sunrisesm.com',
                'phone'         => '04-87654321',
                'address'       => 'Lot 22, Jalan Bukit Mertajam, 14000 Penang',
                'tax_number'    => 'B002-9876-5432',
                'credit_limit'  => 30000.00,
                'is_active'     => true,
            ],
            [
                'company_id'    => $companyId,
                'customer_code' => 'CUST-003',
                'name'          => 'Nusantara Enterprise',
                'email'         => 'finance@nusantara.com.my',
                'phone'         => '07-3456789',
                'address'       => 'No. 8, Jalan Dato Onn, 80000 Johor Bahru',
                'tax_number'    => null,
                'credit_limit'  => 20000.00,
                'is_active'     => true,
            ],
        ];

        $createdCustomers = [];
        foreach ($customers as $c) {
            $createdCustomers[] = Customer::firstOrCreate(
                ['company_id' => $companyId, 'customer_code' => $c['customer_code']],
                $c
            );
        }

        // ---- Vendors ----
        $vendors = [
            [
                'company_id'  => $companyId,
                'vendor_code' => 'VND-001',
                'name'        => 'Setia Supplier Sdn Bhd',
                'email'       => 'invoice@setiasupplier.com',
                'phone'       => '03-61234567',
                'address'     => 'Lot 7, Kawasan Perindustrian Shah Alam, Selangor',
                'tax_number'  => 'C003-1122-3344',
                'is_active'   => true,
            ],
            [
                'company_id'  => $companyId,
                'vendor_code' => 'VND-002',
                'name'        => 'Global Freight & Logistics',
                'email'       => 'billing@globalfreight.com.my',
                'phone'       => '03-77654321',
                'address'     => 'Suite 10-2, Kelana Jaya, 47301 Selangor',
                'tax_number'  => null,
                'is_active'   => true,
            ],
            [
                'company_id'  => $companyId,
                'vendor_code' => 'VND-003',
                'name'        => 'Bestari Office Supplies',
                'email'       => 'sales@bestarioffice.com',
                'phone'       => '03-91234567',
                'address'     => 'No. 33, Jalan Masjid India, 50100 Kuala Lumpur',
                'tax_number'  => null,
                'is_active'   => true,
            ],
        ];

        $createdVendors = [];
        foreach ($vendors as $v) {
            $createdVendors[] = Vendor::firstOrCreate(
                ['company_id' => $companyId, 'vendor_code' => $v['vendor_code']],
                $v
            );
        }

        // ---- Invoices (AR) ----
        // Only create if none exist yet
        if (Invoice::where('company_id', $companyId)->doesntExist()) {

            $invoicesData = [
                // INV-2025-001 — Approved & Paid
                [
                    'meta' => [
                        'invoice_number' => 'INV-2025-001',
                        'invoice_date'   => '2025-01-10',
                        'due_date'       => '2025-02-10',
                        'status'         => 'paid',
                        'notes'          => 'Bayaran penuh diterima via bank transfer.',
                        'customer_idx'   => 0,
                    ],
                    'lines' => [
                        ['description' => 'Jualan Barangan Runcit (Jan)', 'quantity' => 100, 'unit_price' => 50.00,  'tax_rate' => 8],
                        ['description' => 'Perkhidmatan Penghantaran',    'quantity' => 1,   'unit_price' => 300.00, 'tax_rate' => 0],
                    ],
                ],
                // INV-2025-002 — Approved & Partially Paid
                [
                    'meta' => [
                        'invoice_number' => 'INV-2025-002',
                        'invoice_date'   => '2025-02-15',
                        'due_date'       => '2025-03-15',
                        'status'         => 'partially_paid',
                        'notes'          => 'Bayaran sebahagian RM 2,000 diterima.',
                        'customer_idx'   => 1,
                    ],
                    'lines' => [
                        ['description' => 'Jualan Barangan Runcit (Feb)', 'quantity' => 80,  'unit_price' => 55.00,  'tax_rate' => 8],
                        ['description' => 'Diskaun Promosi',              'quantity' => 1,   'unit_price' => -200.00,'tax_rate' => 0],
                    ],
                ],
                // INV-2025-003 — Pending Approval
                [
                    'meta' => [
                        'invoice_number' => 'INV-2025-003',
                        'invoice_date'   => '2025-03-20',
                        'due_date'       => '2025-04-20',
                        'status'         => 'pending',
                        'notes'          => 'Menunggu kelulusan pengurus.',
                        'customer_idx'   => 2,
                    ],
                    'lines' => [
                        ['description' => 'Jualan Barangan Pembungkusan', 'quantity' => 200, 'unit_price' => 12.50, 'tax_rate' => 8],
                    ],
                ],
                // INV-2025-004 — Overdue
                [
                    'meta' => [
                        'invoice_number' => 'INV-2025-004',
                        'invoice_date'   => '2025-01-05',
                        'due_date'       => '2025-02-05',
                        'status'         => 'overdue',
                        'notes'          => 'Invois tertunggak. Sila hubungi pelanggan.',
                        'customer_idx'   => 0,
                    ],
                    'lines' => [
                        ['description' => 'Bekalan Barangan Kering',   'quantity' => 50,  'unit_price' => 75.00, 'tax_rate' => 8],
                        ['description' => 'Caj Pengendalian Khas',     'quantity' => 1,   'unit_price' => 150.00,'tax_rate' => 0],
                    ],
                ],
                // INV-2025-005 — Draft
                [
                    'meta' => [
                        'invoice_number' => 'INV-2025-005',
                        'invoice_date'   => '2025-04-01',
                        'due_date'       => '2025-05-01',
                        'status'         => 'draft',
                        'notes'          => 'Draf — belum dihantar kepada pelanggan.',
                        'customer_idx'   => 1,
                    ],
                    'lines' => [
                        ['description' => 'Jualan Barangan Premix (Apr)', 'quantity' => 60, 'unit_price' => 45.00, 'tax_rate' => 8],
                    ],
                ],
            ];

            foreach ($invoicesData as $inv) {
                $m = $inv['meta'];
                $subtotal = 0;
                $taxTotal  = 0;

                foreach ($inv['lines'] as $line) {
                    $lineAmt  = $line['quantity'] * $line['unit_price'];
                    $lineTax  = $lineAmt * ($line['tax_rate'] / 100);
                    $subtotal += $lineAmt;
                    $taxTotal  += $lineTax;
                }

                $total   = $subtotal + $taxTotal;
                $paid    = match($m['status']) {
                    'paid'           => $total,
                    'partially_paid' => 2000.00,
                    default          => 0,
                };
                $balance = $total - $paid;

                $approvedBy = in_array($m['status'], ['approved','paid','partially_paid','overdue'])
                    ? $managerId : null;

                $invoice = Invoice::create([
                    'company_id'    => $companyId,
                    'customer_id'   => $createdCustomers[$m['customer_idx']]->id,
                    'invoice_number'=> $m['invoice_number'],
                    'invoice_date'  => $m['invoice_date'],
                    'due_date'      => $m['due_date'],
                    'subtotal'      => $subtotal,
                    'tax_amount'    => $taxTotal,
                    'total_amount'  => $total,
                    'paid_amount'   => $paid,
                    'balance_due'   => $balance,
                    'status'        => $m['status'],
                    'notes'         => $m['notes'],
                    'created_by'    => $accountantId,
                    'approved_by'   => $approvedBy,
                ]);

                foreach ($inv['lines'] as $line) {
                    $lineAmt = $line['quantity'] * $line['unit_price'];
                    InvoiceLine::create([
                        'invoice_id'  => $invoice->id,
                        'description' => $line['description'],
                        'quantity'    => $line['quantity'],
                        'unit_price'  => $line['unit_price'],
                        'tax_rate'    => $line['tax_rate'],
                        'amount'      => $lineAmt + ($lineAmt * $line['tax_rate'] / 100),
                    ]);
                }
            }
        }

        // ---- Bills (AP) ----
        if (Bill::where('company_id', $companyId)->doesntExist()) {

            $billsData = [
                // BILL-2025-001 — Paid
                [
                    'meta' => [
                        'bill_number'           => 'BILL-2025-001',
                        'vendor_invoice_number' => 'SS-INV-0012',
                        'bill_date'             => '2025-01-12',
                        'due_date'              => '2025-02-12',
                        'status'                => 'paid',
                        'notes'                 => 'Bayaran penuh kepada pembekal.',
                        'vendor_idx'            => 0,
                    ],
                    'lines' => [
                        ['description' => 'Pembelian Stok Barangan Runcit Jan', 'quantity' => 200, 'unit_price' => 25.00, 'tax_rate' => 8],
                    ],
                ],
                // BILL-2025-002 — Approved, pending payment
                [
                    'meta' => [
                        'bill_number'           => 'BILL-2025-002',
                        'vendor_invoice_number' => 'GFL-2025-0088',
                        'bill_date'             => '2025-02-18',
                        'due_date'              => '2025-03-18',
                        'status'                => 'approved',
                        'notes'                 => 'Bil penghantaran — belum bayar.',
                        'vendor_idx'            => 1,
                    ],
                    'lines' => [
                        ['description' => 'Perkhidmatan Pengangkutan Feb', 'quantity' => 5, 'unit_price' => 350.00, 'tax_rate' => 0],
                        ['description' => 'Caj Insurans Kargo',           'quantity' => 1, 'unit_price' => 200.00, 'tax_rate' => 0],
                    ],
                ],
                // BILL-2025-003 — Pending
                [
                    'meta' => [
                        'bill_number'           => 'BILL-2025-003',
                        'vendor_invoice_number' => 'BO-2025-551',
                        'bill_date'             => '2025-03-22',
                        'due_date'              => '2025-04-22',
                        'status'                => 'pending',
                        'notes'                 => 'Menunggu kelulusan pengurus.',
                        'vendor_idx'            => 2,
                    ],
                    'lines' => [
                        ['description' => 'Alat Tulis & Bekalan Pejabat',   'quantity' => 10, 'unit_price' => 45.00, 'tax_rate' => 8],
                        ['description' => 'Toner Pencetak (x4)',            'quantity' => 4,  'unit_price' => 85.00, 'tax_rate' => 8],
                    ],
                ],
                // BILL-2025-004 — Overdue
                [
                    'meta' => [
                        'bill_number'           => 'BILL-2025-004',
                        'vendor_invoice_number' => 'SS-INV-0020',
                        'bill_date'             => '2024-12-05',
                        'due_date'              => '2025-01-05',
                        'status'                => 'overdue',
                        'notes'                 => 'Bil tertunggak. Perlu dibayar segera.',
                        'vendor_idx'            => 0,
                    ],
                    'lines' => [
                        ['description' => 'Stok Akhir Tahun 2024', 'quantity' => 150, 'unit_price' => 30.00, 'tax_rate' => 8],
                    ],
                ],
            ];

            foreach ($billsData as $bill) {
                $m        = $bill['meta'];
                $subtotal = 0;
                $taxTotal  = 0;

                foreach ($bill['lines'] as $line) {
                    $lineAmt  = $line['quantity'] * $line['unit_price'];
                    $lineTax  = $lineAmt * ($line['tax_rate'] / 100);
                    $subtotal += $lineAmt;
                    $taxTotal  += $lineTax;
                }

                $total   = $subtotal + $taxTotal;
                $paid    = $m['status'] === 'paid' ? $total : 0;
                $balance = $total - $paid;

                $approvedBy = in_array($m['status'], ['approved','paid','overdue'])
                    ? $managerId : null;

                $b = Bill::create([
                    'company_id'           => $companyId,
                    'vendor_id'            => $createdVendors[$m['vendor_idx']]->id,
                    'bill_number'          => $m['bill_number'],
                    'vendor_invoice_number'=> $m['vendor_invoice_number'],
                    'bill_date'            => $m['bill_date'],
                    'due_date'             => $m['due_date'],
                    'subtotal'             => $subtotal,
                    'tax_amount'           => $taxTotal,
                    'total_amount'         => $total,
                    'paid_amount'          => $paid,
                    'balance_due'          => $balance,
                    'status'               => $m['status'],
                    'notes'                => $m['notes'],
                    'created_by'           => $accountantId,
                    'approved_by'          => $approvedBy,
                ]);

                foreach ($bill['lines'] as $line) {
                    $lineAmt = $line['quantity'] * $line['unit_price'];
                    BillLine::create([
                        'bill_id'     => $b->id,
                        'description' => $line['description'],
                        'quantity'    => $line['quantity'],
                        'unit_price'  => $line['unit_price'],
                        'tax_rate'    => $line['tax_rate'],
                        'amount'      => $lineAmt + ($lineAmt * $line['tax_rate'] / 100),
                    ]);
                }
            }
        }

        // ---- Journal Entries ----
        if (JournalEntry::where('company_id', $companyId)->doesntExist()) {

            $accCash     = $getAcc('1010');
            $accBank     = $getAcc('1020');
            $accAR       = $getAcc('1100');
            $accAP       = $getAcc('2000');
            $accSales    = $getAcc('4000');
            $accService  = $getAcc('4100');
            $accCOGS     = $getAcc('5000');
            $accSalary   = $getAcc('5100');
            $accRental   = $getAcc('5200');
            $accUtility  = $getAcc('5300');
            $accCapital  = $getAcc('3000');
            $accSST      = $getAcc('2300');

            $journalEntries = [
                [
                    'entry_number' => 'JE-2025-001',
                    'entry_date'   => '2025-01-02',
                    'description'  => 'Modal awal syarikat — sumbangan pemegang saham',
                    'status'       => 'approved',
                    'lines' => [
                        ['account_id' => $accBank,    'description' => 'Wang masuk ke akaun bank', 'debit' => 100000.00, 'credit' => 0],
                        ['account_id' => $accCapital, 'description' => 'Modal saham',               'debit' => 0,         'credit' => 100000.00],
                    ],
                ],
                [
                    'entry_number' => 'JE-2025-002',
                    'entry_date'   => '2025-01-10',
                    'description'  => 'Jualan barangan runcit kepada Mega Retail (INV-2025-001)',
                    'status'       => 'approved',
                    'lines' => [
                        ['account_id' => $accAR,    'description' => 'Akaun belum terima — Mega Retail', 'debit' => 5724.00, 'credit' => 0],
                        ['account_id' => $accSales,  'description' => 'Hasil jualan barangan runcit',   'debit' => 0,        'credit' => 5000.00],
                        ['account_id' => $accService,'description' => 'Caj penghantaran',               'debit' => 0,        'credit' => 300.00],
                        ['account_id' => $accSST,    'description' => 'SST 8%',                         'debit' => 0,        'credit' => 424.00],
                    ],
                ],
                [
                    'entry_number' => 'JE-2025-003',
                    'entry_date'   => '2025-01-12',
                    'description'  => 'Pembelian stok dari Setia Supplier (BILL-2025-001)',
                    'status'       => 'approved',
                    'lines' => [
                        ['account_id' => $accCOGS, 'description' => 'Kos barang jualan — stok Jan', 'debit' => 5000.00, 'credit' => 0],
                        ['account_id' => $accSST,  'description' => 'SST 8% (input)',               'debit' => 400.00,  'credit' => 0],
                        ['account_id' => $accAP,   'description' => 'Akaun belum bayar — Setia Supplier','debit' => 0, 'credit' => 5400.00],
                    ],
                ],
                [
                    'entry_number' => 'JE-2025-004',
                    'entry_date'   => '2025-01-31',
                    'description'  => 'Gaji pekerja bulan Januari 2025',
                    'status'       => 'approved',
                    'lines' => [
                        ['account_id' => $accSalary, 'description' => 'Gaji & elaun pekerja Jan 2025', 'debit' => 8500.00, 'credit' => 0],
                        ['account_id' => $accBank,   'description' => 'Bayaran gaji melalui bank',     'debit' => 0,        'credit' => 8500.00],
                    ],
                ],
                [
                    'entry_number' => 'JE-2025-005',
                    'entry_date'   => '2025-02-01',
                    'description'  => 'Bayaran sewa premis Feb 2025',
                    'status'       => 'approved',
                    'lines' => [
                        ['account_id' => $accRental, 'description' => 'Sewa premis Jalan Ampang Feb', 'debit' => 3500.00, 'credit' => 0],
                        ['account_id' => $accBank,   'description' => 'Bayaran sewa melalui bank',    'debit' => 0,        'credit' => 3500.00],
                    ],
                ],
                [
                    'entry_number' => 'JE-2025-006',
                    'entry_date'   => '2025-02-28',
                    'description'  => 'Bil utiliti pejabat Februari 2025 (TNB & Air)',
                    'status'       => 'approved',
                    'lines' => [
                        ['account_id' => $accUtility, 'description' => 'Kos elektrik & air Feb 2025', 'debit' => 750.00, 'credit' => 0],
                        ['account_id' => $accBank,    'description' => 'Bayaran melalui akaun bank',  'debit' => 0,       'credit' => 750.00],
                    ],
                ],
                [
                    'entry_number' => 'JE-2025-007',
                    'entry_date'   => '2025-03-05',
                    'description'  => 'Terima bayaran dari Mega Retail bagi INV-2025-001',
                    'status'       => 'approved',
                    'lines' => [
                        ['account_id' => $accBank, 'description' => 'Penerimaan bayaran — Mega Retail', 'debit' => 5724.00, 'credit' => 0],
                        ['account_id' => $accAR,   'description' => 'Tolak akaun belum terima',         'debit' => 0,        'credit' => 5724.00],
                    ],
                ],
                [
                    'entry_number' => 'JE-2025-008',
                    'entry_date'   => '2025-03-31',
                    'description'  => 'Gaji pekerja bulan Mac 2025',
                    'status'       => 'pending',
                    'lines' => [
                        ['account_id' => $accSalary, 'description' => 'Gaji & elaun pekerja Mac 2025', 'debit' => 8500.00, 'credit' => 0],
                        ['account_id' => $accBank,   'description' => 'Bayaran gaji melalui bank',     'debit' => 0,        'credit' => 8500.00],
                    ],
                ],
            ];

            foreach ($journalEntries as $je) {
                $totalDebit  = collect($je['lines'])->sum('debit');
                $totalCredit = collect($je['lines'])->sum('credit');

                $approvedBy = $je['status'] === 'approved' ? $managerId : null;

                $entry = JournalEntry::create([
                    'company_id'   => $companyId,
                    'entry_number' => $je['entry_number'],
                    'entry_date'   => $je['entry_date'],
                    'description'  => $je['description'],
                    'status'       => $je['status'],
                    'created_by'   => $accountantId,
                    'approved_by'  => $approvedBy,
                    'approved_at'  => $approvedBy ? now() : null,
                    'total_debit'  => $totalDebit,
                    'total_credit' => $totalCredit,
                ]);

                foreach ($je['lines'] as $line) {
                    JournalEntryLine::create([
                        'journal_entry_id' => $entry->id,
                        'account_id'       => $line['account_id'],
                        'description'      => $line['description'],
                        'debit'            => $line['debit'],
                        'credit'           => $line['credit'],
                    ]);
                }
            }
        }
    }

    // -------------------------------------------------------
    // CHART OF ACCOUNTS (standard Malaysian chart)
    // -------------------------------------------------------
    private function seedChartOfAccounts(int $companyId): void
    {
        $accounts = [
            // Assets
            ['code' => '1000', 'name' => 'Cash and Bank',              'type' => 'asset',   'category' => 'current_asset'],
            ['code' => '1010', 'name' => 'Cash on Hand',               'type' => 'asset',   'category' => 'current_asset'],
            ['code' => '1020', 'name' => 'Bank Account - Maybank',     'type' => 'asset',   'category' => 'current_asset'],
            ['code' => '1100', 'name' => 'Accounts Receivable',        'type' => 'asset',   'category' => 'current_asset'],
            ['code' => '1200', 'name' => 'Inventory',                  'type' => 'asset',   'category' => 'current_asset'],
            ['code' => '1300', 'name' => 'Prepaid Expenses',           'type' => 'asset',   'category' => 'current_asset'],
            ['code' => '1500', 'name' => 'Property & Equipment',       'type' => 'asset',   'category' => 'fixed_asset'],
            ['code' => '1510', 'name' => 'Office Equipment',           'type' => 'asset',   'category' => 'fixed_asset'],
            ['code' => '1520', 'name' => 'Furniture & Fixtures',       'type' => 'asset',   'category' => 'fixed_asset'],
            ['code' => '1530', 'name' => 'Motor Vehicle',              'type' => 'asset',   'category' => 'fixed_asset'],
            ['code' => '1600', 'name' => 'Accumulated Depreciation',   'type' => 'asset',   'category' => 'fixed_asset'],
            // Liabilities
            ['code' => '2000', 'name' => 'Accounts Payable',           'type' => 'liability','category' => 'current_liability'],
            ['code' => '2100', 'name' => 'Accrued Liabilities',        'type' => 'liability','category' => 'current_liability'],
            ['code' => '2200', 'name' => 'Tax Payable',                'type' => 'liability','category' => 'current_liability'],
            ['code' => '2300', 'name' => 'SST Payable',                'type' => 'liability','category' => 'current_liability'],
            ['code' => '2500', 'name' => 'Long-term Loan',             'type' => 'liability','category' => 'long_term_liability'],
            // Equity
            ['code' => '3000', 'name' => 'Share Capital',              'type' => 'equity',  'category' => 'equity'],
            ['code' => '3100', 'name' => 'Retained Earnings',          'type' => 'equity',  'category' => 'equity'],
            ['code' => '3200', 'name' => 'Current Year Profit/Loss',   'type' => 'equity',  'category' => 'equity'],
            // Revenue
            ['code' => '4000', 'name' => 'Sales Revenue',              'type' => 'revenue', 'category' => 'revenue'],
            ['code' => '4100', 'name' => 'Service Revenue',            'type' => 'revenue', 'category' => 'revenue'],
            ['code' => '4200', 'name' => 'Other Income',               'type' => 'revenue', 'category' => 'other_income'],
            ['code' => '4300', 'name' => 'Interest Income',            'type' => 'revenue', 'category' => 'other_income'],
            // Expenses
            ['code' => '5000', 'name' => 'Cost of Goods Sold',         'type' => 'expense', 'category' => 'cost_of_goods'],
            ['code' => '5100', 'name' => 'Salaries & Wages',           'type' => 'expense', 'category' => 'operating_expense'],
            ['code' => '5200', 'name' => 'Rental Expense',             'type' => 'expense', 'category' => 'operating_expense'],
            ['code' => '5300', 'name' => 'Utilities Expense',          'type' => 'expense', 'category' => 'operating_expense'],
            ['code' => '5400', 'name' => 'Depreciation Expense',       'type' => 'expense', 'category' => 'operating_expense'],
            ['code' => '5500', 'name' => 'Marketing & Advertising',    'type' => 'expense', 'category' => 'operating_expense'],
            ['code' => '5600', 'name' => 'Professional Fees',          'type' => 'expense', 'category' => 'operating_expense'],
            ['code' => '5700', 'name' => 'Insurance Expense',          'type' => 'expense', 'category' => 'operating_expense'],
            ['code' => '5800', 'name' => 'Office Supplies',            'type' => 'expense', 'category' => 'operating_expense'],
            ['code' => '5900', 'name' => 'Interest Expense',           'type' => 'expense', 'category' => 'other_expense'],
            ['code' => '6000', 'name' => 'Tax Expense',                'type' => 'expense', 'category' => 'other_expense'],
        ];

        foreach ($accounts as $acc) {
            ChartOfAccount::firstOrCreate(
                ['company_id' => $companyId, 'account_code' => $acc['code']],
                [
                    'account_name'     => $acc['name'],
                    'account_type'     => $acc['type'],
                    'account_category' => $acc['category'],
                    'is_active'        => true,
                ]
            );
        }
    }
}
