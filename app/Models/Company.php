<?php
// app/Models/Company.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'registration_number', 'address',
        'phone', 'email', 'logo', 'subscription_status', 'trial_ends_at'
    ];

    protected $casts = ['trial_ends_at' => 'datetime'];

    public function users() { return $this->hasMany(User::class); }
    public function journalEntries() { return $this->hasMany(JournalEntry::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function bills() { return $this->hasMany(Bill::class); }
    public function chartOfAccounts() { return $this->hasMany(ChartOfAccount::class); }
}
