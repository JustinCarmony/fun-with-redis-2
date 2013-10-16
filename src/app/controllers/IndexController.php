<?php

class IndexController extends \Phalcon\Mvc\Controller
{

    public function indexAction()
    {
        $mode_names = ModeManager::getInstance()->mode_names;
        $this->view->setVar("mode_names", $mode_names);
    }

}