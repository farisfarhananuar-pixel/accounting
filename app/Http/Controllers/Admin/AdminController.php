<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\JournalEntry;
use App\Models\Invoice;
use App\Models\Bill;
use App\Models\LoginLog;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    private function cid()
    {
        return auth()->user()->company_id;
    }

    // ===================== DASHBOARD =====================
    public function dashboard()
    {
        $cid = $this->cid();

        $totalUsers    = User::where('company_id', $cid)->where('is_active', true)->count();
        $totalJournals = JournalEntry::where('company_id', $cid)->count();
        $totalInvoices = Invoice::where('company_id', $cid)->count();
        $totalBills    = Bill::where('company_id', $cid)->count();

        $roleBreakdown = User::where('company_id', $cid)
            ->selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');

        $recentLogins = LoginLog::where('company_id', $cid)
            ->with('user')->latest()->take(8)->get();

        $company = Company::findOrFail($cid);

        return view('admin.admin_dashboard', compact(
            'totalUsers', 'totalJournals', 'totalInvoices', 'totalBills',
            'roleBreakdown', 'recentLogins', 'company'
        ));
    }

    // ===================== USER MANAGEMENT =====================
    public function users(Request $request)
    {
        $cid   = $this->cid();
        $query = User::where('company_id', $cid);

        if ($request->role)   $query->where('role', $request->role);
        if ($request->status) $query->where('is_active', $request->status === 'active');
        if ($request->search) $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%'.$request->search.'%')
              ->orWhere('username', 'like', '%'.$request->search.'%')
              ->orWhere('email', 'like', '%'.$request->search.'%');
        });

        $users = $query->latest()->paginate(15);
        return view('admin.edit_users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,manager,executive_accountant,auditor',
            'phone'    => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'company_id' => $this->cid(),
            'name'       => $request->name,
            'username'   => $request->username,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'phone'      => $request->phone,
            'is_active'  => true,
        ]);

        AuditTrail::log('create', 'user', $user->id, [], $user->toArray(), "Admin created user {$request->username} with role {$request->role}");

        return back()->with('success', "User '{$request->username}' created successfully!");
    }

    public function updateUser(Request $request, User $user)
    {
        // Ensure user belongs to same company
        abort_if($user->company_id !== $this->cid(), 403);

        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role'  => 'required|in:admin,manager,executive_accountant,auditor',
            'phone' => 'nullable|string|max:20',
        ]);

        $old = $user->toArray();
        $data = ['name' => $request->name, 'email' => $request->email, 'role' => $request->role, 'phone' => $request->phone];
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);

        AuditTrail::log('update', 'user', $user->id, $old, $user->fresh()->toArray(), "Admin updated user {$user->username}");

        return back()->with('success', "User '{$user->username}' updated successfully!");
    }

    public function deleteUser(User $user)
    {
        abort_if($user->company_id !== $this->cid(), 403);
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');

        AuditTrail::log('delete', 'user', $user->id, $user->toArray(), [], "Admin deleted user {$user->username}");
        $user->delete();

        return back()->with('success', "User '{$user->username}' deleted.");
    }

    public function toggleUserStatus(User $user)
    {
        abort_if($user->company_id !== $this->cid(), 403);
        abort_if($user->id === auth()->id(), 403, 'You cannot deactivate your own account.');

        $old = $user->toArray();
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';

        AuditTrail::log('update', 'user', $user->id, $old, $user->fresh()->toArray(), "Admin {$status} user {$user->username}");

        return back()->with('success', "User '{$user->username}' has been {$status}.");
    }

    public function createRoles()
    {
        $cid   = $this->cid();
        $users = User::where('company_id', $cid)->orderBy('role')->get();
        return view('admin.create_roles', compact('users'));
    }
}
