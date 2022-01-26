<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\Slim;

use BuzzingPixel\SlimBridge\Config\Config;

use function is_callable;

class RetrieveAppCreatedCallback
{
    public function __construct(private readonly Config $config)
    {
    }

    public function retrieve(): callable
    {
        $appCreatedCallback = $this->config->getItem(
            item: 'appCreatedCallback',
            index: 'slimBridge',
        );

        if (is_callable($appCreatedCallback)) {
            return $appCreatedCallback;
        }

        return static function (): void {
        };
    }
}
