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
        $predis->del('simple_set.incr');
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
        $uncounted = 0;

        while($count < $limit)
        {
            $count++;
            $uncounted++;
            $predis->set('simple_set.value', rand(0,1000000));

            if(rand(0,100) == 100)
            {
                $predis->incrby('simple_set.incr', $uncounted);
                $uncounted = 0;
            }
        }
        if($uncounted > 0)
        {
            $predis->incrby('simple_set.incr', $uncounted);
        }
    }

    /* Templates */

    public function templateControlPanel()
    {
        $predis = \PredisManager::GetMasterPredis();
        $val = $predis->get('simple_set.value');
        $incr = $predis->get('simple_set.incr');
        $incr = number_format($incr, 0);

        return <<<HTML
<h1>Simple Set</h1>
<p>All of the workers are setting random values, current value: {$val}, it has been set {$incr} times.</p>
HTML;

    }
}