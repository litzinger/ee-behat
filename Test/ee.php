<?php

$project_url = 'http://ee300.dev/';
$project_base = realpath('/path/to/your/ee/sandbox/dev300').'/';
$system_path = $project_base . 'system';
$debug = 2;

// Set a few server vars that an add-on or EE expects.
// This is simulating a normal HTTP request.
$_SERVER = array(
    'PHP_SELF' => 'ee.php',
    'REMOTE_ADDR' => 'localhost',
    'SERVER_PORT' => '80',
    'SERVER_NAME' => 'ee300.dev',
    'REQUEST_URI' => '',
    'argv' => array(),
);

// Turn off extensions, we don't need 3rd party hooks
// running in the middle of our tests.
$assign_to_config['allow_extensions'] = 'n';
$assign_to_config['secure_forms'] = 'n';
$assign_to_config['disable_csrf_protection'] = 'y';

/*
 * ---------------------------------------------------------------
 *  Disable all routing, send everything to the frontend
 * ---------------------------------------------------------------
 */
$routing['directory'] = '';
$routing['controller'] = 'ee';
$routing['function'] = 'index';

/*
 * --------------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * --------------------------------------------------------------------
 */

if (realpath($system_path) !== FALSE)
{
    $system_path = realpath($system_path);
}

$system_path = rtrim($system_path, '/').'/';

/*
 * --------------------------------------------------------------------
 *  Now that we know the path, set the main constants
 * --------------------------------------------------------------------
 */
// The name of this file
define('SELF', basename(__FILE__));

// Path to this file
define('FCPATH', __DIR__.'/');

// Path to the "system" folder
define('SYSPATH', $system_path);

// Name of the "system folder"
define('SYSDIR', basename($system_path));

// The $debug value as a constant for global access
define('DEBUG', $debug);  unset($debug);

/*
 * --------------------------------------------------------------------
 *  Set the error reporting level
 * --------------------------------------------------------------------
 */
//error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);
@ini_set('display_errors', 1);

require_once 'boot.php';

