<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\Container;

use BuzzingPixel\Container\Container;
use EE_Config;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;

use function assert;

class RetrieveInternalContainer
{
    public function retrieve(): ContainerInterface
    {
        return new Container(
            [
                ResponseFactoryInterface::class => ResponseFactory::class,
                // @codeCoverageIgnoreStart
                EE_Config::class => static function (): EE_Config {
                    $config = ee()->config;

                    assert($config instanceof EE_Config);

                    return $config;
                },
            ],
        );
    }
}
