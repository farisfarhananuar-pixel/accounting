<?php
// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        // Log the attempt
        $logData = [
            'user_id' => $user?->id,
            'company_id' => $user?->company_id,
            'username_attempted' => $request->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'role' => $user?->role,
        ];

        if (!$user || !Hash::check($request->password, $user->password)) {
            LoginLog::create(array_merge($logData, ['status' => 'failed']));

            throw ValidationException::withMessages([
                'username' => 'The provided credentials are incorrect.',
            ]);
        }

        if (!$user->is_active) {
            LoginLog::create(array_merge($logData, ['status' => 'failed']));
            throw ValidationException::withMessages([
                'username' => 'Your account has been deactivated. Please contact your administrator.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        // Update last login
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        LoginLog::create(array_merge($logData, ['status' => 'success']));

        AuditTrail::log('login', 'auth', $user->id, [], [], "User {$user->name} logged in");

        $request->session()->regenerate();

        return redirect()->intended($this->getRedirectRoute($user->role));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditTrail::log('logout', 'auth', $user->id, [], [], "User {$user->name} logged out");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    private function getRedirectRoute(string $role): string
    {
        return match($role) {
            'admin' => route('admin.dashboard'),
            'manager' => route('manager.dashboard'),
            'executive_accountant' => route('accountant.dashboard'),
            'auditor' => route('auditor.dashboard'),
            default => route('login'),
        };
    }

    private function redirectByRole(string $role)
    {
        return redirect($this->getRedirectRoute($role));
    }

    public function showForgotPassword()
    {
        return view('auth.forgot_password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Guard: ensure MAIL_MAILER is configured before attempting to send
        if (empty(config('mail.default')) || empty(config('mail.mailers.' . config('mail.default') . '.transport'))) {
            return back()->withErrors(['email' => 'Password reset email is not configured. Please contact your administrator.']);
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Password reset link has been sent to your email.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(string $token)
    {
        return view('auth.reset_password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                AuditTrail::log('password_reset', 'auth', $user->id, [], [], "Password reset for {$user->name}");
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password has been reset successfully.')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
