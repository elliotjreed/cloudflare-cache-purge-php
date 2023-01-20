<?php

declare(strict_types=1);

namespace ElliotJReed\Exception;

use Psr\Http\Client\ClientExceptionInterface;

class Cloudflare extends \Exception implements ClientExceptionInterface
{
    protected $message = 'Cloudflare API error.';
}
