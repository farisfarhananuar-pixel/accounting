<?php
// app/Http/Controllers/Developer/DeveloperController.php
namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeveloperController extends Controller
{
    // Developer credentials (hardcoded for simplicity — change these!)
    const DEV_USERNAME = 'developer';
    const DEV_PASSWORD = 'AccountEasy@Dev2025';

    public function showLogin()
    {
        if (session('developer_logged_in')) {
            return redirect()->route('developer.dashboard');
        }
        return view('developer.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($request->username === self::DEV_USERNAME && $request->password === self::DEV_PASSWORD) {
            session(['developer_logged_in' => true]);
            return redirect()->route('developer.dashboard');
        }

        return back()->withErrors(['username' => 'Invalid developer credentials.'])->withInput();
    }

    public function logout()
    {
        session()->forget('developer_logged_in');
        return redirect()->route('developer.login');
    }

    public function dashboard()
    {
        $pendingPayments = SubscriptionPayment::where('status', 'pending')->latest()->get();
        $approvedPayments = SubscriptionPayment::where('status', 'approved')->latest()->take(10)->get();
        $rejectedPayments = SubscriptionPayment::where('status', 'rejected')->latest()->take(10)->get();
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('subscription_status', 'active')->count();

        // Get current QR image
        $qrImage = \DB::table('developer_settings')->where('key', 'qr_image')->value('value');

        return view('developer.dashboard', compact(
            'pendingPayments', 'approvedPayments', 'rejectedPayments',
            'totalCompanies', 'activeCompanies', 'qrImage'
        ));
    }

    public function approvePayment(Request $request, $id)
    {
        $payment = SubscriptionPayment::findOrFail($id);

        // Activate the company
        if ($payment->company_id) {
            Company::where('id', $payment->company_id)->update([
                'subscription_status' => 'active',
                'payment_verified' => true,
            ]);
        }

        $payment->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', "Payment approved! Company '{$payment->company_name}' is now active.");
    }

    public function rejectPayment(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $payment = SubscriptionPayment::findOrFail($id);
        $payment->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->reason,
        ]);

        return back()->with('success', "Payment rejected for '{$payment->company_name}'.");
    }

    public function updateQr(Request $request)
    {
        $request->validate([
            'qr_image' => 'required|image|mimes:jpeg,png,gif,webp|max:5120',
        ]);

        // Delete old QR
        $old = \DB::table('developer_settings')->where('key', 'qr_image')->value('value');
        if ($old) Storage::disk('public')->delete($old);

        $path = $request->file('qr_image')->store('developer', 'public');

        \DB::table('developer_settings')->updateOrInsert(
            ['key' => 'qr_image'],
            ['value' => $path, 'updated_at' => now(), 'created_at' => now()]
        );

        return back()->with('success', 'QR code updated successfully!');
    }
}
