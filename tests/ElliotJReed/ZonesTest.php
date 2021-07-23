<?php

declare(strict_types=1);

namespace Tests\ElliotJReed;

use ElliotJReed\Exception\Cloudflare;
use ElliotJReed\Zones;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class ZonesTest extends TestCase
{
    public function testItReturnsResponse(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{
              "success": true,
              "errors": [],
              "messages": [],
              "result": [{
                "id": "9a7806061c88ada191ed06f989cc3dac",
                "name": "https://www.example.com/image1.png"
              }]
            }')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $zones = (new Zones($client, 'a-secret-clouflare-token'))->get();

        $results = $zones->getResults();
        $this->assertSame('9a7806061c88ada191ed06f989cc3dac', $results[0]->getId());
        $this->assertSame('https://www.example.com/image1.png', $results[0]->getName());

        $this->assertTrue($zones->hasResults());

        $headers = $mock->getLastRequest()->getHeaders();
        $this->assertSame(['application/json'], $headers['Content-Type']);
        $this->assertSame(['Bearer a-secret-clouflare-token'], $headers['Authorization']);
    }

    public function testItReturnsResponseWithNullNameWhenNameNotReturnedByApi(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{
              "success": true,
              "errors": [],
              "messages": [],
              "result": [{
                "id": "9a7806061c88ada191ed06f989cc3dac"
              }]
            }')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $zones = (new Zones($client, 'a-secret-clouflare-token'))->get();

        $results = $zones->getResults();
        $this->assertSame('9a7806061c88ada191ed06f989cc3dac', $results[0]->getId());
        $this->assertNull($results[0]->getName());
        $this->assertTrue($zones->hasResults());

        $headers = $mock->getLastRequest()->getHeaders();
        $this->assertSame(['application/json'], $headers['Content-Type']);
        $this->assertSame(['Bearer a-secret-clouflare-token'], $headers['Authorization']);
    }

    public function testItReturnsResponseWithNullIdWhenIdNotReturnedByApi(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{
              "success": true,
              "errors": [],
              "messages": [],
              "result": [{
                "name": "https://www.example.com/image1.png"
              }]
            }')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $zones = (new Zones($client, 'a-secret-clouflare-token'))->get();

        $results = $zones->getResults();
        $this->assertNull($results[0]->getId());
        $this->assertSame('https://www.example.com/image1.png', $results[0]->getName());
        $this->assertTrue($zones->hasResults());

        $headers = $mock->getLastRequest()->getHeaders();
        $this->assertSame(['application/json'], $headers['Content-Type']);
        $this->assertSame(['Bearer a-secret-clouflare-token'], $headers['Authorization']);
    }

    public function testItReturnsEmptyResponseWhenDetailsNotReturnedByApi(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{
              "success": true,
              "errors": [],
              "messages": [],
              "result": []
            }')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $zones = (new Zones($client, 'a-secret-clouflare-token'))->get();

        $this->assertSame([], $zones->getResults());
        $this->assertFalse($zones->hasResults());

        $headers = $mock->getLastRequest()->getHeaders();
        $this->assertSame(['application/json'], $headers['Content-Type']);
        $this->assertSame(['Bearer a-secret-clouflare-token'], $headers['Authorization']);
    }

    public function testItThrowsExceptionWhenRequestIsUnsuccessful(): void
    {
        $mock = new MockHandler([
            new Response(403, [])
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $this->expectException(Cloudflare::class);
        $this->expectExceptionMessage('Cloudflare Zone List API error');

        (new Zones($client, 'a-secret-clouflare-token'))->get();
    }
}
