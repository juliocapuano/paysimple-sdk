<?php

namespace PaySimple\Tests\V4\Core;

use PaySimple\V4\Core\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testSandboxConfig(): void
    {
        $user = 'test_user';
        $token = 'test_token';
        $config = new Config($user, $token, true);

        $this->assertTrue($config->isSandbox());

        $generated = $config->generateConfig();
        $this->assertSame(Config::SANDBOX_URL, $generated['base_uri']);
        $this->assertFalse($generated['http_errors']);
        $this->assertArrayHasKey('headers', $generated);
        $this->assertArrayHasKey('Authorization', $generated['headers']);
        $this->assertStringStartsWith('PSSERVER accessid=test_user; timestamp=', $generated['headers']['Authorization']);
        $this->assertStringContainsString('; signature=', $generated['headers']['Authorization']);
    }

    public function testProductionConfig(): void
    {
        $user = 'live_user';
        $token = 'live_token';
        $config = new Config($user, $token, false);

        $this->assertFalse($config->isSandbox());

        $generated = $config->generateConfig();
        $this->assertSame(Config::PRODUCTION_URL, $generated['base_uri']);
        $this->assertFalse($generated['http_errors']);
        $this->assertArrayHasKey('Authorization', $generated['headers']);
        $this->assertStringStartsWith('PSSERVER accessid=live_user;', $generated['headers']['Authorization']);
    }
}
