<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'subdomain',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [];

    public function subdomain(): Attribute
    {
        return Attribute::set(fn ($value) => strtolower($value));
    }

    /**
     * Get the route key for the model.
     * @see https://laravel.com/docs/9.x/routing#explicit-binding
     */
    public function getRouteKeyName()
    {
        return 'subdomain';
    }
}
