<?php

namespace App\Http\Controllers;

use App\Models\ApplicationToken;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SatisAuthenticationController extends Controller
{
    public function __invoke(Authenticatable $applicationToken, Request $request): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        if (!$applicationToken instanceof ApplicationToken) {
            abort(401);
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

        return response('valid', 200);
    }


    protected function getRequestedPackage(Request $request): string
    {
        $originalUrl = $request->header('X-Original-URI', '');
        preg_match('#(?:https?://[^/]+)?/dist/(?<package>iwaves/[^/]*)/#', $originalUrl, $matches);

        if (!key_exists('package', $matches)) {
            abort(401, 'Missing X-Original-URI header');
        }

        return $matches['package'];
    }
}
