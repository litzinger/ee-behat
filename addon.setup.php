<?php

use EllisLab\ExpressionEngine\Core\Provider;

$addon_config = array(
    'author'      => '',
    'author_url'  => '',
    'name'        => '',
    'description' => '',
    'version'     => '',
    'namespace'   => '',

    'services.singletons' => array(
        'RequestCache' => function() {
            return new Publisher\Service\RequestCache();
        },
    ),

    'services' => array(
        'YourService' => function($provider) {
            /** @var Provider $provider */
            $service = $provider->make('AnotherService');
            return new AddonName\Service\YourService($service);
        },
    ),

    'models' => array(
    ),
);

/**
 * Any services that you need to mock for the sake of testing can be
 * overridden here. Generally such services as these need to implement
 * the same interface, and when type hinting the injected service or
 * using instanceof you will want to type hint the interface name.
 */
if (defined('BEHAT_IS_RUNNING'))
{
    $addon_config['services.singletons']['RequestCache'] = function() {
        return new Publisher\Test\Service\TestRequestCache();
    };
}

return $addon_config;
