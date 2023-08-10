<?php

namespace App\Console\Commands\Satis;

use App\Models\Package;
use Illuminate\Console\Command;

abstract class AbstractSatisCommand extends Command
{
    protected function getConfig(): array
    {
        $config = [
            'name' => config('satis.name'),
            'homepage' => config('satis.homepage'),
            'output-dir' => config('satis.output_dir'),
            'repositories' => [],
            'archive' => [
                'format' => config('satis.archive.format'),
                'directory' => config('satis.archive.directory'),
                'skip-dev' => config('satis.archive.skip_dev'),
            ],
            'require-all' => config('satis.require_all'),
        ];

        $packages = Package::all();
        foreach ($packages as $package) {
            $config['repositories'][] = [
                'type' => 'vcs',
                'url' => $package->url,
            ];
        }

        return $config;
    }
}
