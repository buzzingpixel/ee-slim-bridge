<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\Slim;

use BuzzingPixel\SlimBridge\Config\Config;
use PHPUnit\Framework\TestCase;

class RetrieveAppCreatedCallbackTest extends TestCase
{
    /** @var mixed[] */
    private array $calls = [];

    /** @var callable|null */
    private mixed $getItemReturn = null;

    private RetrieveAppCreatedCallback $retrieveAppCreatedCallback;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->getItemReturn = null;

        $this->retrieveAppCreatedCallback = new RetrieveAppCreatedCallback(
            config: $this->mockConfig(),
        );
    }

    private function mockConfig(): Config
    {
        $mock = $this->createMock(Config::class);

        $mock->method('getItem')->willReturnCallback(
            function (string $item, string $index): mixed {
                $this->calls[] = [
                    'object' => 'Config',
                    'method' => 'getItem',
                    'item' => $item,
                    'index' => $index,
                ];

                return $this->getItemReturn;
            }
        );

        return $mock;
    }

    public function testWhenNoCallable(): void
    {
        self::assertIsCallable(
            $this->retrieveAppCreatedCallback->retrieve(),
        );

        self::assertSame(
            [
                [
                    'object' => 'Config',
                    'method' => 'getItem',
                    'item' => 'appCreatedCallback',
                    'index' => 'slimBridge',
                ],
            ],
            $this->calls,
        );
    }

    public function testWhenCallable(): void
    {
        $this->getItemReturn = static function (): string {
            return 'fooBarCallable';
        };

        self::assertSame(
            'fooBarCallable',
            $this->retrieveAppCreatedCallback->retrieve()(),
        );

        self::assertSame(
            [
                [
                    'object' => 'Config',
                    'method' => 'getItem',
                    'item' => 'appCreatedCallback',
                    'index' => 'slimBridge',
                ],
            ],
            $this->calls,
        );
    }
}
