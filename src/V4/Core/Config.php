<?php

declare(strict_types=1);

namespace PaySimple\V4\Core;

/**
 * Class Config
 * @package PaySimple\V4\Core
 */
class Config
{
    public const string PRODUCTION_URL = "https://api.paysimple.com/v4/'";
    public const string SANDBOX_URL = "https://sandbox-api.paysimple.com/v4/";
    public const array HTTP_ERRORS_CODES = [400, 401, 403, 404, 405, 500];
    public const array HTTP_SUCCESS_CODES = [200, 201, 204];

    public function __construct(
        private readonly string $user,
        private readonly string $token,
        private readonly bool $sandbox = false
    ) {
    }

    final public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * @return array<string, mixed>
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
