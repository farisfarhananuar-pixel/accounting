<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Invoice extends Model {
    use SoftDeletes;
    protected $fillable = ['company_id','customer_id','invoice_number','invoice_date','due_date','subtotal','tax_amount','total_amount','paid_amount','balance_due','status','notes','created_by','approved_by','rejection_reason'];
    protected $casts = ['invoice_date'=>'date','due_date'=>'date','subtotal'=>'decimal:2','tax_amount'=>'decimal:2','total_amount'=>'decimal:2','paid_amount'=>'decimal:2','balance_due'=>'decimal:2'];
    public function company() { return $this->belongsTo(Company::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function lines() { return $this->hasMany(InvoiceLine::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function isOverdue(): bool { return $this->due_date < now() && !in_array($this->status, ['paid']); }
}
