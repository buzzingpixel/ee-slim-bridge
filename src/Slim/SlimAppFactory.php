<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\Slim;

use BuzzingPixel\SlimBridge\Container\RetrieveConfigContainer;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\Factory\AppFactory;

class SlimAppFactory
{
    private static ?App $app = null;

    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly RetrieveConfigContainer $retrieveConfigContainer,
        private readonly RetrieveAppCreatedCallback $retrieveAppCreatedCallback,
    ) {
    }

    public function make(): App
    {
        if (self::$app !== null) {
            return self::$app;
        }

        self::$app = AppFactory::create(
            $this->responseFactory,
            $this->retrieveConfigContainer->retrieve(),
        );

        $this->retrieveAppCreatedCallback->retrieve()(self::$app);

        return self::$app;
    }
}
