<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\BuildSatisPackage;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class WebhookController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(string $package)
    {
        $package = $this->getPackage($package);
        BuildSatisPackage::dispatch($package->url);

        return response()->json([
            'status' => 'Package build started',
        ]);
    }

    protected function getPackage(string $partialName): Package
    {
        $package = Package::query()->where('technical_name', 'like', '%' . $partialName . '%')->first();

        if (!$package instanceof Package) {
            abort(404, 'Package not found');
        }

        return $package;
    }
}
