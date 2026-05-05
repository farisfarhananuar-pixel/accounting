<?php
// app/Http/Middleware/CompanyScopeMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures all queries are scoped to the authenticated user's company.
 * This is the core of multi-tenancy in Account Easy.
 */
class CompanyScopeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            // Store company_id in the session for global access
            session(['current_company_id' => auth()->user()->company_id]);
        }

        return $next($request);
    }
}
