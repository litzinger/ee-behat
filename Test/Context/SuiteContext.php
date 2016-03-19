<?php
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;

class SuiteContext implements Context, SnippetAcceptingContext
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        ee()->load->dbforge();
    }

    /**
     * @BeforeSuite
     */
    public static function beforeSuite()
    {
        // CircleCI
        if (file_exists('/home/ubuntu/YOUR_GITHUB_REPO_SHORT_NAME')) {
            define('SCREENSHOT_PATH', '/home/ubuntu/YOUR_GITHUB_REPO_SHORT_NAME/behat_screenshots/');
            exec("mysql --host=localhost -uubuntu -e 'DROP DATABASE IF EXISTS circle_test'");
            exec("mysql --host=localhost -uubuntu -e 'CREATE DATABASE circle_test'");
            exec("mysql --host=localhost -uubuntu circle_test < ".ADDON_PATH."/Test/ee300_clean.sql");

        // Local
        } else {
            $mysql = '/Applications/MAMP/Library/bin/mysql';
            exec("$mysql --host=localhost -uroot -proot -e 'DROP DATABASE IF EXISTS ee300_clean'");
            exec("$mysql --host=localhost -uroot -proot -e 'CREATE DATABASE ee300_clean'");
            exec("$mysql --host=localhost -uroot -proot ee300_clean < ".ADDON_PATH."/Test/ee300_clean.sql");
        }
    }

    /**
     * @AfterSuite
     */
    public static function afterSuite()
    {

    }

    /**
     * Converts a list to an array. E.g. Then I expect foo to equal "[bar, fizz, baz]"
     *
     * @Transform /^\[(.*)\]$/
     */
    public function castStringToArray($string)
    {
        return explode(',', $string);
    }

    /**
     * Update EE's site perferences
     *
     * @Given /^I set site config "([^"]*)" to "([^"]*)"$/
     * @param $key
     * @param $value
     */
    public function iSetSiteConfigTo($key, $value)
    {
        $result = ee()->db->select('site_system_preferences')->get('sites');
        $prefs = $result->row('site_system_preferences');

        $prefs = unserialize(base64_decode($prefs));
        $prefs[$key] = $value;

        $prefs = base64_encode(serialize($prefs));
        ee()->db->update('sites', ['site_system_preferences' => $prefs], ['site_id' => 1]);
    }
}
