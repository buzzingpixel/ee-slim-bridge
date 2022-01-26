<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\Config;

use EE_Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /** @var mixed[] */
    private array $calls = [];

    /** @var mixed[] */
    private array $configItems = [
        'foo' => 'bar',
        'bool1' => 'y',
        'bool2' => 'yes',
        'bool3' => '1',
        'bool4' => 1,
        'bool5' => 'true',
        'bool6' => true,
        'bool7' => 'n',
        'bool8' => 'no',
        'bool9' => '0',
        'bool10' => 0,
        'bool11' => 'false',
        'bool12' => false,
    ];

    private Config $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new Config(eeConfig: $this->mockEeConfig());
    }

    private function mockEeConfig(): EE_Config
    {
        $mock = $this->createMock(EE_Config::class);

        $mock->method('item')->willReturnCallback(
            function (string $item, string $index): mixed {
                $this->calls[] = [
                    'object' => 'EE_Config',
                    'method' => 'item',
                    'item' => $item,
                    'index' => $index,
                ];

                return $this->configItems[$item] ?? null;
            }
        );

        return $mock;
    }

    public function testGetItem(): void
    {
        self::assertNull($this->config->getItem(
            item: 'baz',
            index: 'bar',
        ));

        self::assertSame(
            'bar',
            $this->config->getItem(item: 'foo'),
        );

        self::assertSame(
            [
                [
                    'object' => 'EE_Config',
                    'method' => 'item',
                    'item' => 'baz',
                    'index' => 'bar',
                ],
                [
                    'object' => 'EE_Config',
                    'method' => 'item',
                    'item' => 'foo',
                    'index' => '',
                ],
            ],
            $this->calls,
        );
    }

    public function testGetBoolean(): void
    {
        self::assertFalse($this->config->getBoolean('baz'));
        self::assertTrue($this->config->getBoolean('bool1'));
        self::assertTrue($this->config->getBoolean('bool2'));
        self::assertTrue($this->config->getBoolean('bool3'));
        self::assertTrue($this->config->getBoolean('bool4'));
        self::assertTrue($this->config->getBoolean('bool5'));
        self::assertTrue($this->config->getBoolean('bool6'));
        self::assertFalse($this->config->getBoolean('bool7'));
        self::assertFalse($this->config->getBoolean('bool8'));
        self::assertFalse($this->config->getBoolean('bool9'));
        self::assertFalse($this->config->getBoolean('bool10'));
        self::assertFalse($this->config->getBoolean('bool11'));
        self::assertFalse($this->config->getBoolean('bool12'));
    }
}
