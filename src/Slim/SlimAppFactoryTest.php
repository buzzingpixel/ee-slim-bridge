<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\Slim;

use BuzzingPixel\SlimBridge\Container\RetrieveConfigContainer;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;

class SlimAppFactoryTest extends TestCase
{
    /** @var mixed[] */
    private array $calls = [];

    private ResponseFactoryInterface $responseFactory;

    private ContainerInterface $container;

    private SlimAppFactory $slimAppFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->responseFactory = $this->createMock(
            ResponseFactoryInterface::class,
        );

        $this->container = $this->createMock(
            ContainerInterface::class,
        );

        $this->slimAppFactory = new SlimAppFactory(
            responseFactory: $this->responseFactory,
            retrieveConfigContainer: $this->mockRetrieveConfigContainer(),
            retrieveAppCreatedCallback: $this->mockRetrieveAppCreatedCallback(),
        );
    }

    private function mockRetrieveConfigContainer(): RetrieveConfigContainer
    {
        $mock = $this->createMock(
            RetrieveConfigContainer::class,
        );

        $mock->method('retrieve')->willReturn(
            $this->container,
        );

        return $mock;
    }

    private function mockRetrieveAppCreatedCallback(): RetrieveAppCreatedCallback
    {
        $mock = $this->createMock(
            RetrieveAppCreatedCallback::class,
        );

        $mock->method('retrieve')->willReturn(
            function (App $app): void {
                $this->calls[] = [
                    'object' => 'AppCreatedCallback',
                    'app' => $app,
                ];
            }
        );

        return $mock;
    }

    public function testMake(): void
    {
        $app1 = $this->slimAppFactory->make();

        $app2 = $this->slimAppFactory->make();

        self::assertSame($app1, $app2);

        self::assertSame(
            $this->responseFactory,
            $app1->getResponseFactory(),
        );

        self::assertSame(
            $this->container,
            $app1->getContainer(),
        );

        self::assertSame(
            [
                [
                    'object' => 'AppCreatedCallback',
                    'app' => $app1,
                ],
            ],
            $this->calls
        );
    }
}
