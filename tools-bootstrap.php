<?php

declare(strict_types=1);

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

const BASEPATH   = __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/legacy/';

/**
 * @phpstan-ignore-next-line
 */
function get_instance()
{
    global $CI;

    return $CI;
}

/** @phpstan-ignore-next-line */
function ee($dep = null)
{
    $facade = get_instance();

    if (isset($dep) && isset($facade->di)) {
        $args = func_get_args();

        return call_user_func_array(
        /** @phpstan-ignore-next-line */
            [$facade->di, 'make'],
            $args,
        );
    }

    return $facade;
}

require_once __DIR__ . '/src/ExpressionEngine/addon.setup.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Service/Template/Variables/ModifiableTrait.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/legacy/fieldtypes/EE_Fieldtype.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/legacy/database/DB_forge.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/legacy/database/DB_driver.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/legacy/database/DB_active_rec.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Service/Database/Query.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/legacy/database/drivers/mysqli/mysqli_driver.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/legacy/database/DB_result.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Service/Model/Facade.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Service/Validation/ValidationAware.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Service/Event/Subscriber.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Service/Event/Publisher.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Library/Mixin/Mixable.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Library/Mixin/MixableImpl.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Library/Data/Entity.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Library/Data/SerializableEntity.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Service/Model/Model.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Model/Addon/Module.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Service/Model/Query/Builder.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Model/Addon/Action.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Model/Addon/Fieldtype.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Model/Addon/Extension.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Library/CP/URL.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Service/URL/URLFactory.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/legacy/libraries/Csrf.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/legacy/core/Lang.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/legacy/libraries/Cp.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/legacy/libraries/Functions.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/legacy/libraries/Typography.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Service/View/View.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Service/Alert/AlertCollection.php';
require_once __DIR__ . '/vendor/expressionengine/expressionengine/system/ee/ExpressionEngine/Service/Alert/Alert.php';
