<?php
/**
 * Created by JetBrains PhpStorm.
 * User: justin
 * Date: 4/26/12
 * Time: 9:23 AM
 * To change this template use File | Settings | File Templates.
 */

class Client
{
    /**
     * @var Predis\Client
     */
    public $predis;
    public $internal_id;
    public $instance_id;
    public $reboot_id;
    public $client_id;
    public $working = false;
    public $mode_name = '';
    /**
     * @var Modes\BaseMode
     */
    public $mode = null;
    public $pipeline = 'off';
    public $pipeline_count = 100;
    public $latency_ms = null;
    public $latency_start = 0;

    const ALLOWED_CHARS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";


    public function __construct($internal_id)
    {
        $this->predis = PredisManager::GetClientPredis();
        $this->internal_id = $internal_id;
    }

    public function Log($txt)
    {
        echo "[".number_format(round(microtime(true), 2), 2, '.', '')."] ".$txt."\n";
    }

    public function StartLatency()
    {
        $this->latency_start = microtime(true);
    }

    public function EndLatency()
    {
        $this->latency_ms = round((microtime(true) - $this->latency_start) * 1000, 0);
    }

    public function Run()
    {
        $this->Startup();
        $count = 0;

        while(1)
        {
            $count++;

            $this->CheckMode();

            $this->pipeline = $this->predis->get('system.pipeline');
            $this->pipeline_count = $this->predis->get('system.pipeline_count');

            $this->Work();

            if($count >= 60)
            {
                $count = 0;
            }

            // Check to see if a master reboot has been issued
            if($this->predis->get('reboot.client') != $this->reboot_id)
            {
                $this->predis->hdel('client.heartbeat', $this->client_id);
                $this->predis->hdel('client.status', $this->client_id);
                $this->Log("Reboot Detected!");
                $this->Log("Shutting Down...");
                return;
            }

            // Check to see if master reset has been called
            if($this->predis->get('system.instance') != $this->instance_id)
            {
                $this->Log("System Reset Detected... Executing...");
                // Re-startup
                $this->Startup();
                $this->Log("System Reset Complete, New Instace ID: ".$this->instance_id);
            }

            $this->Heartbeat();
        }
    }

    public function Startup()
    {
        $this->Log("Random Sleep to offset Clients");

        usleep(rand(1000000,5000000));
        $this->Log("Executing Client Startup...");
        $this->Log("Internal ID: ".$this->internal_id);
        $this->instance_id = $this->predis->get('system.instance');
        $this->Log("Instance ID: ".$this->instance_id);

        $this->client_id = $this->predis->incr('client.incr');
        $this->log("Client ID: ".$this->client_id);

        $this->reboot_id = $this->predis->get('reboot.client');
        if(!$this->reboot_id)
        {
            $this->predis->set('reboot.client', 1);
            $this->reboot_id = 1;
        }

        $this->Log("Reboot ID: ".$this->reboot_id);

        $this->method = 'idle';

        // Check in with a hearbeat
        $this->Heartbeat();
    }

    public function Heartbeat()
    {
        $time = time();
        $this->predis->hset('client.heartbeats', $this->client_id, $time);
        $status = new stdClass();
        $status->client_id = $this->client_id;
        $status->internal_id = $this->internal_id;
        $status->working = $this->working;
        $status->heartbeat = $time;
        $status->latency_ms = $this->latency_ms;
        $ips = Utility::GetMachineIPs();
        $status->ip = $ips[3]; // Get the internal ID
        $status->hostname = gethostname();
        $this->predis->hset('client.status', $this->client_id, json_encode($status));
    }

    public function CheckMode()
    {
        $this->Log("Checking Current Mode");
        $current_mode = $this->predis->get('system.mode');

        if($current_mode != $this->mode_name)
        {
            $this->Log("Mode Doesn't Match. Old: {$this->mode_name} New: {$this->mode_name}");

            // Tear Down the old Mode
            if($this->mode)
            {
                $this->Log("Tearing Down old Mode");
                $this->mode->clientTeardown();
            }

            $this->Log("Getting New Mode");
            $this->mode = ModeManager::getInstance()->createMode($current_mode);
            $this->mode->setClientWorker($this);
            $this->mode->clientSetup();
            $this->mode_name = $current_mode;

            $this->Log("New Mode Set & Ready to Go!");
        }
    }

