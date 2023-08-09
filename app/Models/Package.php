<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory, UUID;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($package) {
            $applicationIds = $package->applications->pluck('name');
            if ($applicationIds->isNotEmpty()) {
                $message = 'Cannot delete package that is assigned to the following applications: ' . $applicationIds->implode(', ');
                throw new \Exception($message);
            }
        });

        static::creating(function ($model) {
            if ($model->getKey() === null) {
                $model->setAttribute($model->getKeyName(), Str::uuid()->toString());
            }
        });
    }

    public function applications(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'application_package', 'package_id', 'application_id');
    }
}
