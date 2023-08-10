<?php

namespace App\Http\Middleware;

use App\Models\ApplicationToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BearerToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            abort(401, 'License key invalid');
        }

        $applicationToken = ApplicationToken::query()
            ->where('token', $token)
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

        /*
         * Find the package that is being requested.
         */
        $packageTechnicalName = $this->getRequestedPackage($request);

        /*
         * Check if the application token has access to the requested package.
         */
        $packages = $applicationToken->application->packages;
        $packageMatch = $packages->contains(function ($package) use ($packageTechnicalName) {
            if ($package->technical_name === $packageTechnicalName) {
                $package->increment('download_count');
                return true;
            }

            return false;
        });

        abort_unless($packageMatch, 403, 'Package not authorized');

        return $next($request);
    }

    protected function getRequestedPackage(Request $request): string
    {
        $originalUrl = $request->fullUrl();
        preg_match('#/dist/(?<package>iwaves/[^/]*)/[^/]*$#', $originalUrl, $matches);

        if (!key_exists('package', $matches)) {
            abort(401, 'Missing package information');
        }

        return $matches['package'];
    }
}
