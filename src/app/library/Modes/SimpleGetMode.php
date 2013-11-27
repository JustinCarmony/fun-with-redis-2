<?php

namespace Modes;

class SimpleGetMode extends BaseMode
{
    public function masterSetup()
    {
        $predis = \PredisManager::GetMasterPredis();
        $predis->set('simple_get.value', rand(1,9999999999));
    }

    public function masterTeardown()
    {
        $predis = \PredisManager::GetMasterPredis();
        $predis->del('simple_get.value');
        $predis->del('simple_get.incr');
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
            $predis->get('simple_get.value');

            if(rand(0,100) == 100)
            {
                $predis->incrby('simple_get.incr', $uncounted);
                $uncounted = 0;
            }
        }
        if($uncounted > 0)
        {
            $predis->incrby('simple_get.incr', $uncounted);
        }
    }

    /* Templates */

    public function templateControlPanel()
    {
        $predis = \PredisManager::GetMasterPredis();
        $val = $predis->get('simple_get.value');
        $incr = $predis->get('simple_get.incr');
        $incr = number_format($incr, 0);
        return <<<HTML
<h1>Simple Get</h1>
<p>All Active Workers are performing a "Get" on the key simple_get.value which is {$val}, and has been called {$incr} times. </p>
HTML;

    }
}