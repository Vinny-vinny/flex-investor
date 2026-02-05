<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

trait DataTransfer
{
    protected function httpClient(array $options = []): Client
    {
        return new Client(array_merge([
            'timeout' => 15,
            'connect_timeout' => 10,
            'http_errors' => false, // handle errors manually
        ], $options));
    }

    protected function getRequest(
        string $url,
        array $query = [],
        array $headers = []
    ): array {
        try {
            $response = $this->httpClient()->get($url, [
                'query'   => $query,
                'headers' => $headers,
            ]);

            return $this->formatResponse($response);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    public function postRequest(
        string $url,
        array $payload = [],
        array $headers = []
    ): array {
        try {
            $response = $this->httpClient()->post($url, [
                'json'    => $payload,
                'headers' => $headers,
            ]);

            return $this->formatResponse($response);
        } catch (RequestException $e) {
            return $this->handleException($e);
        }
    }

    protected function formatResponse($response): array
    {
        $statusCode = $response->getStatusCode();
        $body       = (string) $response->getBody();

        return [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'status'  => $statusCode,
            'data'    => json_decode($body, true) ?? $body,
        ];
    }
    protected function handleException(RequestException $e): array
    {
        Log::error('External Service Error', [
            'message' => $e->getMessage(),
            'url'     => optional($e->getRequest())->getUri(),
        ]);

        if ($e->hasResponse()) {
            $response = $e->getResponse();

            return [
                'success' => false,
                'status'  => $response->getStatusCode(),
                'error'   => json_decode((string) $response->getBody(), true)
                    ?? (string) $response->getBody(),
            ];
        }

        return [
            'success' => false,
            'status'  => 500,
            'error'   => 'Unable to connect to external service',
        ];
    }
}
