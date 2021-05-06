<?php

namespace PaySimple\V4;

class Config
{
    const PRODUCTION_URL     = "https://api.paysimple.com/v4/'";
    const SANDBOX_URL        = "https://sandbox-api.paysimple.com/v4/";
    const HTTP_ERRORS_CODES  = [400, 401, 403, 404, 405, 500];
    const HTTP_SUCCESS_CODES = [200, 201];


    /**
     * @var bool
     */
    private $sandbox;
    /**
     * @var string
     */
    private $user;
    /**
     * @var string
     */
    private $token;

    public function __construct(string $user, string $token, bool $sandbox = false)
    {
        $this->user    = $user;
        $this->token   = $token;
        $this->sandbox = $sandbox;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * @return array
     */
    public function generateConfig()
    {
        $timestamp = gmdate("c");
        $hmac      = base64_encode(hash_hmac("sha256", $timestamp, $this->token, true));

        return [
            'base_uri'    => $this->isSandbox() ? Config::SANDBOX_URL : Config::PRODUCTION_URL,
            'http_errors' => false,
            'headers'     => [
                'Authorization' => "PSSERVER accessid={$this->user}; timestamp={$timestamp}; signature={$hmac}",
            ]
        ];
    }
}
