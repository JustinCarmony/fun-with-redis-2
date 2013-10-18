<?php

namespace Modes;

class PipeSetMode extends BaseMode
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
        $limit = 1000;
        $pipe = $predis->pipeline();
        while($count < $limit)
        {
            $count++;
            $pipe->set('simple_get.value', rand(0,1000));
        }
        $pipe->execute();

    }

    /* Templates */

    public function templateControlPanel()
    {
        return <<<HTML
<h1>Better World!</h1>
HTML;

    }
}