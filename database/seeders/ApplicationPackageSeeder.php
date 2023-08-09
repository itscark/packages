<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Package;
use Illuminate\Database\Seeder;

class ApplicationPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 applications with their packages
        Application::factory(10)->create()->each(function ($application) {
            // For each application, create 5 packages
            $packages = Package::factory(15)->create();

            // Attach these packages to the application
            $application->packages()->attach($packages);
        });

    }
}
