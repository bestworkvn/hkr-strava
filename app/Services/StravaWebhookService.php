<?php
// app/Services/StravaWebhookService.php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StravaWebhookService
{
    private string $url;
    private string $client_id;
    private string $client_secret;
    private string $callback_url;
    private string $verify_token;

    public function __construct()
    {
        $this->url = config('services.strava.push_subscriptions_url');
        $this->client_id = config('ct_strava.client_id');
        $this->client_secret = config('ct_strava.client_secret');
        $this->callback_url = config('services.strava.webhook_callback_url');
        $this->verify_token = config('services.strava.webhook_verify_token');
    }

    public function subscribe(): int|null
    {
        $response = Http::post($this->url, [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'callback_url' => $this->callback_url,
            'verify_token' => $this->verify_token,
        ]);

        if ($response->status() === Response::HTTP_CREATED) {
            return json_decode($response->body())->id;
        }

        Log::channel('strava')->error(json_encode($response->body()), [$response->status()]);

        return null;
    }

    public function unsubscribe(): bool
    {
        $id = app(StravaWebhookService::class)->view(); // use the singleton

        if (!$id) {
            return false;
        }

        $response = Http::delete("$this->url/$id", [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
        ]);

        if ($response->status() === Response::HTTP_NO_CONTENT) {
            return true;
        }

        Log::channel('strava')->error(json_encode($response->body()), [$response->status()]);

        return false;
    }

    public function view(): int|null
    {
        $response = Http::get($this->url, [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
        ]);

        if ($response->status() === Response::HTTP_OK) {
            $body = json_decode($response->body());

            if ($body) {
                return $body[0]->id; // each application can have only 1 subscription
            } else {
                return null; // no subscription found
            }
        }

        Log::channel('strava')->error(json_encode($response->body()), [$response->status()]);

        return null;
    }

    // GET https://mycallbackurl.com?hub.verify_token=STRAVA&hub.challenge=15f7d1a91c1f40f8a748fd134752feb3&hub.mode=subscribe
    public function validate(string $mode, string $token, string $challenge): Response|JsonResponse
    {
        // Checks if a token and mode is in the query string of the request
        if ($mode && $token) {
            // Verifies that the mode and token sent are valid
            if ($mode === 'subscribe' && $token === $this->verify_token) {
                // Responds with the challenge token from the request
                return response()->json(['hub.challenge' => $challenge]);
            } else {
                // Responds with '403 Forbidden' if verify tokens do not match
                return response('', Response::HTTP_FORBIDDEN);
            }
        }

        return response('', Response::HTTP_FORBIDDEN);
    }
}
