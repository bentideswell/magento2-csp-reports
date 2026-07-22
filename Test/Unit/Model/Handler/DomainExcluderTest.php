<?php

namespace FishPig\CspReport\Test\Unit\Model\Handler;

use FishPig\CspReport\Model\Config;
use FishPig\CspReport\Model\Handler\DomainExcluder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DomainExcluderTest extends TestCase
{
    #[DataProvider('isExcludedDataProvider')]
    public function testIsExcluded(array $excludedDomains, string $blockedUri, bool $expected): void
    {
        $config = $this->createMock(Config::class);
        $config->method('getExcludedDomains')->willReturn($excludedDomains);

        $domainExcluder = new DomainExcluder($config);

        $this->assertSame($expected, $domainExcluder->isExcluded($blockedUri));
    }

    public static function isExcludedDataProvider(): array
    {
        return [
            'exact domain match' => [
                ['cdn.honey.io'],
                'https://cdn.honey.io/fonts/suisse-intl/700.ttf',
                true,
            ],
            'exact domain does not match different subdomain' => [
                ['cdn.honey.io'],
                'https://other.honey.io/fonts/suisse-intl/700.ttf',
                false,
            ],
            'wildcard matches subdomain' => [
                ['*.honey.io'],
                'https://cdn.honey.io/fonts/suisse-intl/700.ttf',
                true,
            ],
            'wildcard matches different subdomain' => [
                ['*.honey.io'],
                'https://assets.honey.io/img/logo.png',
                true,
            ],
            'wildcard does not match bare domain' => [
                ['*.honey.io'],
                'https://honey.io/img/logo.png',
                false,
            ],
            'wildcard does not match unrelated domain' => [
                ['*.honey.io'],
                'https://honey.io.evil.com/img/logo.png',
                false,
            ],
            'match is case-insensitive against pattern case' => [
                ['CDN.HONEY.IO'],
                'https://cdn.honey.io/fonts/suisse-intl/700.ttf',
                true,
            ],
            'match is case-insensitive against host case' => [
                ['cdn.honey.io'],
                'https://CDN.HONEY.IO/fonts/suisse-intl/700.ttf',
                true,
            ],
            'no configured domains excludes nothing' => [
                [],
                'https://cdn.honey.io/fonts/suisse-intl/700.ttf',
                false,
            ],
            'unmatched domain is not excluded' => [
                ['cdn.honey.io'],
                'https://example.com/script.js',
                false,
            ],
            'blocked uri with no host is not excluded' => [
                ['cdn.honey.io'],
                'self',
                false,
            ],
            'inline blocked uri is not excluded' => [
                ['*'],
                'inline',
                false,
            ],
            'wildcard-all matches every host' => [
                ['*'],
                'https://example.com/script.js',
                true,
            ],
        ];
    }
}
