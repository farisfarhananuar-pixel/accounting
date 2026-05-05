# 💼 Account Easy - Multi-User Accounting Information System

> **Built with Laravel 10 | Multi-Tenant | Role-Based Access Control | Audit Compliant**

---

## 🚀 Setup Instructions (Railway + GitHub)

### 1. Clone & Install
```bash
git clone https://github.com/YOUR_USERNAME/account-easy.git
cd account-easy
composer install
cp .env.example .env
php artisan key:generate
```

### 2. Configure Database (.env)
```env
DB_CONNECTION=mysql
DB_HOST=your-railway-mysql-host
DB_PORT=3306
DB_DATABASE=account_easy
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Run Migrations & Seed
```bash
php artisan migrate
php artisan db:seed
```

### 4. Deploy to Railway
- Push to GitHub
- Connect repo in Railway dashboard
- Set environment variables
- Railway auto-deploys!

---

## 🔐 Login Credentials

### COMPANY 1: ABC Trading Sdn Bhd

| Role | Username | Password |
|------|----------|----------|
| 🔴 Admin | `admin_abc` | `Admin@1234` |
| 🟡 Manager | `manager_abc` | `Manager@1234` |
| 🟢 Executive Accountant | `accountant_abc` | `Account@1234` |
| 🔵 Auditor | `auditor_abc` | `Auditor@1234` |

### COMPANY 2: XYZ Solutions Sdn Bhd

| Role | Username | Password |
|------|----------|----------|
| 🔴 Admin | `admin_xyz` | `Admin@5678` |
| 🟡 Manager | `manager_xyz` | `Manager@5678` |
| 🟢 Executive Accountant | `accountant_xyz` | `Account@5678` |
| 🔵 Auditor | `auditor_xyz` | `Auditor@5678` |

---

## 📦 Parts Overview

| Part | Contents |
|------|----------|
| **Part 1** | Foundation: Auth, Models, Migrations, Login Page, Layouts |
| **Part 2** | Executive Accountant: Dashboard, Journal Entries, AR, AP |
| **Part 3** | Manager: Dashboard, Approve/Reject, Reports |
| **Part 4** | Admin & Auditor: User Management, Audit Trail, Reports |

---

## 🏗️ Architecture

### Multi-Tenancy
Every database table includes `company_id`. All queries are automatically scoped to the authenticated user's company using `CompanyScopeMiddleware`. Users from Company A **cannot** see Company B's data.

### Roles
- **Admin** - Manages users & company settings
- **Manager** - Approves/rejects transactions, views reports
- **Executive Accountant** - Creates journal entries, invoices, bills
- **Auditor** - Read-only access, generates audit reports

### Approval Workflow
```
Draft → Pending (submitted by Accountant) → Approved/Rejected (by Manager)
```

---

## 🎨 Design Theme
- **Primary Color**: Green (`#1a7a57`)
- **Font**: Poppins
- **Framework**: Bootstrap 5
- **Charts**: Chart.js
- **Mobile**: Fully responsive

---

## 📄 Pages List

### Auth
- `/` or `/login` - Login page (index.php equivalent)
- `/forgot-password` - Password reset request
- `/reset-password/{token}` - Password reset form

### Executive Accountant (`/accountant/`)
- `dashboard` - Main dashboard with charts
- `journal-entries` - Journal entry management
- `account-receivable` - AR/Invoice management  
- `account-payable` - AP/Bill management
- `chart-of-account` - Chart of accounts
- `general-ledger` - General ledger view
- `bank-reconciliation` - Bank reconciliation
- `fixed-asset` - Fixed asset management
- `trial-balance` - Trial balance report
- `profit-loss` - Profit & Loss statement
- `financial-position` - Balance sheet
- `tax-calculations` - Tax calculations
- `financial-statements` - Combined view

### Manager (`/manager/`)
- `dashboard` - Manager dashboard
- `approve-reject` - Transaction approval
- `approval-queue` - Pending approvals
- `unusual-transactions` - Anomaly detection
- `reports` - Financial reports view
- `journal-monitor` - Journal monitoring
- `ar-monitor` - AR monitoring
- `ap-monitor` - AP monitoring
- `chart-of-account` - COA view
- `create-roles` - Create accountant/auditor accounts

### Admin (`/admin/`)
- `dashboard` - Admin dashboard
- `users` - User management (add/edit/delete)
- `create-roles` - Create all role accounts

### Auditor (`/auditor/`)
- `dashboard` - Auditor dashboard
- `view-logs` - Login/activity logs
- `audit-financial-report` - Financial audit
- `audit-trail` - Complete audit trail
- `journal-entries` - Journal entry review
- `general-ledger` - GL view
- `ar-ap` - AR/AP view
- `payment-history` - Payment records
- `audit-report` - Generate audit reports
