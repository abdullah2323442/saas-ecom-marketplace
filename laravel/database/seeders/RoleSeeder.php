<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'platform_super_admin', 'scope' => 'platform', 'description' => 'Full platform access'],
            ['name' => 'support_agent', 'scope' => 'platform', 'description' => 'Platform support access'],
            ['name' => 'tenant_owner', 'scope' => 'tenant', 'description' => 'Tenant account owner'],
            ['name' => 'tenant_staff', 'scope' => 'tenant', 'description' => 'Tenant operations staff'],
            ['name' => 'customer', 'scope' => 'tenant', 'description' => 'Store customer'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['name' => $role['name'], 'scope' => $role['scope']],
                ['description' => $role['description']]
            );
        }
    }
}
