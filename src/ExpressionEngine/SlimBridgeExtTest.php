<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\ExpressionEngine;

use BuzzingPixel\SlimBridge\Config\Config;
use BuzzingPixel\SlimBridge\PhpFunctions;
use BuzzingPixel\SlimBridge\Slim\ServerRequestFactory;
use BuzzingPixel\SlimBridge\Slim\SlimAppFactory;
use ExpressionEngine\Model\Addon\Extension;
use ExpressionEngine\Service\Model\Facade as RecordService;
use ExpressionEngine\Service\Model\Query\Builder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;
use Slim\App;
use Slim\ResponseEmitter;
use Slim_bridge_ext;

use function assert;

class SlimBridgeExtTest extends TestCase
{
    /** @var mixed[] */
    private array $calls = [];

    private bool $configBooleanReturn = false;

    private ServerRequestInterface $request;

    private ResponseInterface $response;

    private ?Extension $extension = null;

    private Slim_bridge_ext $slimBridgeExt;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->configBooleanReturn = false;

        $this->request = $this->createMock(
            ServerRequestInterface::class,
        );

        $this->response = $this->createMock(
            ResponseInterface::class,
        );

        $this->extension = null;

        $slimBridgeExtRef = new ReflectionClass(
            Slim_bridge_ext::class,
        );

        $slimBridgeExt = $slimBridgeExtRef->newInstanceWithoutConstructor();

        /** @phpstan-ignore-next-line */
        assert($slimBridgeExt instanceof Slim_bridge_ext);

        $slimBridgeExtObjectRef = new ReflectionClass(
            $slimBridgeExt,
        );

        /**
         * Config
         */
        $configProperty = $slimBridgeExtObjectRef->getProperty(
            'config',
        );
        /** @noinspection PhpExpressionResultUnusedInspection */
        $configProperty->setAccessible(true);
        $configProperty->setValue(
            $slimBridgeExt,
            $this->mockConfig(),
        );

        /**
         * SlimAppFactory
         */
        $slimAppFactoryProperty = $slimBridgeExtObjectRef->getProperty(
            'slimAppFactory',
        );
        /** @noinspection PhpExpressionResultUnusedInspection */
        $slimAppFactoryProperty->setAccessible(true);
        $slimAppFactoryProperty->setValue(
            $slimBridgeExt,
            $this->mockSlimAppFactory(),
        );

        /**
         * ServerRequestFactory
         */
        $serverRequestFactoryProperty = $slimBridgeExtObjectRef->getProperty(
            'serverRequestFactory',
        );
        /** @noinspection PhpExpressionResultUnusedInspection */
        $serverRequestFactoryProperty->setAccessible(true);
        $serverRequestFactoryProperty->setValue(
            $slimBridgeExt,
            $this->mockServerRequestFactory(),
        );

        /**
         * ResponseEmitter
         */
        $responseEmitterProperty = $slimBridgeExtObjectRef->getProperty(
            'responseEmitter',
        );
        /** @noinspection PhpExpressionResultUnusedInspection */
        $responseEmitterProperty->setAccessible(true);
        $responseEmitterProperty->setValue(
            $slimBridgeExt,
            $this->mockResponseEmitter(),
        );

        /**
         * PhpFunctions
         */
        $phpFunctionsProperty = $slimBridgeExtObjectRef->getProperty(
            'phpFunctions',
        );
        /** @noinspection PhpExpressionResultUnusedInspection */
        $phpFunctionsProperty->setAccessible(true);
        $phpFunctionsProperty->setValue(
            $slimBridgeExt,
            $this->mockPhpFunctions(),
        );

        /**
         * RecordService
         */
        $recordServiceProperty = $slimBridgeExtObjectRef->getProperty(
            'recordService',
        );
        /** @noinspection PhpExpressionResultUnusedInspection */
        $recordServiceProperty->setAccessible(true);
        $recordServiceProperty->setValue(
            $slimBridgeExt,
            $this->mockRecordService(),
        );

