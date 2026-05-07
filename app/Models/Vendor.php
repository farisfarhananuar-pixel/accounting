<?php
// app/Models/Vendor.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Vendor extends Model {
    use SoftDeletes;
    protected $fillable = ['company_id','vendor_code','name','email','phone','address','tax_number','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function company() { return $this->belongsTo(Company::class); }
    public function bills() { return $this->hasMany(Bill::class); }
}
