<?php

declare(strict_types=1);

namespace BuzzingPixel\SlimBridge\ExpressionEngine;

use ExpressionEngine\Model\Addon\Extension;
use ExpressionEngine\Service\Model\Facade as RecordService;
use ExpressionEngine\Service\Model\Query\Builder;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Slim_bridge_ext;

use function assert;

class SlimBridgeExtTest extends TestCase
{
    /** @var mixed[] */
    private array $calls = [];

    private ?Extension $extension = null;

    private Slim_bridge_ext $slimBridgeExt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calls = [];

        $this->extension = null;

        $slimBridgeExtRef = new ReflectionClass(
            Slim_bridge_ext::class,
        );

        $slimBridgeExt = $slimBridgeExtRef->newInstanceWithoutConstructor();

        assert($slimBridgeExt instanceof Slim_bridge_ext);

        $slimBridgeExtObjectRef = new ReflectionClass(
            $slimBridgeExt,
        );

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

    private function mockRecordService(): RecordService
    {
        $mock = $this->createMock(RecordService::class);

        // TODO
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

    public function testCoreBoot(): void
    {
        $this->slimBridgeExt->core_boot();

        self::assertTrue(true);
    }

    public function testActivateExtensionWhenRecordIsNotNull(): void
    {
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
