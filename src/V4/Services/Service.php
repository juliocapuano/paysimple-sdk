<?php


namespace PaySimple\V4\Services;

use PaySimple\V4\Core\ApiClient;

abstract class Service
{

    /**
     * @var ApiClient
     */
    protected $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }
}
