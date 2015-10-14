<?php
use Behat\MinkExtension\Context\MinkContext;

class BrowserContext extends MinkContext
{
    /**
     * Initializes context.
     */
    public function __construct()
    {
        // Bootstrap EE()
        // require_once 'ee.php';
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
}
