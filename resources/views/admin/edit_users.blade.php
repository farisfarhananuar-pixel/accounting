@extends('layouts.app')
@section('title','Manage Users') @section('page_title','Manage Users') @section('page_subtitle','Add, edit or remove user accounts')
@section('sidebar_nav')
<a href="{{ route('admin.dashboard') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-th-large"></i></span> Dashboard</a>
<a href="{{ route('admin.users') }}" class="nav-item-link active"><span class="nav-icon"><i class="fas fa-users"></i></span> Manage Users</a>
<a href="{{ route('admin.create_roles') }}" class="nav-item-link"><span class="nav-icon"><i class="fas fa-user-shield"></i></span> Create Roles</a>
<span class="nav-section-title">Data Management</span>
<a href="{{ route('admin.transactions') }}" class="nav-item-link {{ request()->routeIs('admin.transactions')?'active':'' }}"><span class="nav-icon"><i class="fas fa-trash-alt"></i></span> Manage Transactions</a>
@endsection
@section('content')
@if(session('success'))
<div class="alert alert-dismissible fade show" style="background:#d1fae5;color:#065f46;border:none;border-radius:12px" role="alert">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-dismissible fade show" style="background:#fef2f2;color:#991b1b;border:none;border-radius:12px" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Filter + Add Button --}}
<div class="chart-card mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, username, email..." value="{{ request('search') }}"></div>
        <div class="col-md-2"><select name="role" class="form-select form-select-sm"><option value="">All Roles</option><option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option><option value="manager" {{ request('role')=='manager'?'selected':'' }}>Manager</option><option value="executive_accountant" {{ request('role')=='executive_accountant'?'selected':'' }}>Accountant</option><option value="auditor" {{ request('role')=='auditor'?'selected':'' }}>Auditor</option></select></div>
        <div class="col-md-2"><select name="status" class="form-select form-select-sm"><option value="">All Status</option><option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option><option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option></select></div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-green btn-sm px-3"><i class="fas fa-search me-1"></i> Filter</button>
            <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
            <button type="button" class="btn btn-sm px-3 ms-auto" style="background:#7c3aed;color:white;border-radius:8px;font-weight:600" onclick="document.getElementById('addUserModal').style.display='flex'">
                <i class="fas fa-plus me-1"></i> Add User
            </button>
        </div>
    </form>
</div>

{{-- Users Table --}}
<div class="data-table">
    <div class="table-header"><h6><i class="fas fa-users me-2" style="color:var(--green-main)"></i>Users ({{ $users->total() }})</h6></div>
    <div class="table-responsive"><table class="table">
        <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Email</th><th>Last Login</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($users as $user)
        <tr>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div style="width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,var(--green-main),var(--green-dark));display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.8rem;flex-shrink:0">
                        {{ strtoupper(substr($user->name,0,2)) }}
                    </div>
                    <strong>{{ $user->name }}</strong>
                    @if($user->id == auth()->id()) <span style="font-size:.65rem;background:#d1fae5;color:#065f46;padding:2px 6px;border-radius:4px;font-weight:700">YOU</span> @endif
                </div>
            </td>
            <td><code style="background:#f0fdf9;color:var(--green-main);padding:2px 8px;border-radius:4px;font-size:.78rem">{{ $user->username }}</code></td>
            <td>
                @php $rc=['admin'=>['#fee2e2','#991b1b'],'manager'=>['#fef3c7','#92400e'],'executive_accountant'=>['#d1fae5','#065f46'],'auditor'=>['#dbeafe','#1e40af']]; $rb=$rc[$user->role]??['#f3f4f6','#374151']; @endphp
                <span style="background:{{ $rb[0] }};color:{{ $rb[1] }};padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700">{{ $user->role_label }}</span>
            </td>
            <td><small class="text-muted">{{ $user->email }}</small></td>
            <td><small class="text-muted">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</small></td>
            <td><span class="status-badge {{ $user->is_active?'badge-approved':'badge-rejected' }}">{{ $user->is_active?'Active':'Inactive' }}</span></td>
            <td>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm px-2" style="background:#dbeafe;color:#1e40af;border-radius:6px;font-size:.72rem" title="Edit" onclick="openEditModal({{ $user->id }},'{{ addslashes($user->name) }}','{{ $user->email }}','{{ $user->role }}','{{ $user->phone ?? '' }}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    @if($user->id != auth()->id())
                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}">@csrf
                        <button type="submit" class="btn btn-sm px-2" style="background:{{ $user->is_active?'#fee2e2':'#d1fae5' }};color:{{ $user->is_active?'#991b1b':'#065f46' }};border-radius:6px;font-size:.72rem" title="{{ $user->is_active?'Deactivate':'Activate' }}">
                            <i class="fas fa-{{ $user->is_active?'ban':'check' }}"></i>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.delete', $user) }}" onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm px-2" style="background:#fee2e2;color:#991b1b;border-radius:6px;font-size:.72rem" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center py-5" style="color:#9ca3af"><i class="fas fa-users fa-2x d-block mb-2"></i>No users found</td></tr>
        @endforelse
        </tbody>
    </table></div>
    @if($users->hasPages())<div class="p-3">{{ $users->withQueryString()->links() }}</div>@endif
