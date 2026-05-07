<?php
// app/Models/ChartOfAccount.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'account_code', 'account_name', 'account_type',
        'account_category', 'opening_balance', 'current_balance',
        'is_active', 'description', 'parent_account_id'
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function parent() { return $this->belongsTo(ChartOfAccount::class, 'parent_account_id'); }
    public function children() { return $this->hasMany(ChartOfAccount::class, 'parent_account_id'); }

    public function getTypeColorAttribute(): string {
        return match($this->account_type) {
            'asset' => 'success',
            'liability' => 'danger',
            'equity' => 'warning',
            'revenue' => 'info',
            'expense' => 'secondary',
            default => 'light',
        };
    }
}
