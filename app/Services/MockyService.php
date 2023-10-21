<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class MockyService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://run.mocky.io'
        ]);
    }

    public function authorizeTransaction(): array
    {
        $uri = '/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';
        try {
            $response = $this->client->request('GET', $uri);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            return ['message' => 'Não Autorizado'];
        }
    }

    public function notifyUser(string $fakeUserId): array
    {
        $uri = '/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';

        try {
            $response = $this->client->request($uri);

            return json_decode($response->getBody(), true);
        } catch (GuzzleException $e) {
            return ['error'];
        }
    }
}