    public function Work()
    {
        $this->StartLatency();
        $percent = $this->predis->get('system.workforce') / 10;
        $this->EndLatency();

        $this->working = false;

        if($percent < 1)
        {
            $this->mode->clientIdle();
            return;
        }

        if(($this->client_id % 10) > $percent - 1)
        {
            $this->mode->clientIdle();
            return;
        }

        $this->working = true;

        if($this->mode)
        {
            $this->mode->clientWork();
        }
        else
        {
            $this->Log("Huh, thats odd, I don't have a mode set!");
            usleep(4000000);
        }
    }

    /** OLD DEPRECADED FUNCTIONS */
    public function Idle()
    {
        usleep(4000000);
        echo ".";
    }

    public function Increment()
    {
        $count = 0;
        $limit = 1000;
        if($this->pipeline == 'on')
        {
            $pipe = null;
            while($count < $limit)
            {
                $count++;
                if(!$pipe)
                {
                    $pipe = $this->predis->pipeline();
                }
                $pipe->incr('increment.value');
                if($count % $this->pipeline_count == 0)
                {
                    $this->StartLatency();
                    $pipe->execute();
                    $this->EndLatency();
                    unset($pipe);
                }
            }

            if($pipe)
            {
                $this->StartLatency();
                $pipe->execute();
                $this->EndLatency();
            }
        }
        else
        {
            while($count < $limit)
            {
                $count++;
                $this->StartLatency();
                $this->predis->incr('increment.value');
                $this->EndLatency();
            }
        }
    }

    public function Random_Number()
    {
        $count = 0;
        $limit = 1000;
        if($this->pipeline == 'on')
        {
            $pipe = null;
            while($count < $limit)
            {
                $count++;
                if(!$pipe)
                {
                    $pipe = $this->predis->pipeline();
                }

                $num = rand(1, 5000000);
                $key_num = floor($num / 100000);

                $pipe->hset('random_number.set:'.$key_num, $num, $num);
                if($count % $this->pipeline_count == 0)
                {
                    $this->StartLatency();
                    $pipe->execute();
                    $this->EndLatency();
                    unset($pipe);
                }
            }

            if($pipe)
            {
                $this->StartLatency();
                $pipe->execute();
                $this->EndLatency();
            }
        }
        else
        {
            while($count < $limit)
            {
                $count++;
                $num = rand(1, 5000000);
                $key_num = floor($num / 100000);

                $this->StartLatency();
                $this->predis->hset('random_number.set:'.$key_num, $num, $num);
                $this->EndLatency();
            }
        }
    }

    public function Md5_Gen()
    {
        $count = 0;
        $limit = 1000;
        if($this->pipeline == 'on')
        {
            $pipe = null;
            $end = $this->predis->incrby('md5_gen.value', $limit);
            $start = $count = $end - $limit;
            while($count < $end)
            {
                $count++;
                if(!$pipe)
                {
                    $pipe = $this->predis->pipeline();
                }

                $value = self::GetHashFromID($count);
                $hash = md5($value);
                $group = substr($hash, 0, 2);
                $pipe->hset('md5_gen.set:'.$group, $hash, $value);
                if($count % $this->pipeline_count == 0)
                {
                    $this->StartLatency();
                    $pipe->execute();
                    $this->EndLatency();
                    unset($pipe);
                }
            }

            if($pipe)
            {
                $this->StartLatency();
                $pipe->execute();
                $this->EndLatency();
            }
        }
        else
        {
            $end = $this->predis->incrby('md5_gen.value', $limit);
            $start = $count = $end - $limit;
            while($count < $end)
            {
                $count++;
                $value = self::GetHashFromID($count);
                $hash = md5($value);
                $group = substr($hash, 0, 2);

                $this->StartLatency();
                $this->predis->hset('md5_gen.set:'.$group, $hash, $value);
                $this->EndLatency();
            }
        }
    }

    public function Rand_Read()
    {
        usleep(1000000);
        echo ".";
    }

    public function Rand_Write()
    {
        usleep(1000000);
        echo ".";
    }

    public function Bench()
    {
        usleep(1000000);
        echo ".";
    }

    static public function GetHashFromID ($integer)
    {
        $base = self::ALLOWED_CHARS;
        $length = strlen($base);
        $out = '';
        while($integer > $length - 1)
        {
            $out = $base[fmod($integer, $length)] . $out;
            $integer = floor( $integer / $length );
        }
        return $base[$integer] . $out;
    }
}