<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Accountant\AccountantController;
use App\Http\Controllers\Auditor\AuditorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Developer\DeveloperController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::get('/login', [LoginController::class, 'showLogin']);
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset
Route::get('/forgot-password', [LoginController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [LoginController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('password.update');

// Company Registration
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
Route::get('/register/pending', [RegisterController::class, 'pending'])->name('register.pending');

/*
|--------------------------------------------------------------------------
| Developer Portal Routes
|--------------------------------------------------------------------------
*/
Route::prefix('developer')->name('developer.')->group(function () {
    Route::get('/login', [DeveloperController::class, 'showLogin'])->name('login');
    Route::post('/login', [DeveloperController::class, 'login'])->name('login.post');
    Route::get('/logout', [DeveloperController::class, 'logout'])->name('logout');

    Route::middleware(['developer'])->group(function () {
        Route::get('/dashboard', [DeveloperController::class, 'dashboard'])->name('dashboard');
        Route::post('/payments/{id}/approve', [DeveloperController::class, 'approvePayment'])->name('approve_payment');
        Route::post('/payments/{id}/reject', [DeveloperController::class, 'rejectPayment'])->name('reject_payment');
        Route::post('/update-qr', [DeveloperController::class, 'updateQr'])->name('update_qr');
    });
});

/*
|--------------------------------------------------------------------------
| Profile Routes (authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.photo.remove');
    Route::get('/notifications', [ProfileController::class, 'getNotifications'])->name('notifications.get');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin', 'company.scope'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::get('/create-roles', [AdminController::class, 'createRoles'])->name('create_roles');
});

/*
|--------------------------------------------------------------------------
| Manager Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:manager', 'company.scope'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
    Route::get('/approve-reject', [ManagerController::class, 'approveReject'])->name('approve_reject');
    Route::post('/approve/{type}/{id}', [ManagerController::class, 'approve'])->name('approve');
    Route::post('/reject/{type}/{id}', [ManagerController::class, 'reject'])->name('reject');
    Route::get('/approval-queue', [ManagerController::class, 'approvalQueue'])->name('approval_queue');
    Route::get('/unusual-transactions', [ManagerController::class, 'unusualTransactions'])->name('unusual_transactions');
    Route::get('/reports', [ManagerController::class, 'reports'])->name('reports');
    Route::get('/journal-monitor', [ManagerController::class, 'journalMonitor'])->name('journal_monitor');
    Route::get('/ar-monitor', [ManagerController::class, 'arMonitor'])->name('ar_monitor');
    Route::get('/ap-monitor', [ManagerController::class, 'apMonitor'])->name('ap_monitor');
    Route::get('/chart-of-account', [ManagerController::class, 'chartOfAccount'])->name('chart_of_account');
    Route::get('/create-roles', [ManagerController::class, 'createRoles'])->name('create_roles');
    Route::post('/create-roles', [ManagerController::class, 'storeRole'])->name('create_roles.store');
    Route::get('/rejection-reasons', [ManagerController::class, 'rejectionReasons'])->name('rejection_reasons');
});

/*
|--------------------------------------------------------------------------
| Executive Accountant Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:executive_accountant', 'company.scope'])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/dashboard', [AccountantController::class, 'dashboard'])->name('dashboard');

    // Journal Entries
    Route::get('/journal-entries', [AccountantController::class, 'journalEntries'])->name('journal_entries');
    Route::get('/journal-entries/create', [AccountantController::class, 'createJournal'])->name('journal_entries.create');
    Route::post('/journal-entries', [AccountantController::class, 'storeJournal'])->name('journal_entries.store');
    Route::get('/journal-entries/{id}/edit', [AccountantController::class, 'editJournal'])->name('journal_entries.edit');
    Route::put('/journal-entries/{id}', [AccountantController::class, 'updateJournal'])->name('journal_entries.update');
    Route::post('/journal-entries/{id}/submit', [AccountantController::class, 'submitJournal'])->name('journal_entries.submit');
    Route::delete('/journal-entries/{id}', [AccountantController::class, 'deleteJournal'])->name('journal_entries.delete');

    // Account Receivable
    Route::get('/account-receivable', [AccountantController::class, 'accountReceivable'])->name('account_receivable');
    Route::get('/account-receivable/create', [AccountantController::class, 'createInvoice'])->name('account_receivable.create');
    Route::post('/account-receivable', [AccountantController::class, 'storeInvoice'])->name('account_receivable.store');
    Route::get('/account-receivable/{id}', [AccountantController::class, 'showInvoice'])->name('account_receivable.show');
    Route::post('/account-receivable/{id}/payment', [AccountantController::class, 'recordPayment'])->name('account_receivable.payment');

    // Account Payable
    Route::get('/account-payable', [AccountantController::class, 'accountPayable'])->name('account_payable');
    Route::get('/account-payable/create', [AccountantController::class, 'createBill'])->name('account_payable.create');
    Route::post('/account-payable', [AccountantController::class, 'storeBill'])->name('account_payable.store');
    Route::get('/account-payable/{id}', [AccountantController::class, 'showBill'])->name('account_payable.show');
    Route::post('/account-payable/{id}/payment', [AccountantController::class, 'recordBillPayment'])->name('account_payable.payment');

    // Chart of Account
    Route::get('/chart-of-account', [AccountantController::class, 'chartOfAccount'])->name('chart_of_account');
    Route::post('/chart-of-account', [AccountantController::class, 'storeAccount'])->name('chart_of_account.store');
    Route::put('/chart-of-account/{id}', [AccountantController::class, 'updateAccount'])->name('chart_of_account.update');

    // Reports
    Route::get('/general-ledger', [AccountantController::class, 'generalLedger'])->name('general_ledger');
    Route::get('/bank-reconciliation', [AccountantController::class, 'bankReconciliation'])->name('bank_reconciliation');
    Route::get('/fixed-asset', [AccountantController::class, 'fixedAsset'])->name('fixed_asset');
    Route::get('/trial-balance', [AccountantController::class, 'trialBalance'])->name('trial_balance');
    Route::get('/profit-loss', [AccountantController::class, 'profitLoss'])->name('profit_loss');
    Route::get('/financial-position', [AccountantController::class, 'financialPosition'])->name('financial_position');
    Route::get('/tax-calculations', [AccountantController::class, 'taxCalculations'])->name('tax_calculations');
    Route::get('/financial-statements', [AccountantController::class, 'financialStatements'])->name('financial_statements');
});

/*
|--------------------------------------------------------------------------
| Auditor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:auditor', 'company.scope'])->prefix('auditor')->name('auditor.')->group(function () {
    Route::get('/dashboard', [AuditorController::class, 'dashboard'])->name('dashboard');
    Route::get('/view-logs', [AuditorController::class, 'viewLogs'])->name('view_logs');
    Route::get('/audit-financial-report', [AuditorController::class, 'auditFinancialReport'])->name('audit_financial_report');
    Route::get('/audit-trail', [AuditorController::class, 'auditTrail'])->name('audit_trail');
    Route::get('/journal-entries', [AuditorController::class, 'journalEntries'])->name('journal_entries');
    Route::get('/general-ledger', [AuditorController::class, 'generalLedger'])->name('general_ledger');
    Route::get('/ar-ap', [AuditorController::class, 'arAp'])->name('ar_ap');
    Route::get('/payment-history', [AuditorController::class, 'paymentHistory'])->name('payment_history');
    Route::get('/audit-report', [AuditorController::class, 'auditReport'])->name('audit_report');
    Route::post('/audit-report/generate', [AuditorController::class, 'generateReport'])->name('audit_report.generate');
});
