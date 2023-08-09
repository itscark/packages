<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory, UUID;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function packages(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'application_package', 'application_id', 'package_id');
    }

    public function tokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApplicationToken::class);
    }

    /**
     * Scope a query to only include valid tokens.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('active', true);
    }

    public function getIsActiveAttribute()
    {
        return $this->active;
    }

    public function getRessourceUriKey(): string
    {
        return \App\Nova\Application::uriKey();
    }
}
