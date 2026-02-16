<?php

use App\Support\Tenancy\TenantManager;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'tenant'])->group(function (): void {
    Route::get('/store', function (TenantManager $tenantManager) {
        $tenant = $tenantManager->current();

        return response()->json([
            'tenant' => $tenant?->only(['id', 'uuid', 'company_name', 'slug', 'primary_domain']),
            'message' => 'Tenant storefront is active.',
        ]);
    })->name('tenant.storefront');
});
