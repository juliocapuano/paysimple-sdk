<?php

namespace PaySimple\V4\Core;

/**
 * Class Config
 * @package PaySimple\V4\Core
 */
class Config
{
    public const PRODUCTION_URL = "https://api.paysimple.com/v4/'";
    public const SANDBOX_URL = "https://sandbox-api.paysimple.com/v4/";
    public const HTTP_ERRORS_CODES = [400, 401, 403, 404, 405, 500];
    public const HTTP_SUCCESS_CODES = [200, 201, 204];


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

    final public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * @return array
     */
    final public function generateConfig(): array
    {
        $timestamp = gmdate("c");
        $hmac      = base64_encode(hash_hmac("sha256", $timestamp, $this->token, true));

        return [
            'base_uri'    => $this->isSandbox() ? self::SANDBOX_URL : self::PRODUCTION_URL,
            'http_errors' => false,
            'headers'     => [
                'Authorization' => sprintf("PSSERVER accessid=%s; timestamp=%s; signature=%s", $this->user, $timestamp, $hmac),
            ]
        ];
    }
}
