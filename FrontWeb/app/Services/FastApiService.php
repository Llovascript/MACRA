<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class FastApiService
{
    private $client;
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('API_BASE_URL', 'http://localhost:8000');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => config('app.api_timeout', 30),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => false
        ]);
    }

    public function register(array $userData)
    {
        try {
            $response = $this->client->post('/auth/register', [
                'json' => $userData
            ]);

            return [
                'success' => true,
                'data' => json_decode($response->getBody(), true),
                'status' => $response->getStatusCode()
            ];
        } catch (RequestException $e) {
            Log::error('API Register Error: ' . $e->getMessage());
            
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
            $errorBody = $e->getResponse() ? json_decode($e->getResponse()->getBody(), true) : [];
            
            return [
                'success' => false,
                'error' => $errorBody['detail'] ?? 'Error en el registro',
                'status' => $statusCode
            ];
        }
    }

    public function login(string $email, string $password)
    {
        try {
            $response = $this->client->post('/auth/login', [
                'json' => [
                    'correo' => $email,
                    'contraseña' => $password
                ]
            ]);

            return [
                'success' => true,
                'data' => json_decode($response->getBody(), true),
                'status' => $response->getStatusCode()
            ];
        } catch (RequestException $e) {
            Log::error('API Login Error: ' . $e->getMessage());
            
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 500;
            $errorBody = $e->getResponse() ? json_decode($e->getResponse()->getBody(), true) : [];
            
            return [
                'success' => false,
                'error' => $errorBody['detail'] ?? 'Error en el login',
                'status' => $statusCode
            ];
        }
    }

    public function getUser(string $token)
    {
        try {
            $response = $this->client->get('/auth/me', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            return [
                'success' => true,
                'data' => json_decode($response->getBody(), true),
                'status' => $response->getStatusCode()
            ];
        } catch (RequestException $e) {
            Log::error('API Get User Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Error al obtener usuario',
                'status' => $e->getResponse() ? $e->getResponse()->getStatusCode() : 500
            ];
        }
    }

    public function makeAuthenticatedRequest(string $method, string $endpoint, array $data = [], string $token = null)
    {
        try {
            $options = [
                'headers' => []
            ];

            if ($token) {
                $options['headers']['Authorization'] = 'Bearer ' . $token;
            }

            if (!empty($data)) {
                $options['json'] = $data;
            }

            $response = $this->client->request($method, $endpoint, $options);

            return [
                'success' => true,
                'data' => json_decode($response->getBody(), true),
                'status' => $response->getStatusCode()
            ];
        } catch (RequestException $e) {
            Log::error('API Request Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Error en la petición',
                'status' => $e->getResponse() ? $e->getResponse()->getStatusCode() : 500
            ];
        }
    }
}