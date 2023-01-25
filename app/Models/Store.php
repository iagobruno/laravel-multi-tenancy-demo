<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};
use Stancl\Tenancy\Database\Concerns\{HasDatabase, CentralConnection, HasDomains};

class Store extends BaseTenant implements TenantWithDatabase
{
    use CentralConnection;
    use HasDatabase;
    use HasDomains;

    protected $table = 'stores';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public static function getCustomColumns(): array
    {
        return ['id', 'owner_id'];
    }

    protected $casts = [
        // 'settings' => AsArrayObject::class,
        // 'settings' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function name(): Attribute
    {
        return Attribute::get(fn () => $this->settings['site_name'] ?? null);
    }

    public function subdomain(): Attribute
    {
        return Attribute::get(
            fn () => $this->domains()->firstWhere('domain', 'LIKE', ('%' . config('app.short_url')))->domain
        );
    }

    public function updateSettings(array $newSettings)
    {
        $this->settings = array_merge(
            $this->settings ?? [],
            $newSettings
        );
        return $this->save();
    }

    public function checkBelongsTo(User $model): bool
    {
        return $this->owner_id === $model->id && $model instanceof User;
    }
}
