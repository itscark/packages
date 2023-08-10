<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SatisController extends Controller
{
    public function index(): \Illuminate\Foundation\Application|Response|Application|ResponseFactory
    {
        $html = file_get_contents(resource_path('views/satis/satis.html'));

        return response($html, 200)
            ->header('Content-Type', 'text/html');
    }

    public function packagesJson(): BinaryFileResponse
    {
        $path = storage_path('app/satis/packages.json');

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function dist($path): BinaryFileResponse
    {
        $filePath = storage_path("app/satis/dist/{$path}");

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath);
    }

    public function serveFile($prefix, $path): BinaryFileResponse
    {
        $filePath = storage_path("app/satis/{$prefix}/{$path}");

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->file($filePath);
    }
}
