<?php

class TemplateController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {

    }

    public function modeAction($mode_name)
    {
        $mode = ModeManager::getInstance()->createMode($mode_name);
        echo $mode->templateControlPanel();
        exit();
    }

}