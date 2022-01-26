<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
// phpcs:disable Squiz.Classes.ClassFileName.NoMatch
// phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
use ExpressionEngine\Model\Addon\Extension;
use ExpressionEngine\Service\Model\Facade as RecordService;

class Slim_bridge_ext
{
    public string $version = SLIM_BRIDGE_VERSION;

    private RecordService $recordService;

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->recordService = ee('Model');
    }

    public function core_boot(): void
    {
        // TODO: Implement core boot
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
