<?php

namespace App\Http\Middleware;

use App\Models\TenantDomain;
use App\Support\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InitializeTenancyByDomain
{
    public function __construct(protected TenantManager $tenantManager)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $host = strtolower($request->getHost());
        $centralDomains = array_map('strtolower', config('tenancy.central_domains', []));

        if (in_array($host, $centralDomains, true)) {
            throw new NotFoundHttpException('Tenant routes are unavailable on central domains.');
        }

        $tenantDomain = TenantDomain::query()
            ->with('tenant')
            ->where('domain', $host)
            ->first();

        if (! $tenantDomain?->tenant) {
            throw new NotFoundHttpException('Tenant not found for this domain.');
        }

        $this->tenantManager->setCurrent($tenantDomain->tenant);

        try {
            return $next($request);
        } finally {
            $this->tenantManager->forgetCurrent();
        }
    }
}
