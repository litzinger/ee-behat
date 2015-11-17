<?php  if ( ! defined('SYSPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package     ExpressionEngine
 * @author      EllisLab Dev Team
 * @copyright   Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license     https://ellislab.com/expressionengine/user-guide/license.html
 * @link        http://ellislab.com
 * @since       Version 3.0.0
 * @filesource
 */

/*
 * ------------------------------------------------------
 *  Set and load the framework constants
 *
 *  BASEPATH - path to the legacy app folder. Most legacy
 *             files check for this (`if ! defined ...`)
 * ------------------------------------------------------
 */
define('BASEPATH', SYSPATH.'ee/legacy/');

require BASEPATH.'config/constants.php';

/*
 * ------------------------------------------------------
 *  Load the autoloader and register it
 * ------------------------------------------------------
 */
require SYSPATH.'ee/EllisLab/ExpressionEngine/Core/Autoloader.php';

EllisLab\ExpressionEngine\Core\Autoloader::getInstance()
    ->addPrefix('EllisLab', SYSPATH.'ee/EllisLab/')
    ->addPrefix('Michelf', SYSPATH.'ee/legacy/libraries/typography/Markdown/Michelf/')
    ->register();

/*
 * ------------------------------------------------------
 *  Load the global functions
 * ------------------------------------------------------
 */
require SYSPATH.'ee/EllisLab/ExpressionEngine/Boot/boot.common.php';

/*
 * ------------------------------------------------------
 *  Define a custom error handler so we can log PHP errors
 * ------------------------------------------------------
 */
set_error_handler('_exception_handler');

/*
 * ------------------------------------------------------
 *  Check for the installer if we're booting the CP
 * ------------------------------------------------------
 */
use EllisLab\ExpressionEngine\Core;

require_once 'core.php';

$core = new CliCore();

/*
 * ------------------------------------------------------
 *  Boot the core
 * ------------------------------------------------------
 */
$core->boot();

/*
 * ------------------------------------------------------
 *  Set config items from the index.php file
 * ------------------------------------------------------
 */
if (isset($assign_to_config))
{
    $core->overrideConfig($assign_to_config);
}

/*
 * ------------------------------------------------------
 *  Set routing overrides from the index.php file
 * ------------------------------------------------------
 */
if (isset($routing))
{
    $core->overrideRouting($routing);
}

/*
 * ------------------------------------------------------
 *  Create global helper functions
 *
 *  Using `CI` for the global name, just in case someone
 *  is relying on that instead of get_instance()
 * ------------------------------------------------------
 */

$CI = $core->getLegacyApp()->getFacade();

function get_instance()
{
    global $CI;
    return $CI;
}

function ee($dep = NULL)
{
    $facade = get_instance();

    if (isset($dep) && isset($facade->di))
    {
        $args = func_get_args();
        return call_user_func_array(array($facade->di, 'make'), $args);
        return $facade->di->make($dep);
    }

    return $facade;
}

$app = $core->run(Core\Request::fromGlobals());
