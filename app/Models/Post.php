<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use Sluggable;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'published_at',
        'author_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function isDraft()
    {
        return is_null($this->published_at);
    }

    public function isPublished()
    {
        return !is_null($this->published_at) && $this->published_at->isPast();
    }

    public function isScheduled()
    {
        return !is_null($this->published_at) && $this->published_at->isFuture();
    }

    public function scopeDraft(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->whereNull('published_at');
    }

    public function scopePublished(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeScheduled(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '>=', now());
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
