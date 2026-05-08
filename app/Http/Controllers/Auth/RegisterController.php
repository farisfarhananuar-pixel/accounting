<?php
// app/Http/Controllers/Auth/RegisterController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegister()
    {
        // Get QR code image from developer settings
        $qrImage = DB::table('developer_settings')->where('key', 'qr_image')->value('value');
        return view('auth.register', compact('qrImage'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'company_name'        => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:50|unique:companies,registration_number',
            'company_address'     => 'nullable|string|max:500',
            'company_phone'       => 'nullable|string|max:30',
            'company_email'       => 'nullable|email|max:255',
            'admin_name'          => 'required|string|max:255',
            'admin_username'      => 'required|string|max:50|unique:users,username',
            'admin_email'         => 'required|email|max:255|unique:users,email',
            'admin_password'      => 'required|string|min:8|confirmed',
            'payment_receipt'     => 'required|image|mimes:jpeg,png,gif,webp|max:5120',
        ]);

        DB::transaction(function () use ($request) {
            // Create company (inactive until payment approved)
            $company = Company::create([
                'name'                => $request->company_name,
                'registration_number' => $request->registration_number,
                'address'             => $request->company_address,
                'phone'               => $request->company_phone,
                'email'               => $request->company_email,
                'subscription_status' => 'inactive', // activated after approval
                'payment_verified'    => false,
            ]);

            // Create admin user (inactive until payment approved)
            User::create([
                'company_id' => $company->id,
                'name'       => $request->admin_name,
                'username'   => $request->admin_username,
                'email'      => $request->admin_email,
                'password'   => Hash::make($request->admin_password),
                'role'       => 'admin',
                'is_active'  => false, // activated after approval
            ]);

            // Store payment receipt
            $receiptPath = $request->file('payment_receipt')->store('payment_receipts', 'public');

            // Create subscription payment record
            SubscriptionPayment::create([
                'company_id'    => $company->id,
                'company_name'  => $request->company_name,
                'contact_name'  => $request->admin_name,
                'contact_email' => $request->admin_email,
                'receipt_path'  => $receiptPath,
                'amount'        => 50.00,
                'status'        => 'pending',
            ]);
        });

        return redirect()->route('register.pending');
    }

    public function pending()
    {
        return view('auth.register_pending');
    }
}
