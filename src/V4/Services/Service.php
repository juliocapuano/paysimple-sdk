<?php


namespace PaySimple\V4\Services;

use PaySimple\V4\ApiClient;

class Service
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
