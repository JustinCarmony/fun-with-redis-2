<?php

namespace Modes;

class PipeIncrMode extends BaseMode
{
    public function masterSetup()
    {

    }

    public function masterTeardown()
    {
        $predis = \PredisManager::GetMasterPredis();
        $predis->del('pipe_incr.value');
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
        $limit = 3000;
        $pipe = $predis->pipeline();
        while($count < $limit)
        {
            $count++;
            $pipe->incr('pipe_incr.value');
        }
        $pipe->execute();
    }

    /* Templates */

    public function templateControlPanel()
    {
        $predis = \PredisManager::GetMasterPredis();
        $val = $predis->get('pipe_incr.value');
        $val = number_format($val, 0);

        return <<<HTML
<h1>Pipe Set</h1>
<p>We're piping in increment commands 5,000 at a time. Last incr value: {$val} </p>
HTML;

    }
}