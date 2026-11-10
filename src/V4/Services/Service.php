<?php

declare(strict_types=1);

namespace PaySimple\V4\Services;

use PaySimple\V4\Core\ApiClient;

abstract class Service
{
    public function __construct(protected ApiClient $client)
    {
    }
}
