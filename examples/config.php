<?php

use BuzzingPixel\Container\ConstructorParamConfig;
use BuzzingPixel\Container\Container;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

$config = [];

$config['other_ee_config_items'] = 'y';

$config['slimBridge'] = [
    'enabled' => true,
    'containerInterface' => new Container(
        bindings: [
            MyInterface::class => MyConcreteClass::class,
            MyOtherClass::class => static function (): MyOtherClass {
                return new MyOtherClass();
            }
            // etc.
        ],
        constructorParamConfigs: [
            new ConstructorParamConfig(
                id: SomeClass::class,
                param: 'myScalarParamLikeApiKeyOrSomething',
                give: 'SomeSecret',
            )
        ],
    ),
    'appCreatedCallback' => static function (App $app): void {
        $app->get('/', function () use (
            $app,
        ): ResponseInterface {
            /**
             * @psalm-suppress PossiblyNullReference
             * @phpstan-ignore-next-line
             */
            $responseFactory = $app->getContainer()->get(
                ResponseFactoryInterface::class
            );

            assert(
                $responseFactory instanceof ResponseFactoryInterface
            );

            $response = $responseFactory->createResponse();

            $response->getBody()->write('hello world');

            return $response;
        });

        $app->get('/test/route', function () use (
            $app,
        ): ResponseInterface {
            /**
             * @psalm-suppress PossiblyNullReference
             * @phpstan-ignore-next-line
             */
            $responseFactory = $app->getContainer()->get(
                ResponseFactoryInterface::class
            );

            assert(
                $responseFactory instanceof ResponseFactoryInterface
            );

            $response = $responseFactory->createResponse();

            $response->getBody()->write('test-thing');

            return $response;
        });
    },
];
