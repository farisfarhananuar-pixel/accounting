<?php
// app/Models/JournalEntry.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'entry_number', 'entry_date', 'description',
        'reference', 'status', 'created_by', 'approved_by', 'rejected_by',
        'rejection_reason', 'approved_at', 'rejected_at', 'total_debit', 'total_credit'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function rejecter() { return $this->belongsTo(User::class, 'rejected_by'); }
    public function lines() { return $this->hasMany(JournalEntryLine::class); }

    public function getStatusBadgeAttribute(): string {
        return match($this->status) {
            'draft' => '<span class="status-badge badge-draft">Draft</span>',
            'pending' => '<span class="status-badge badge-pending">Pending</span>',
            'approved' => '<span class="status-badge badge-approved">Approved</span>',
            'rejected' => '<span class="status-badge badge-rejected">Rejected</span>',
            default => '<span class="status-badge badge-draft">Unknown</span>',
        };
    }

    public function isBalanced(): bool {
        return abs($this->total_debit - $this->total_credit) < 0.01;
    }
}
