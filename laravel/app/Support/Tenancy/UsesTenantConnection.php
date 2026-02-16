<?php

namespace App\Support\Tenancy;

trait UsesTenantConnection
{
    public function getConnectionName(): string
    {
        return 'tenant';
    }
}
