<?php
// app/Models/Customer.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Customer extends Model {
    use SoftDeletes;
    protected $fillable = ['company_id','customer_code','name','email','phone','address','tax_number','credit_limit','is_active'];
    protected $casts = ['credit_limit'=>'decimal:2','is_active'=>'boolean'];
    public function company() { return $this->belongsTo(Company::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
}
