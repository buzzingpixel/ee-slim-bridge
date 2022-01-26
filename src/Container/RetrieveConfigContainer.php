<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\Container;

use BuzzingPixel\SlimBridge\Config\Config;
use Psr\Container\ContainerInterface;
use RuntimeException;

class RetrieveConfigContainer
{
    public function __construct(private readonly Config $config)
    {
    }

    public function retrieve(): ContainerInterface
    {
        $container = $this->config->getItem(
            item: 'containerInterface',
            index: 'slimBridge',
        );

        if ($container instanceof ContainerInterface) {
            return $container;
        }

        throw new RuntimeException(
            'The config array must have a config item for ' .
                '`slimBridge` as an array with the key `containerInterface` ' .
                'that returns an  implementation of ' .
                ContainerInterface::class,
        );
    }
}
