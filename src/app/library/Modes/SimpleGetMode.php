<?php

namespace Modes;

class SimpleGetMode extends BaseMode
{
    public function masterSetup()
    {
        // Do nothing
    }

    public function masterTeardown()
    {
        // Do nothing
    }

    public function clientSetup()
    {
        // Do nothing
    }

    public function clientTeardown()
    {
        // Do nothing
    }

    public function clientWork()
    {
        sleep(5);
    }

    /* Templates */

    public function templateControlPanel()
    {
        return <<<HTML
<h1>Better World!</h1>
HTML;

    }
}