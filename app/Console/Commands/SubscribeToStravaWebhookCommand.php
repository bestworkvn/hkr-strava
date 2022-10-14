<?php

namespace App\Console\Commands;

use App\Services\StravaWebhookService;
use Illuminate\Console\Command;

class SubscribeToStravaWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strava:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribes to a Strava webhook';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $id = app(StravaWebhookService::class)->subscribe();
        if ($id) {
            $this->info("Successfully subscribed ID: {$id}");
        } else {
            $this->warn('Unable to subscribe');
        }

        return 0;
    }
}
