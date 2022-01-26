<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\Config;

use EE_Config;

use function in_array;

class Config
{
    public function __construct(private readonly EE_Config $eeConfig)
    {
    }

    /** @noinspection PhpMixedReturnTypeCanBeReducedInspection */
    public function getItem(string $item, string $index = ''): mixed
    {
        return $this->eeConfig->item(
            $item,
            $index
        );
    }

    public function getBoolean(string $item, string $index = ''): bool
    {
        return in_array(
            $this->getItem(item: $item, index: $index),
            [
                'y',
                'yes',
                '1',
                1,
                'true',
                true,
            ],
            true,
        );
    }
}
