<?php

namespace App\Services;

use GuzzleHttp\Client;

class FastApiService{
    protected $client;
    protected $baseUri;

    public Function __construct()
    {
        $this->baseUri = env('FASTAPI_URL', 'http://localhost:8000');
        $this->client = new Client([
            'base_uri' => $this->baseUri,
            'headers'  => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'verify' => false
        ]);
    }

    public function get($endpoint, $params = [])
    {
        try{
            $response = $this->client->get($endpoint, ['query' => $params]);
            return json_decode($response->getBody(), true);
        }catch(\GuzzleHttp\Exception\ConnectException $e){
            return ['error' => 'no se pudo conectar al servidor. Verifica tu conexion.'];
        }
    }

    public function post($endpoint, $data = [])
    {
        try{
            $response = $this->client->post($endpoint, ['json' => $data]);
            return json_decode($response->getBody(), true);
        }catch(\GuzzleHttp\Exception\ConnectException $e){
            return['error' => 'no se pudo conectar al servidor. Verifica tu conexion.'];
        }
    }
}