<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\User;
use App\Models\ChartOfAccount;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Company 1: ABC Trading
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

        User::firstOrCreate(['username' => 'admin_abc'], [
            'company_id' => $company1->id,
            'name'       => 'Ahmad Rizal bin Hassan',
            'email'      => 'admin@abctrading.com',
            'password'   => Hash::make('Admin@1234'),
            'role'       => 'admin',
            'is_active'  => true,
        ]);

        User::firstOrCreate(['username' => 'manager_abc'], [
            'company_id' => $company1->id,
            'name'       => 'Siti Nurhaliza binti Aziz',
            'email'      => 'manager@abctrading.com',
            'password'   => Hash::make('Manager@1234'),
            'role'       => 'manager',
            'is_active'  => true,
        ]);

        User::firstOrCreate(['username' => 'accountant_abc'], [
            'company_id' => $company1->id,
            'name'       => 'Chong Wei Ming',
            'email'      => 'accountant@abctrading.com',
            'password'   => Hash::make('Account@1234'),
            'role'       => 'executive_accountant',
            'is_active'  => true,
        ]);

        User::firstOrCreate(['username' => 'auditor_abc'], [
            'company_id' => $company1->id,
            'name'       => 'Rajendran s/o Muthu',
            'email'      => 'auditor@abctrading.com',
            'password'   => Hash::make('Auditor@1234'),
            'role'       => 'auditor',
            'is_active'  => true,
        ]);

        // Company 2: XYZ Solutions
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

        User::firstOrCreate(['username' => 'admin_xyz'], [
            'company_id' => $company2->id,
            'name'       => 'Lim Ah Kow',
            'email'      => 'admin@xyzsolutions.com',
            'password'   => Hash::make('Admin@5678'),
            'role'       => 'admin',
            'is_active'  => true,
        ]);

        User::firstOrCreate(['username' => 'manager_xyz'], [
            'company_id' => $company2->id,
            'name'       => 'Farah binti Kamarudin',
            'email'      => 'manager@xyzsolutions.com',
            'password'   => Hash::make('Manager@5678'),
            'role'       => 'manager',
            'is_active'  => true,
        ]);

        User::firstOrCreate(['username' => 'accountant_xyz'], [
            'company_id' => $company2->id,
            'name'       => 'Kevin Tan Boon Hock',
            'email'      => 'accountant@xyzsolutions.com',
            'password'   => Hash::make('Account@5678'),
            'role'       => 'executive_accountant',
            'is_active'  => true,
        ]);

        User::firstOrCreate(['username' => 'auditor_xyz'], [
            'company_id' => $company2->id,
            'name'       => 'Priya a/p Subramaniam',
            'email'      => 'auditor@xyzsolutions.com',
            'password'   => Hash::make('Auditor@5678'),
            'role'       => 'auditor',
            'is_active'  => true,
        ]);

        // Chart of Accounts
        $this->seedChartOfAccounts($company1->id);
        $this->seedChartOfAccounts($company2->id);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function seedChartOfAccounts(int $companyId): void
    {
        $accounts = [
            ['code'=>'1000','name'=>'Cash and Bank','type'=>'asset','category'=>'current_asset'],
            ['code'=>'1010','name'=>'Cash on Hand','type'=>'asset','category'=>'current_asset'],
            ['code'=>'1020','name'=>'Bank Account - Maybank','type'=>'asset','category'=>'current_asset'],
            ['code'=>'1100','name'=>'Accounts Receivable','type'=>'asset','category'=>'current_asset'],
            ['code'=>'1200','name'=>'Inventory','type'=>'asset','category'=>'current_asset'],
            ['code'=>'1300','name'=>'Prepaid Expenses','type'=>'asset','category'=>'current_asset'],
            ['code'=>'1500','name'=>'Property & Equipment','type'=>'asset','category'=>'fixed_asset'],
            ['code'=>'1510','name'=>'Office Equipment','type'=>'asset','category'=>'fixed_asset'],
            ['code'=>'1520','name'=>'Furniture & Fixtures','type'=>'asset','category'=>'fixed_asset'],
            ['code'=>'1530','name'=>'Motor Vehicle','type'=>'asset','category'=>'fixed_asset'],
            ['code'=>'1600','name'=>'Accumulated Depreciation','type'=>'asset','category'=>'fixed_asset'],
            ['code'=>'2000','name'=>'Accounts Payable','type'=>'liability','category'=>'current_liability'],
            ['code'=>'2100','name'=>'Accrued Liabilities','type'=>'liability','category'=>'current_liability'],
            ['code'=>'2200','name'=>'Tax Payable','type'=>'liability','category'=>'current_liability'],
            ['code'=>'2300','name'=>'SST Payable','type'=>'liability','category'=>'current_liability'],
            ['code'=>'2500','name'=>'Long-term Loan','type'=>'liability','category'=>'long_term_liability'],
            ['code'=>'3000','name'=>'Share Capital','type'=>'equity','category'=>'equity'],
            ['code'=>'3100','name'=>'Retained Earnings','type'=>'equity','category'=>'equity'],
            ['code'=>'3200','name'=>'Current Year Profit/Loss','type'=>'equity','category'=>'equity'],
            ['code'=>'4000','name'=>'Sales Revenue','type'=>'revenue','category'=>'revenue'],
            ['code'=>'4100','name'=>'Service Revenue','type'=>'revenue','category'=>'revenue'],
            ['code'=>'4200','name'=>'Other Income','type'=>'revenue','category'=>'other_income'],
            ['code'=>'4300','name'=>'Interest Income','type'=>'revenue','category'=>'other_income'],
            ['code'=>'5000','name'=>'Cost of Goods Sold','type'=>'expense','category'=>'cost_of_goods'],
            ['code'=>'5100','name'=>'Salaries & Wages','type'=>'expense','category'=>'operating_expense'],
            ['code'=>'5200','name'=>'Rental Expense','type'=>'expense','category'=>'operating_expense'],
            ['code'=>'5300','name'=>'Utilities Expense','type'=>'expense','category'=>'operating_expense'],
            ['code'=>'5400','name'=>'Depreciation Expense','type'=>'expense','category'=>'operating_expense'],
            ['code'=>'5500','name'=>'Marketing & Advertising','type'=>'expense','category'=>'operating_expense'],
            ['code'=>'5600','name'=>'Professional Fees','type'=>'expense','category'=>'operating_expense'],
            ['code'=>'5700','name'=>'Insurance Expense','type'=>'expense','category'=>'operating_expense'],
            ['code'=>'5800','name'=>'Office Supplies','type'=>'expense','category'=>'operating_expense'],
            ['code'=>'5900','name'=>'Interest Expense','type'=>'expense','category'=>'other_expense'],
            ['code'=>'6000','name'=>'Tax Expense','type'=>'expense','category'=>'other_expense'],
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
