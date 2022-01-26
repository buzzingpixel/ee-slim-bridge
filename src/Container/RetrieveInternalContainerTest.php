<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\Container;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;

class RetrieveInternalContainerTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function test(): void
    {
        $container = (new RetrieveInternalContainer())->retrieve();

        self::assertInstanceOf(
            ResponseFactory::class,
            $container->get(ResponseFactoryInterface::class),
        );
    }
}
