<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }

    protected static function bootBelongsToTenant()
    {
        static::creating(function (Model $model) {
            if (
                !is_null(tenant()) &&
                !$model->isDirty('tenant_id')
            ) {
                $model->forceFill(['tenant_id' => tenant()->id]);
            }
        });

        static::addGlobalScope('tenant', function (Builder $query) {
            $query->whereBelongsTo(tenant());
        });
    }
}
