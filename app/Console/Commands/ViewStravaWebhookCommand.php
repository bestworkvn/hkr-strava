<?php

namespace App\Console\Commands;

use App\Services\StravaWebhookService;
use Illuminate\Console\Command;

class ViewStravaWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strava:view-subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Views a Strava webhook subscription';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = app(StravaWebhookService::class)->view();

        if ($id) {
            $this->info("Subscription ID: $id");
        } else {
            $this->warn('Error or no subscription found');
        }

        return 0;
    }
}
