<?php

class CliCore extends \EllisLab\ExpressionEngine\Core\ExpressionEngine
{
    /**
     * Run a given request
     *
     * Currently mostly delegates to the legacy app
     */
    public function run(EllisLab\ExpressionEngine\Core\Request $request)
    {
        if ( ! $this->booted)
        {
            throw new \Exception('Application must be booted before running.');
        }

        $this->running = TRUE;

        $application = $this->loadApplicationCore();

        $routing = $this->getRouting($request);
        $routing = $this->loadController($routing);
        $routing = $this->validateRequest($routing);

        //$application->setRequest($request);
        //$application->setResponse(new EllisLab\ExpressionEngine\Core\Response());

        // This is where EE is fully bootstrapped.
        $this->runController($routing);

        // Not necessary to return, but an option.
        return $application;
    }
}
