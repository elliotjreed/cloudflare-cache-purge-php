<?php

declare(strict_types=1);

namespace Tests\ElliotJReed;

use ElliotJReed\Cache;
use ElliotJReed\Entity\Response as ResponseEntity;
use ElliotJReed\Entity\Result;
use ElliotJReed\Exception\Cloudflare;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    public function testItReturnsSingleEntryResponseArray(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{
              "success": true,
              "errors": [],
              "messages": [],
              "result": {
                "id": "9a7806061c88ada191ed06f989cc3dac"
              }
            }')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $cache = new Cache($client, 'a-secret-clouflare-token');

        $this->assertEquals(
            (new ResponseEntity())->addResults((new Result())->setId('9a7806061c88ada191ed06f989cc3dac')),
            $cache->purgeFiles('zone-id', ['file1.txt', 'file2.txt'])
        );

        $headers = $mock->getLastRequest()->getHeaders();
        $this->assertSame(['application/json'], $headers['Content-Type']);
        $this->assertSame(['Bearer a-secret-clouflare-token'], $headers['Authorization']);
    }

    public function testItReturnsTwoEntriesResponseArrayWhenMoreThanThirtyFilesInRequest(): void
    {
        $mock = new MockHandler([
            new Response(200, [], '{
              "success": true,
              "errors": [],
              "messages": [],
              "result": {
                "id": "id-1"
              }
            }'),
            new Response(200, [], '{
              "success": true,
              "errors": [],
              "messages": [],
              "result": {
                "id": "id-2"
              }
            }')
        ]);

        $client = new Client(['handler' => HandlerStack::create($mock)]);

        $cache = new Cache($client, 'a-secret-clouflare-token');

        $response = $cache->purgeFiles('zone-id', [
            '01.txt',
            '02.txt',
            '03.txt',
            '04.txt',
            '05.txt',
            '06.txt',
            '07.txt',
            '08.txt',
            '09.txt',
            '10.txt',
            '11.txt',
            '12.txt',
            '13.txt',
            '14.txt',
            '15.txt',
            '16.txt',
            '17.txt',
            '18.txt',
            '19.txt',
            '20.txt',
            '21.txt',
            '22.txt',
            '23.txt',
            '24.txt',
            '25.txt',
            '26.txt',
            '27.txt',
            '28.txt',
            '29.txt',
            '30.txt',
            '31.txt',
            '32.txt'
        ]);
        $this->assertEquals(
            (new ResponseEntity())->addResults((new Result())->setId('id-1'), (new Result())->setId('id-2')),
            $response
        );

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
        $this->expectExceptionMessage('Cloudflare Cache Purge API error');

        (new Cache($client, 'a-secret-clouflare-token'))->purgeFiles('zone-id', ['file.txt']);
    }
}
