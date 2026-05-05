<?php
// app/Models/AuditTrail.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $fillable = [
        'user_id', 'company_id', 'action', 'module',
        'record_type', 'record_id', 'old_values', 'new_values',
        'ip_address', 'description'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function company() { return $this->belongsTo(Company::class); }

    public static function log(string $action, string $module, ?int $recordId = null, array $oldValues = [], array $newValues = [], string $description = ''): void
    {
        $user = auth()->user();
        if (!$user) return;

        self::create([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'action' => $action,
            'module' => $module,
            'record_type' => $module,
            'record_id' => $recordId,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => request()->ip(),
            'description' => $description,
        ]);
    }
}
