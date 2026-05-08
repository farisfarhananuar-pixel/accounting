<?php
// app/Models/SubscriptionPayment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    protected $fillable = [
        'company_id', 'company_name', 'contact_name', 'contact_email',
        'receipt_path', 'amount', 'status', 'approved_at', 'rejected_at',
        'rejection_reason', 'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}
