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
        exec("/Applications/MAMP/Library/bin/mysql --host=localhost -uroot -proot -e 'DROP DATABASE IF EXISTS ee300_clean'");
        exec("/Applications/MAMP/Library/bin/mysql --host=localhost -uroot -proot -e 'CREATE DATABASE ee300_clean'");
        exec("/Applications/MAMP/Library/bin/mysql --host=localhost -uroot -proot ee300_clean < ".ADDON_PATH."/Test/ee300_clean.sql");
    }

    /**
     * @AfterSuite
     */
    public static function afterSuite()
    {

    }
}
