<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
use BuzzingPixel\SlimBridge\Config\Config;
use BuzzingPixel\SlimBridge\Container\RetrieveInternalContainer;
use BuzzingPixel\SlimBridge\PhpFunctions;
use BuzzingPixel\SlimBridge\Slim\ServerRequestFactory;
use BuzzingPixel\SlimBridge\Slim\SlimAppFactory;
use ExpressionEngine\Model\Addon\Extension;
use ExpressionEngine\Service\Model\Facade as RecordService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\ResponseEmitter;

class Slim_bridge_ext
{
    public string $version = SLIM_BRIDGE_VERSION;

    private Config $config;

    private SlimAppFactory $slimAppFactory;

    private ServerRequestFactory $serverRequestFactory;

    private ResponseEmitter $responseEmitter;

    private PhpFunctions $phpFunctions;

    private RecordService $recordService;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $container = (new RetrieveInternalContainer())->retrieve();

        /** @phpstan-ignore-next-line */
        $this->config = $container->get(Config::class);

        /** @phpstan-ignore-next-line */
        $this->slimAppFactory = $container->get(SlimAppFactory::class);

        /** @phpstan-ignore-next-line */
        $this->serverRequestFactory = $container->get(
            ServerRequestFactory::class,
        );

        /** @phpstan-ignore-next-line */
        $this->responseEmitter = $container->get(ResponseEmitter::class);

        /** @phpstan-ignore-next-line */
        $this->phpFunctions = $container->get(PhpFunctions::class);

        $this->recordService = ee('Model');
    }

    public function core_boot(): void
    {
        $isEnabled = $this->config->getBoolean(
            item: 'enabled',
            index: 'slimBridge'
        );

        if (! $isEnabled) {
            return;
        }

        $response = $this->slimAppFactory->make()->handle(
            $this->serverRequestFactory->make(),
        );

        $this->responseEmitter->emit($response);

        $this->phpFunctions->stopExecution();
    }

    public function activate_extension(): bool
    {
        $record = $this->recordService->get('Extension')
            ->filter('class', 'Slim_bridge_ext')
            ->filter('method', 'core_boot')
            ->filter('hook', 'core_boot')
            ->first();

        if ($record !== null) {
            return true;
        }

        $record = $this->recordService->make('Extension');

        assert($record instanceof Extension);

        $record->setProperty('class', 'Slim_bridge_ext')
            ->setProperty('method', 'core_boot')
            ->setProperty('hook', 'core_boot')
            ->setProperty('settings', '')
            ->setProperty('priority', 1)
            ->setProperty('version', SLIM_BRIDGE_VERSION)
            ->setProperty('enabled', 'y')
            ->save();

        return true;
    }

    public function update_extension(): bool
    {
        $record = $this->recordService->get('Extension')
            ->filter('class', 'Slim_bridge_ext')
            ->filter('method', 'core_boot')
            ->filter('hook', 'core_boot')
            ->first();

        if ($record === null) {
            return false;
        }

        assert($record instanceof Extension);

        $record->setProperty('version', SLIM_BRIDGE_VERSION)
            ->save();

        return true;
    }

    public function disable_extension(): bool
    {
        $record = $this->recordService->get('Extension')
            ->filter('class', 'Slim_bridge_ext')
            ->first();

        if ($record === null) {
            return true;
        }

        assert($record instanceof Extension);

        $record->delete();

        return true;
    }
}
