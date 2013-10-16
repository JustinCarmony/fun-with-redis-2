<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jcarmony
 * Date: 10/15/13
 * Time: 10:50 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Modes;

class IdleMode extends BaseMode
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
<h1>All Workers are Idle</h1>
HTML;

    }
}