<?php
namespace Modes;

abstract class BaseMode
{
    /**
     * @var \Client
     */
    public $worker;

    abstract public function masterSetup();
    abstract public function masterTeardown();

    abstract public function clientSetup();
    abstract public function clientTeardown();

    abstract public function clientWork();

    abstract public function templateControlPanel();

    public function clientIdle()
    {
        $pubsub = $this->worker->predis->pubSub();
        $pubsub->subscribe('system.changes', 'system.ping');

        $pings = 0;

        foreach ($pubsub as $message) {
            switch ($message->kind) {
                case 'subscribe':
                    $this->worker->Log("Subscribed to {$message->channel}\n");
                    break;

                case 'message':
                    if ($message->channel == 'system.changes') {
                        if ($message->payload == 'quit_loop') {
                            $this->worker->Log("Aborting pubsub loop...\n");

                        }
                    }
                    else
                    {
                        $pings++;
                        if($pings > 5)
                        {
                            $this->worker->Log("Aborting pubsub loop...\n");
                            $pubsub->unsubscribe();
                        }
                    }
                    break;
            }
        }

        unset($pubsub);
    }

    public function setClientWorker($worker)
    {
        $this->worker = $worker;
    }


}