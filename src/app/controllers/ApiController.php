<?php

class ApiController extends \Phalcon\Mvc\Controller
{
    public $response;

    /**
     * @var \Predis\Client
     */
    public $predis;

    public function initialize()
    {
        $this->response = new stdClass();
        $this->response->status = '';
        $this->predis = PredisManager::GetMasterPredis();
    }

    public function indexAction()
    {
        echo "Working";
        exit();
    }

    public function pingAction()
    {
        $this->response->ping = 'pong';
        $this->sendResponse();
    }

    public function statusAction()
    {
        $worker_status = $this->getWorkerStatus();

        $this->response->activeWorkers = $worker_status['active']." /" .$worker_status['all'];
        $this->response->requestsPerSecond = number_format($this->predis->get('stats.cps'), 0);
        $this->response->userCpu =  number_format($this->predis->get('stats.cpu'), 1);
        $this->response->sysCpu = 0; //rand(0,40);

        $this->response->userMemory = 10;


        $this->sendResponse();
    }

    public function modeAction()
    {
        $current_mode = $this->predis->get('system.mode');
        if($this->request->isGet())
        {
            $this->response->mode = $current_mode;
        }
        else if($this->request->isPost())
        {
            $data = (object)json_decode($this->request->getRawBody());
            $new_mode = $data->mode;

            if($current_mode == $new_mode)
            {
                $this->sendError("Mode Already Selected");
            }

            if($current_mode)
            {
                $mode = ModeManager::getInstance()->createMode($current_mode);
                $mode->masterTeardown();
            }


            $mode = ModeManager::getInstance()->createMode($new_mode);
            $mode->masterSetup();

            $this->predis->set('system.mode', $new_mode);
            $this->predis->publish('system.changes', 1);
        }
        else
        {
            $this->sendError("Only GET/POST Allowed");
        }

        $this->sendResponse();
    }



    public function sendResponse()
    {
        header('Content-Type: application/json');
        $this->response->status = 'success';
        echo json_encode($this->response);
        exit();
    }

    public function sendError($msg = '')
    {
        header('Content-Type: application/json');
        header('HTTP/1.1 400 Bad Request', true, 400);
        $this->response->status = 'error';
        $this->response->err_msg = $msg;
        echo json_encode($this->response);
        exit();
    }

    /** Helper Functions */

    public function getWorkerStatus()
    {
        $report = array('active' => 0, 'idle' => 0, 'all' => 0);

        $status_data = $this->predis->hgetall('client.status');

        foreach($status_data as $data_json)
        {
            $worker_status = (object)json_decode($data_json);
            if($worker_status->working)
            {
                $report['active']++;
            }
            else
            {
                $report['idle']++;
            }
            $report['all']++;
        }

        return $report;
    }
}