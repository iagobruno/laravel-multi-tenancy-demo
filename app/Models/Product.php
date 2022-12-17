<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany};

class Product extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image_url',
        'price',
        'compare_at_price',
        'cost',
        'has_variants',
        'shippable',
        'returnable',
        'sku',
        'barcode',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * The relationships that should always be loaded.
     */
    protected $with = [
        'variants',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function imageUrl(): Attribute
    {
        return Attribute::set(function ($value) {
            if (!str_starts_with($value, 'http')) return asset($value);
            else return $value;
        });
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
