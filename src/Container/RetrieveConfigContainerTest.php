<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\Container;

use BuzzingPixel\SlimBridge\Config\Config;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Throwable;

use function assert;

class RetrieveConfigContainerTest extends TestCase
{
    /** @var mixed[] */
    private array $calls = [];

    private ?ContainerInterface $getItemReturn = null;

    private RetrieveConfigContainer $retrieveConfigContainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->retrieveConfigContainer = new RetrieveConfigContainer(
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

    public function testWhenNoContainer(): void
    {
        $exception = null;

        try {
            $this->retrieveConfigContainer->retrieve();
        } catch (Throwable $e) {
            $exception = $e;
        }

        assert($exception instanceof RuntimeException);

        self::assertSame(
            'The config array must have a config item for ' .
                '`slimBridge` as an array with the key `containerInterface` ' .
                'that returns an  implementation of ' .
                ContainerInterface::class,
            $exception->getMessage(),
        );

        self::assertSame(
            [
                [
                    'object' => 'Config',
                    'method' => 'getItem',
                    'item' => 'containerInterface',
                    'index' => 'slimBridge',
                ],
            ],
            $this->calls,
        );
    }

    public function testWhenContainer(): void
    {
        $this->getItemReturn = $this->createMock(
            ContainerInterface::class,
        );

        self::assertSame(
            $this->getItemReturn,
            $this->retrieveConfigContainer->retrieve(),
        );

        self::assertSame(
            [
                [
                    'object' => 'Config',
                    'method' => 'getItem',
                    'item' => 'containerInterface',
                    'index' => 'slimBridge',
                ],
            ],
            $this->calls,
        );
    }
}
