<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Store extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $table = 'stores';

    protected $fillable = [
        'data',
        'settings',
    ];

    protected $casts = [
        // 'settings' => AsArrayObject::class,
        // 'settings' => 'array',
    ];

    public function name(): Attribute
    {
        return Attribute::get(fn () => $this->settings['site_name'] ?? null);
    }

    public function updateSettings(array $newSettings)
    {
        $this->settings = array_merge(
            $this->settings ?? [],
            $newSettings
        );
        return $this->save();
    }
}
