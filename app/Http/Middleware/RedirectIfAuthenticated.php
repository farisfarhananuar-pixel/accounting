<?php
// app/Http/Middleware/RedirectIfAuthenticated.php
namespace App\Http\Middleware;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class RedirectIfAuthenticated {
    public function handle(Request $request, Closure $next, string ...$guards): Response {
        $guards = empty($guards) ? [null] : $guards;
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                return redirect(match($user->role) {
                    'admin' => route('admin.dashboard'),
                    'manager' => route('manager.dashboard'),
                    'executive_accountant' => route('accountant.dashboard'),
                    'auditor' => route('auditor.dashboard'),
                    default => '/',
                });
            }
        }
        return $next($request);
    }
}
