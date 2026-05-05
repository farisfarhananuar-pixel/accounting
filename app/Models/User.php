<?php
// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'company_id', 'name', 'username', 'email', 'password',
        'role', 'is_active', 'phone', 'profile_photo',
        'last_login_at', 'last_login_ip',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company() { return $this->belongsTo(Company::class); }
    public function loginLogs() { return $this->hasMany(LoginLog::class); }
    public function auditTrails() { return $this->hasMany(AuditTrail::class); }
    public function journalEntries() { return $this->hasMany(JournalEntry::class, 'created_by'); }

    // Role helpers
    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isManager(): bool { return $this->role === 'manager'; }
    public function isAccountant(): bool { return $this->role === 'executive_accountant'; }
    public function isAuditor(): bool { return $this->role === 'auditor'; }

    public function getRoleLabelAttribute(): string {
        return match($this->role) {
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'executive_accountant' => 'Executive Accountant',
            'auditor' => 'Auditor',
            default => ucfirst($this->role),
        };
    }

    public function getRoleBadgeColorAttribute(): string {
        return match($this->role) {
            'admin' => 'danger',
            'manager' => 'warning',
            'executive_accountant' => 'success',
            'auditor' => 'info',
            default => 'secondary',
        };
    }
}
