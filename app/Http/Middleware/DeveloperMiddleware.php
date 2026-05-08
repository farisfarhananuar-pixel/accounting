<?php
// app/Http/Middleware/DeveloperMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DeveloperMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('developer_logged_in')) {
            return redirect()->route('developer.login');
        }
        return $next($request);
    }
}
