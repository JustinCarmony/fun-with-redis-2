<?php

class ModeManager
{
    static private $_instance = null;

    public $mode_names = array();

    private function __construct()
    {
        $this->loadModes();
    }

    /**
     * @return ModeManager
     */
    static public function getInstance()
    {
        if(!self::$_instance)
        {
            self::$_instance = new ModeManager();
        }

        return self::$_instance;
    }


    public function loadModes()
    {
        $mode_dir = __DIR__.'/Modes';
        $dir_items = scandir($mode_dir);

        foreach($dir_items as $file_name)
        {
            if(substr($file_name, -4) == '.php' && stripos($file_name, 'BaseMode') === false)
            {
                $class_name = str_ireplace('.php', '', $file_name);
                $this->mode_names[] = $class_name;
            }
        }
    }

    /**
     * @param $mode_name
     * @return Modes\BaseMode
     */
    public function createMode($mode_name)
    {
        $class_name = "\\Modes\\".$mode_name;
        return new $class_name();
    }

}