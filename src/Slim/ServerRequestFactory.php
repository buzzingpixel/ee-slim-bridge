<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\Slim;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\ServerRequestCreatorFactory;

class ServerRequestFactory
{
    public function make(): ServerRequestInterface
    {
        return ServerRequestCreatorFactory::create()
            ->createServerRequestFromGlobals();
    }
}
