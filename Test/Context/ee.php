<?php
/*
 * Bootstrap ExpressionEngine so the application
 * is available from virtually anywhere via ee()
 *
 * @author Pascal Kriete
 * @author Brian Litzinger
 */

$project_url = 'http://yoursite.dev/';
$project_base = realpath('/path/to/project').'/';

// Path constants
define('SYSPATH', $project_base.'system/');
define('BASEPATH', SYSPATH.'ee/legacy/');
define('APPPATH',  BASEPATH);

define('LD', '{');
define('RD', '}');

define('IS_CORE', FALSE);
define('DEBUG', 1);

// Turn off "Strict Standards: Only variables should be assigned by reference" errors
error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);

// Minor CI annoyance
function log_message() {}

function &get_config($replace = array())
{
    static $_config;

    if (isset($_config))
    {
        return $_config[0];
    }

    // Fetch the config file
    if (file_exists(SYSPATH.'user/config/config.php'))
    {
        require(SYSPATH.'user/config/config.php');
    }
    else
    {
        exit('The configuration file does not exist.');
    }

    // Does the $config array exist in the file?
    if ( ! isset($config) OR ! is_array($config))
    {
        set_status_header(503);
        exit('Your config file does not appear to be formatted correctly.');
    }

    // Are any values being dynamically replaced?
    if (count($replace) > 0)
    {
        foreach ($replace as $key => $val)
        {
            if (isset($config[$key]))
            {
                $config[$key] = $val;
            }
        }
    }

    $_config[0] =& $config;

    return $_config[0];
}

function config_item($item)
{
    static $_config_item = array();

    if ( ! isset($_config_item[$item]))
    {
        $config =& get_config();

        if ( ! isset($config[$item]))
        {
            return FALSE;
        }
        $_config_item[$item] = $config[$item];
    }

    return $_config_item[$item];
}

function is_loaded($class = '')
{
    static $_is_loaded = array();

    if ($class != '')
    {
        $_is_loaded[strtolower($class)] = $class;
    }

    return $_is_loaded;
}

function load_class($class, $directory = 'libraries', $prefix = 'EE_')
{
    static $_classes = array();

    // Does the class exist?  If so, we're done...
    if (isset($_classes[$class]))
    {
        return $_classes[$class];
    }

    $name = FALSE;

    // Look for the class first in the native system/libraries folder
    // thenin the local application/libraries folder
    foreach (array(BASEPATH, APPPATH) as $path)
    {
        if (file_exists($path.$directory.'/'.$class.'.php'))
        {
            $name = $prefix.$class;

            if (class_exists($name) === FALSE)
            {
                require($path.$directory.'/'.$class.'.php');
            }

            break;
        }
    }

    // Is the request a class extension?  If so we load it too
    if (file_exists(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php'))
    {
        $name = config_item('subclass_prefix').$class;

        if (class_exists($name) === FALSE)
        {
            require(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php');
        }
    }

    // Did we find the class?
    if ($name === FALSE)
    {
        // Note: We use exit() rather then show_error() in order to avoid a
        // self-referencing loop with the Excptions class
        set_status_header(503);
        exit('Unable to locate the specified class: '.$class.'.php');
    }

    // Keep track of what we just loaded
    is_loaded($class);

    $_classes[$class] = new $name();
    return $_classes[$class];
}

function set_status_header($id) {}


require SYSPATH."ee/EllisLab/ExpressionEngine/Core/Autoloader.php";

$autoloader = EllisLab\ExpressionEngine\Core\Autoloader::getInstance()
    ->addPrefix('EllisLab', SYSPATH.'ee/EllisLab/');
$autoloader->register();

global $di;
$di = new EllisLab\ExpressionEngine\Service\Dependency\InjectionContainer();
$reg = new EllisLab\ExpressionEngine\Core\ProviderRegistry($di);
$app = new EllisLab\ExpressionEngine\Core\Application(
    $autoloader,
    $di,
    $reg
);

$provider = $app->addProvider(
    SYSPATH.'ee/EllisLab/ExpressionEngine',
    'app.setup.php',
    'ee'
);

$provider->setConfigPath(SYSPATH.'user/config');

$di->register('App', function($di, $prefix = NULL) use ($app)
{
    if (isset($prefix))
    {
        return $app->get($prefix);
    }

    return $app;
});

function ee($dep = NULL)
{
    if (isset($dep))
    {
        global $di;
        return $di->make($dep);
    }

    static $EE;
    if ( ! $EE) $EE = new stdClass();
    return $EE;
}

function get_instance()
{
    return ee();
}

define('BEHAT_IS_RUNNING', true);
define('URL_THIRD_THEMES', $project_url.'themes/user/third_party/');
define('PATH_THIRD', SYSPATH.'user/addons/');

ee()->di = $di;

require_once(BASEPATH.'database/DB.php');

$files = [
    'ee/legacy/core/Config.php',
    'ee/legacy/database/DB_forge.php',
    'ee/legacy/database/drivers/mysqli/mysqli_forge.php',
    'ee/legacy/core/URI.php',
    'ee/legacy/core/Input.php',
    'ee/legacy/core/Loader.php',
    'ee/legacy/libraries/Extensions.php',
    'ee/legacy/core/Model.php',
    'ee/legacy/models/grid_model.php',
];

foreach ($files as $file) {
    require_once(SYSPATH.$file);
}

ee()->db = DB([
    'hostname' => '127.0.0.1',
    'username' => '',
    'password' => '',
    'database' => '',
    'dbdriver' => 'mysqli',
    'dbprefix' => 'exp_',
    'pconnect' => FALSE,
    //'port' => 8889
]);

ee()->load = new EE_Loader();
ee()->load->setFacade(new EllisLab\ExpressionEngine\Legacy\Facade());
ee()->config = new EE_Config();
ee()->config->set_item('site_id', 1);
ee()->uri = new EE_URI();
ee()->input = new EE_Input();
ee()->dbforge = new CI_DB_mysqli_forge();
ee()->extensions = new EE_Extensions();
ee()->load->helper('string');

$app->addProvider(SYSPATH.'user/addons/addon_name');
