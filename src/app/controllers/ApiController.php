<?php

class ApiController extends \Phalcon\Mvc\Controller
{
    public function init()
    {
        echo "Init Executed!";
    }

    public function indexAction()
    {
        echo "Working";
        exit();
    }

}