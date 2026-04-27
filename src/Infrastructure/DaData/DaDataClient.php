<?php

declare(strict_types=1);

namespace App\Infrastructure\DaData;

use App\Infrastructure\DaData\Exception\DaDataUnavailableException;
use App\Infrastructure\DaData\Exception\UnexpectedDaDataResponseException;
use JsonException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class DaDataClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $baseUri,
        private string $apiKey,
        private float $timeoutSeconds,
        private float $maxDurationSeconds
    ) {
    }

    public function findCompanyByInn(string $inn): ?array
    {
        $payload = [
            'query' => $inn,
            'count' => 1,
            'type' => strlen($inn) === 10 ? 'LEGAL' : 'INDIVIDUAL',
        ];

        if (strlen($inn) === 10) {
            $payload['branch_type'] = 'MAIN';
        }

        try {
            // Fail fast on upstream latency; no automatic retries for this synchronous lookup endpoint.
            $response = $this->httpClient->request(
                'POST',
                rtrim($this->baseUri, '/') . '/findById/party',
                [
                    'timeout' => $this->timeoutSeconds,
                    'max_duration' => $this->maxDurationSeconds,
                    'headers' => [
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Token ' . $this->apiKey,
                    ],
                    'json' => $payload,
                ]
            );

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode >= 300) {
                throw DaDataUnavailableException::becauseOfStatusCode($statusCode);
            }

            $content = $response->getContent(false);
            $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (TransportExceptionInterface $exception) {
            throw DaDataUnavailableException::fromThrowable($exception);
        } catch (JsonException $exception) {
            throw UnexpectedDaDataResponseException::fromThrowable($exception);
        }

        if (!is_array($decoded) || !isset($decoded['suggestions'])
            || !is_array(
                $decoded['suggestions']
            )) {
            throw UnexpectedDaDataResponseException::becausePayloadIsInvalid();
        }

        if ([] === $decoded['suggestions']) {
            return null;
        }

        $suggestion = $decoded['suggestions'][0] ?? null;

        if (!is_array($suggestion)) {
            throw UnexpectedDaDataResponseException::becausePayloadIsInvalid();
        }

        $data = $suggestion['data'] ?? null;

        if (
            !isset($suggestion['value'])
            || !is_array($data)
            || !isset($data['state'])
            || !is_array($data['state'])
            || !array_key_exists('status', $data['state'])
        ) {
            throw UnexpectedDaDataResponseException::becausePayloadIsInvalid();
        }

        if (strlen($inn) === 10 && ($data['branch_type'] ?? null) !== 'MAIN') {
            throw UnexpectedDaDataResponseException::becausePayloadIsInvalid();
        }

        return $suggestion;
    }
}
