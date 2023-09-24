<?php

namespace App\Models;

use App\Traits\UUID;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApplicationToken extends Model implements Authenticatable
{
    use HasFactory, UUID, \Illuminate\Auth\Authenticatable;

    protected $fillable = [
        'token',
        'expires_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->getKey() === null) {
                $model->setAttribute($model->getKeyName(), Str::uuid()->toString());
            }

            if (empty($model->token)) {
                $model->token = self::generateToken();
            }

            if (empty($model->universal)) {
                $model->universal = false;
            }
        });
    }

    public function application(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public static function generateToken(): string
    {
        return hash('sha256', Str::random(60));
    }

    /**
     * Scope a query to only include valid tokens.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsValid(Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where(function ($query) {
            $query->where('expires_at', '>=', Carbon::now())
                ->orWhereNull('expires_at')
                ->orWhere('universal', true);
        });
    }

    public function getRessourceUriKey(): string
    {
        return \App\Nova\ApplicationToken::uriKey();
    }
}
