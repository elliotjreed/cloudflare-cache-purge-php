<?php

declare(strict_types=1);

namespace ElliotJReed;

use ElliotJReed\Entity\Response;
use ElliotJReed\Entity\Result;
use ElliotJReed\Exception\Cloudflare;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;

class Cache
{
    /**
     * Cache constructor.
     *
     * @param \GuzzleHttp\ClientInterface $client A Guzzle HTTP client
     * @param string                      $token  The Cloudflare authorisation token
     */
    public function __construct(private readonly ClientInterface $client, private readonly string $token)
    {
    }

    /**
     * @param string   $zoneId      The Zone ID of the Cloudflare Zone / domain where the files are cached
     * @param string[] $urlsToPurge An array of full URLs to purge from the Cloudflare cache
     *
     * @throws \ElliotJReed\Exception\Cloudflare
     */
    public function purgeFiles(string $zoneId, array $urlsToPurge): Response
    {
        $chunked = \array_chunk(\array_values($urlsToPurge), 30);

        $response = new Response();
        foreach ($chunked as $files) {
            $response->addResults($this->sendRequest($zoneId, $files));
        }

        return $response;
    }

    private function sendRequest(string $zoneId, array $files): Result
    {
        try {
            $request = $this->client->request(
                'POST',
                'https://api.cloudflare.com/client/v4/zones/' . $zoneId . '/purge_cache',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'files' => $files
                    ]
                ]
            );

            $apiResponse = \json_decode($request->getBody()->getContents(), false, 16, \JSON_THROW_ON_ERROR);
        } catch (ClientException | \JsonException $exception) {
            throw new Cloudflare('Cloudflare Cache Purge API error', previous: $exception);
        }

        return (new Result())->setId($apiResponse?->result?->id ?? null);
    }
}
