<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if ($user->is_platform_admin) {
            return $next($request);
        }

        $tenantId = $this->tenantManager->current()?->id;

        if (! $user->hasRole($role, $tenantId) && ! $user->hasRole($role)) {
            abort(403);
        }

        return $next($request);
    }
}
