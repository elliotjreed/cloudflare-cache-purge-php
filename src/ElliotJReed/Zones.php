<?php

declare(strict_types=1);

namespace ElliotJReed;

use ElliotJReed\Entity\Response;
use ElliotJReed\Entity\Result;
use ElliotJReed\Exception\Cloudflare;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class Zones
{
    /**
     * Zones constructor.
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @param string                      $token
     */
    public function __construct(private ClientInterface $client, private string $token)
    {
    }

    /**
     * @throws \ElliotJReed\Exception\Cloudflare
     */
    public function get(): Response
    {
        try {
            $request = $this->client->request('GET', 'https://api.cloudflare.com/client/v4/zones', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json'
                ]
            ]);

            $apiResponse = \json_decode($request->getBody()->getContents(), false, 16, \JSON_THROW_ON_ERROR);
        } catch (GuzzleException | \JsonException $exception) {
            throw new Cloudflare('Cloudflare Zone List API error', previous: $exception);
        }

        $response = new Response();
        if (isset($apiResponse->result)) {
            foreach ($apiResponse->result as $item) {
                $response->addResults((new Result())->setId($item->id ?? null)->setName($item->name ?? null));
            }
        }

        return $response;
    }
}
