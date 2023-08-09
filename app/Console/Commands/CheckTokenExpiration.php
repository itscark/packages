<?php

namespace App\Console\Commands;

use App\Models\ApplicationToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Laravel\Nova\Notifications\NovaNotification;

class CheckTokenExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-token-expiration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if any tokens are expiring in the next 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expirationDate = Carbon::now()->addDays(30);

        $tokens = ApplicationToken::where('expires_at', '<', $expirationDate)
            ->where('expires_at', '>=', Carbon::now())
            ->get();

        $users = User::all();

        foreach ($tokens as $token) {
            $daysUntilExpiration = Carbon::now()->diffInDays($token->expires_at);

            foreach ($users as $user) {
                $user->notify(
                    NovaNotification::make()
                        ->message('A token for the application "' . $token->application->name . '" is expiring in ' . $daysUntilExpiration . ' days.')
                        ->action('Renew', '/resources/' . $token->getRessourceUriKey() . '/' . $token->getKey())
                        ->icon('refresh')
                        ->type('warning')
                );
            }
        }

        $this->info('Notifications sent for tokens expiring soon.');
    }
}
