<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'company_name',
        'slug',
        'status',
        'primary_domain',
        'db_host',
        'db_port',
        'db_database',
        'db_username',
        'db_password',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'db_password' => 'encrypted',
        ];
    }

    public function domains(): HasMany
    {
        return $this->hasMany(TenantDomain::class);
    }
}
