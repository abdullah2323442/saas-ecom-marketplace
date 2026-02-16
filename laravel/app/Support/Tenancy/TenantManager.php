<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class TenantManager
{
    protected ?Tenant $currentTenant = null;

    public function current(): ?Tenant
    {
        return $this->currentTenant;
    }

    public function setCurrent(Tenant $tenant): void
    {
        $this->currentTenant = $tenant;

        config([
            'database.connections.tenant.host' => $tenant->db_host,
            'database.connections.tenant.port' => $tenant->db_port,
            'database.connections.tenant.database' => $tenant->db_database,
            'database.connections.tenant.username' => $tenant->db_username,
            'database.connections.tenant.password' => $tenant->db_password,
        ]);

        DB::purge('tenant');
    }

    public function forgetCurrent(): void
    {
        $this->currentTenant = null;
        DB::disconnect('tenant');
    }
}