        $this->slimBridgeExt = $slimBridgeExt;
    }

    private function mockConfig(): Config
    {
        $mock = $this->createMock(Config::class);

        $mock->method('getBoolean')->willReturnCallback(
            function (string $item, string $index): bool {
                $this->calls[] = [
                    'object' => 'Config',
                    'method' => 'getBoolean',
                    'item' => $item,
                    'index' => $index,
                ];

                return $this->configBooleanReturn;
            }
        );

        return $mock;
    }

    private function mockSlimAppFactory(): SlimAppFactory
    {
        $mock = $this->createMock(SlimAppFactory::class);

        $mock->method('make')->willReturn(
            $this->mockSlimApp(),
        );

        return $mock;
    }

    private function mockSlimApp(): App
    {
        $mock = $this->createMock(App::class);

        $mock->method('handle')->willReturnCallback(
            function (
                ServerRequestInterface $request,
            ): ResponseInterface {
                $this->calls[] = [
                    'object' => 'App',
                    'method' => 'handle',
                    'request' => $request,
                ];

                return $this->response;
            }
        );

        return $mock;
    }

    private function mockServerRequestFactory(): ServerRequestFactory
    {
        $mock = $this->createMock(
            ServerRequestFactory::class,
        );

        $mock->method('make')->willReturnCallback(
            function (): ServerRequestInterface {
                return $this->request;
            }
        );

        return $mock;
    }

    private function mockResponseEmitter(): ResponseEmitter
    {
        $mock = $this->createMock(ResponseEmitter::class);

        $mock->method('emit')->willReturnCallback(
            function (ResponseInterface $response): void {
                $this->calls[] = [
                    'object' => 'ResponseEmitter',
                    'method' => 'emit',
                    'response' => $response,
                ];
            }
        );

        return $mock;
    }

    private function mockPhpFunctions(): PhpFunctions
    {
        $mock = $this->createMock(PhpFunctions::class);

        $mock->method('stopExecution')->willReturnCallback(
            function (): void {
                $this->calls[] = [
                    'object' => 'PhpFunctions',
                    'method' => 'stopExecution',
                ];
            }
        );

        return $mock;
    }

    private function mockRecordService(): RecordService
    {
        $mock = $this->createMock(RecordService::class);

        $mock->method('get')->willReturnCallback(
            function (string $name): Builder {
                $this->calls[] = [
                    'object' => 'RecordService',
                    'method' => 'get',
                    'name' => $name,
                ];

                return $this->mockRecordQueryBuilder();
            }
        );

        $mock->method('make')->willReturnCallback(
            function (string $name): Extension {
                $this->calls[] = [
                    'object' => 'RecordService',
                    'method' => 'make',
                    'name' => $name,
                ];

                return $this->mockMadeExtension();
            }
        );

        return $mock;
    }

    private function mockRecordQueryBuilder(): Builder
    {
        $mock = $this->createMock(Builder::class);

        $mock->method('filter')->willReturnCallback(
            function (
                string $property,
                string $value
            ) use (
                $mock,
            ): Builder {
                $this->calls[] = [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => $property,
                    'value' => $value,
                ];

                return $mock;
            }
        );

        $mock->method('first')->willReturnCallback(
            function (): ?Extension {
                return $this->extension;
            }
        );

        return $mock;
    }

    private function mockMadeExtension(): Extension
    {
        $mock = $this->createMock(Extension::class);

        $mock->method('setProperty')->willReturnCallback(
            function (
                string $name,
                string | int $value
            ) use (
                $mock,
            ): Extension {
                $this->calls[] = [
                    'object' => 'MadeExtension',
                    'method' => 'setProperty',
                    'name' => $name,
                    'value' => $value,
                ];

                return $mock;
            }
        );

        $mock->method('save')->willReturnCallback(
            function () use ($mock): Extension {
                $this->calls[] = [
                    'object' => 'MadeExtension',
                    'method' => 'save',
                ];

                return $mock;
            }
        );

        $mock->method('delete')->willReturnCallback(
            function () use ($mock): Extension {
                $this->calls[] = [
                    'object' => 'MadeExtension',
                    'method' => 'delete',
                ];

                return $mock;
            }
        );

        return $mock;
    }

    private function setReq(string $req): void
    {
        $slimBridgeExtObjectRef = new ReflectionClass(
            $this->slimBridgeExt,
        );

        /**
         * Config
         */
        $configProperty = $slimBridgeExtObjectRef->getProperty(
            'req',
        );
        /** @noinspection PhpExpressionResultUnusedInspection */
        $configProperty->setAccessible(true);
        $configProperty->setValue(
            $this->slimBridgeExt,
            $req,
        );
    }

    public function testCoreBootWhenReqIsNotPage(): void
    {
        $this->slimBridgeExt->core_boot();

        self::assertSame(
            [],
            $this->calls,
        );
    }

    public function testCoreBootWhenNotEnabled(): void
    {
        $this->setReq('PAGE');

        $this->slimBridgeExt->core_boot();

        self::assertSame(
            [
                [
                    'object' => 'Config',
                    'method' => 'getBoolean',
                    'item' => 'enabled',
                    'index' => 'slimBridge',
                ],
            ],
            $this->calls,
        );
    }

    public function testCoreBootWhenEnabled(): void
    {
        $this->setReq('PAGE');

        $this->configBooleanReturn = true;

        $this->slimBridgeExt->core_boot();

        self::assertSame(
            [
                [
                    'object' => 'Config',
                    'method' => 'getBoolean',
                    'item' => 'enabled',
                    'index' => 'slimBridge',
                ],
                [
                    'object' => 'App',
                    'method' => 'handle',
                    'request' => $this->request,
                ],
                [
                    'object' => 'ResponseEmitter',
                    'method' => 'emit',
                    'response' => $this->response,
                ],
                [
                    'object' => 'PhpFunctions',
                    'method' => 'stopExecution',
                ],
            ],
            $this->calls,
        );
    }

    public function testActivateExtensionWhenRecordIsNotNull(): void
    {
        $this->setReq('PAGE');

        $this->extension = $this->createMock(
            Extension::class,
        );

        self::assertTrue($this->slimBridgeExt->activate_extension());

        self::assertSame(
            [
                [
                    'object' => 'RecordService',
                    'method' => 'get',
                    'name' => 'Extension',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'class',
                    'value' => 'Slim_bridge_ext',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'method',
                    'value' => 'core_boot',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'hook',
                    'value' => 'core_boot',
                ],
            ],
            $this->calls,
        );
    }

    public function testActivateExtensionWhenRecordNull(): void
    {
        $this->setReq('PAGE');

        self::assertTrue($this->slimBridgeExt->activate_extension());

        self::assertSame(
            [
                [
                    'object' => 'RecordService',
                    'method' => 'get',
                    'name' => 'Extension',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'class',
                    'value' => 'Slim_bridge_ext',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'method',
                    'value' => 'core_boot',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'hook',
                    'value' => 'core_boot',
                ],
                [
                    'object' => 'RecordService',
                    'method' => 'make',
                    'name' => 'Extension',
                ],
                [
                    'object' => 'MadeExtension',
                    'method' => 'setProperty',
                    'name' => 'class',
                    'value' => 'Slim_bridge_ext',
                ],
                [
                    'object' => 'MadeExtension',
                    'method' => 'setProperty',
                    'name' => 'method',
                    'value' => 'core_boot',
                ],
                [
                    'object' => 'MadeExtension',
                    'method' => 'setProperty',
                    'name' => 'hook',
                    'value' => 'core_boot',
                ],
                [
                    'object' => 'MadeExtension',
                    'method' => 'setProperty',
                    'name' => 'settings',
                    'value' => '',
                ],
                [
                    'object' => 'MadeExtension',
                    'method' => 'setProperty',
                    'name' => 'priority',
                    'value' => 1,
                ],
                [
                    'object' => 'MadeExtension',
                    'method' => 'setProperty',
                    'name' => 'version',
                    'value' => SLIM_BRIDGE_VERSION,
                ],
                [
                    'object' => 'MadeExtension',
                    'method' => 'setProperty',
                    'name' => 'enabled',
                    'value' => 'y',
                ],
                [
                    'object' => 'MadeExtension',
                    'method' => 'save',
                ],
            ],
            $this->calls,
        );
    }

    public function testUpdateExtensionWhenRecordNull(): void
    {
        $this->setReq('PAGE');

        self::assertFalse($this->slimBridgeExt->update_extension());

        self::assertSame(
            [
                [
                    'object' => 'RecordService',
                    'method' => 'get',
                    'name' => 'Extension',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'class',
                    'value' => 'Slim_bridge_ext',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'method',
                    'value' => 'core_boot',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'hook',
                    'value' => 'core_boot',
                ],
            ],
            $this->calls,
        );
    }

    public function testUpdateExtensionWhenRecordIsNotNull(): void
    {
        $this->setReq('PAGE');

        $this->extension = $this->mockMadeExtension();

        self::assertTrue($this->slimBridgeExt->update_extension());

        self::assertSame(
            [
                [
                    'object' => 'RecordService',
                    'method' => 'get',
                    'name' => 'Extension',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'class',
                    'value' => 'Slim_bridge_ext',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'method',
                    'value' => 'core_boot',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'hook',
                    'value' => 'core_boot',
                ],
                [
                    'object' => 'MadeExtension',
                    'method' => 'setProperty',
                    'name' => 'version',
                    'value' => '1.0.0',
                ],
                [
                    'object' => 'MadeExtension',
                    'method' => 'save',
                ],
            ],
            $this->calls,
        );
    }

    public function testDisableExtensionWhenRecordNull(): void
    {
        $this->setReq('PAGE');

        self::assertTrue($this->slimBridgeExt->disable_extension());

        self::assertSame(
            [
                [
                    'object' => 'RecordService',
                    'method' => 'get',
                    'name' => 'Extension',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'class',
                    'value' => 'Slim_bridge_ext',
                ],
            ],
            $this->calls,
        );
    }

    public function testDisableExtensionWhenRecordIsNotNull(): void
    {
        $this->setReq('PAGE');

        $this->extension = $this->mockMadeExtension();

        self::assertTrue($this->slimBridgeExt->disable_extension());

        self::assertSame(
            [
                [
                    'object' => 'RecordService',
                    'method' => 'get',
                    'name' => 'Extension',
                ],
                [
                    'object' => 'Builder',
                    'method' => 'filter',
                    'property' => 'class',
                    'value' => 'Slim_bridge_ext',
                ],
                [
                    'object' => 'MadeExtension',
                    'method' => 'delete',
                ],
            ],
            $this->calls,
        );
    }
}
