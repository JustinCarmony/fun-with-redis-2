<?php

namespace Modes;

class SimpleSetMode extends BaseMode
{
    public function masterSetup()
    {

    }

    public function masterTeardown()
    {
        $predis = \PredisManager::GetMasterPredis();
        $predis->del('simple_set.value');
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
        $predis = \PredisManager::GetClientPredis();
        $count = 0;
        $limit = 500;
        while($count < $limit)
        {
            $count++;
            $predis->set('simple_get.value', rand(0,1000));
        }

    }

    /* Templates */

    public function templateControlPanel()
    {
        return <<<HTML
<h1>Better World!</h1>
HTML;

    }
}