</div>

{{-- Add User Modal --}}
<div id="addUserModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;padding:20px">
    <div style="background:white;border-radius:16px;padding:30px;max-width:480px;width:100%;max-height:90vh;overflow-y:auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 style="color:var(--green-dark);font-weight:700;margin:0"><i class="fas fa-user-plus me-2" style="color:#7c3aed"></i>Add New User</h5>
            <button onclick="document.getElementById('addUserModal').style.display='none'" style="background:none;border:none;font-size:1.2rem;color:#6b7280;cursor:pointer">✕</button>
        </div>
        @if($errors->any())<div style="background:#fef2f2;color:#dc2626;border-radius:8px;padding:10px;font-size:.82rem;margin-bottom:16px"><i class="fas fa-exclamation-circle me-1"></i>{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Full Name *</label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
            <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Username *</label><input type="text" name="username" class="form-control" value="{{ old('username') }}" required></div>
            <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Email *</label><input type="email" name="email" class="form-control" value="{{ old('email') }}" required></div>
            <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Role *</label>
                <select name="role" class="form-select" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="executive_accountant">Executive Accountant</option>
                    <option value="auditor">Auditor</option>
                </select></div>
            <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone') }}"></div>
            <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Password *</label><input type="password" name="password" class="form-control" required></div>
            <div class="mb-4"><label class="form-label" style="font-size:.82rem;font-weight:600">Confirm Password *</label><input type="password" name="password_confirmation" class="form-control" required></div>
            <div class="d-flex gap-2">
                <button type="button" onclick="document.getElementById('addUserModal').style.display='none'" class="btn btn-outline-secondary flex-fill">Cancel</button>
                <button type="submit" class="btn flex-fill" style="background:#7c3aed;color:white;border-radius:8px;font-weight:600"><i class="fas fa-save me-2"></i>Create User</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit User Modal --}}
<div id="editUserModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;padding:20px">
    <div style="background:white;border-radius:16px;padding:30px;max-width:480px;width:100%;max-height:90vh;overflow-y:auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 style="color:var(--green-dark);font-weight:700;margin:0"><i class="fas fa-edit me-2" style="color:#0369a1"></i>Edit User</h5>
            <button onclick="document.getElementById('editUserModal').style.display='none'" style="background:none;border:none;font-size:1.2rem;color:#6b7280;cursor:pointer">✕</button>
        </div>
        <form method="POST" id="editUserForm">
            @csrf @method('PUT')
            <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Full Name *</label><input type="text" name="name" id="edit_name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Email *</label><input type="email" name="email" id="edit_email" class="form-control" required></div>
            <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Role *</label>
                <select name="role" id="edit_role" class="form-select" required>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="executive_accountant">Executive Accountant</option>
                    <option value="auditor">Auditor</option>
                </select></div>
            <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">Phone</label><input type="text" name="phone" id="edit_phone" class="form-control"></div>
            <div class="mb-3"><label class="form-label" style="font-size:.82rem;font-weight:600">New Password <small class="text-muted">(leave blank to keep current)</small></label><input type="password" name="password" class="form-control"></div>
            <div class="mb-4"><label class="form-label" style="font-size:.82rem;font-weight:600">Confirm Password</label><input type="password" name="password_confirmation" class="form-control"></div>
            <div class="d-flex gap-2">
                <button type="button" onclick="document.getElementById('editUserModal').style.display='none'" class="btn btn-outline-secondary flex-fill">Cancel</button>
                <button type="submit" class="btn flex-fill" style="background:#0369a1;color:white;border-radius:8px;font-weight:600"><i class="fas fa-save me-2"></i>Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(id, name, email, role, phone) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('editUserForm').action = `/admin/users/${id}`;
    document.getElementById('editUserModal').style.display = 'flex';
}
@if($errors->any())
document.getElementById('addUserModal').style.display = 'flex';
@endif
</script>
@endpush
