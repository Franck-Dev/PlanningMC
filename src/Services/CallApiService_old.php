<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService_old
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getPolymDatas(): array
    {
        //dd($this->client);
        $response = $this->client->request(
            'GET',
            'http://localhost:82/api/moldings'
        );

        return $response->toArray();
    }
}