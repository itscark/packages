<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\ApplicationToken;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\User' => 'App\Policies\UserPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::viaRequest('application-token', function (Request $request) {
            $applicationToken = ApplicationToken::query()
                ->where('token', $request->getPassword())
                ->first();

            if (!$applicationToken) {
                abort(401, 'License key invalid');
            }

            if (!$applicationToken->isValid()) {
                abort(401, 'This license is expired');
            }

            if (!$applicationToken->application->isActive) {
                abort(401, 'Application was suspended');
            }

            return $applicationToken;
        });
    }
}
