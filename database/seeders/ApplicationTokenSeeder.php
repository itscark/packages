<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\ApplicationToken;
use Illuminate\Database\Seeder;

class ApplicationTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Application::all()->each(function ($application) {
            $numberOfTokens = random_int(1, 3);

            for ($i = 0; $i < $numberOfTokens; $i++) {
                ApplicationToken::factory()->create([
                    'application_id' => $application->id,
                ]);
            }
        });
    }
}
