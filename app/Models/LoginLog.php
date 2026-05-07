<?php
// app/Models/LoginLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $fillable = [
        'user_id', 'company_id', 'username_attempted',
        'status', 'ip_address', 'user_agent', 'role', 'logged_at'
    ];

    protected $casts = ['logged_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function company() { return $this->belongsTo(Company::class); }
}
