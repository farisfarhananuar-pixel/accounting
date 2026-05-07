<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Bill extends Model {
    use SoftDeletes;
    protected $fillable = ['company_id','vendor_id','bill_number','vendor_invoice_number','bill_date','due_date','subtotal','tax_amount','total_amount','paid_amount','balance_due','status','notes','created_by','approved_by','rejection_reason'];
    protected $casts = ['bill_date'=>'date','due_date'=>'date','subtotal'=>'decimal:2','tax_amount'=>'decimal:2','total_amount'=>'decimal:2','paid_amount'=>'decimal:2','balance_due'=>'decimal:2'];
    public function company() { return $this->belongsTo(Company::class); }
    public function vendor() { return $this->belongsTo(Vendor::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
