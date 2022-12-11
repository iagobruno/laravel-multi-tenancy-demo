<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Store extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $table = 'stores';

    protected $fillable = [
        'name',
        'data',
        'settings',
    ];

    protected $casts = [
        // 'settings' => AsArrayObject::class,
    ];

    public function updateSettings(array $newSettings)
    {
        $this->settings = array_merge(
            $this->settings ?? [],
            $newSettings
        );
        return $this->save();
    }
}
