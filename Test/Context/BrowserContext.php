<?php
use Behat\MinkExtension\Context\MinkContext;

class BrowserContext extends MinkContext
{
    /**
     * Initializes context.
     */
    public function __construct()
    {
    }

    /**
     * @Then /^I wait for "([^"]*)"$/
     */
    public function iWaitFor($selector)
    {
        $this->spin(function($context) use ($selector) {
            /** @var $context BrowserContext */
            return $context->getSession()->getPage()->find('css', $selector)->isVisible();
        });
    }

    /**
     * See http://docs.behat.org/en/v2.5/cookbook/using_spin_functions.html
     *
     * @param $lambda
     * @return bool
     */
    public function spin($lambda)
    {
        while (true)
        {
            try {
                if ($lambda($this)) {
                    return true;
                }
            } catch (Exception $e) {
                // do nothing
            }

            sleep(1);
        }

        return false;
    }

    /**
     * Sometimes you will want to use the browser window that Selenium opens to inspect
     * elements or determine the next/correct course of action. Adding "Then I pause"
     * to your .feature file will tell Selenium to wait. Think of this as a debug option.
     *
     * @Then /^I pause$/
     */
    public function iPause()
    {
        $this->getSession()->wait(100000000);
    }

    /**
     * Looks for a table, then looks for a row that contains the given text.
     * Once it finds the right row, it clicks a link in that row.
     *
     * Really handy when you have a generic "Edit" link on each row of
     * a table, and you want to click a specific one (e.g. the "Edit" link
     * in the row that contains "Item #2")
     *
     * @When /^I click on "([^"]*)" on the row containing "([^"]*)"$/
     */
    public function iClickOnOnTheRowContaining($linkName, $rowText)
    {
        /** @var $row \Behat\Mink\Element\NodeElement */
        $row = $this->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
        if (!$row) {
            throw new \Exception(sprintf('Cannot find any row on the page containing the text "%s"', $rowText));
        }
        $row->clickLink($linkName);
    }

    /**
     * Looks for a table, then looks for a row that contains the given text.
     * Once it finds the right row, it clicks a link in that row.
     *
     * Really handy when you have a generic "Edit" link on each row of
     * a table, and you want to click a specific one (e.g. the "Edit" link
     * in the row that contains "Item #2")
     *
     * @When /^I click on "([^"]*)" on the row containing "([^"]*)"$/
     */
    public function iClickOnOnTheRowContaining($linkName, $rowText)
    {
        /** @var $row \Behat\Mink\Element\NodeElement */
        $row = $this->getSession()->getPage()->find('css', sprintf('table tr:contains("%s")', $rowText));
        if (!$row) {
            throw new \Exception(sprintf('Cannot find any row on the page containing the text "%s"', $rowText));
        }
        $row->clickLink($linkName);
    }

    /**
     * @AfterStep
     * @param AfterStepScope $event
     */
    public function printLastResponseOnError(AfterStepScope $event)
    {
        if (!$event->getTestResult()->isPassed()) {
            $this->saveDebugScreenshot();
        }
    }

    /**
     * @Then /^save screenshot$/
     */
    public function saveDebugScreenshot()
    {
        if (!defined('SCREENSHOT_PATH')) {
            return;
        }

        $path = SCREENSHOT_PATH;
        $filename = microtime(true).'.png';

        if (!file_exists($path)) {
            mkdir($path);
        }

        $this->saveScreenshot($filename, $path);
    }
}
