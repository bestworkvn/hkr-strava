<?php

namespace App\Console\Commands;

use App\Services\StravaWebhookService;
use Illuminate\Console\Command;

class UnsubscribeStravaWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strava:unsubscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes a Strava webhook subscription';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        if (app(StravaWebhookService::class)->unsubscribe()) {
            $this->info("Successfully unsubscribed");
        } else {
            $this->warn('Error or no subscription found');
        }

        return 0;
    }
}
