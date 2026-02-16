<?php

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Support\Tenancy\TenantManager;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('tenant:create {company} {domain} {database} {username} {password} {--host=127.0.0.1} {--port=3306}', function () {
    $company = (string) $this->argument('company');
    $domain = strtolower((string) $this->argument('domain'));
    $database = (string) $this->argument('database');
    $username = (string) $this->argument('username');
    $password = (string) $this->argument('password');
    $host = (string) $this->option('host');
    $port = (int) $this->option('port');

    if (TenantDomain::query()->where('domain', $domain)->exists()) {
        $this->error('Domain already registered.');

        return self::FAILURE;
    }

    $slug = Str::slug($company);

    $tenant = DB::transaction(function () use ($company, $slug, $domain, $database, $username, $password, $host, $port) {
        $tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'company_name' => $company,
            'slug' => $slug,
            'status' => 'active',
            'primary_domain' => $domain,
            'db_host' => $host,
            'db_port' => $port,
            'db_database' => $database,
            'db_username' => $username,
            'db_password' => $password,
            'settings' => [],
        ]);

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'domain' => $domain,
            'is_primary' => true,
        ]);

        return $tenant;
    });

    $this->info("Tenant created. ID: {$tenant->id}, UUID: {$tenant->uuid}");

    return self::SUCCESS;
})->purpose('Provision a tenant and bind its primary domain');

Artisan::command('tenant:migrate {tenant_id}', function (TenantManager $tenantManager) {
    $tenantId = (int) $this->argument('tenant_id');
    $tenant = Tenant::query()->find($tenantId);

    if (! $tenant) {
        $this->error('Tenant not found.');

        return self::FAILURE;
    }

    $tenantManager->setCurrent($tenant);

    try {
        $code = Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant',
            '--force' => true,
        ]);

        $this->line(Artisan::output());

        return $code === 0 ? self::SUCCESS : self::FAILURE;
    } finally {
        $tenantManager->forgetCurrent();
    }
})->purpose('Run tenant-specific migrations for one tenant');
