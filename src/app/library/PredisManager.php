<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jcarmony
 * Date: 10/15/13
 * Time: 11:17 AM
 * To change this template use File | Settings | File Templates.
 */

// Get the Singleton of the Predis Instance
class PredisManager
{
    static private $_master_predis;
    static private $_client_predis;

    private function __construct(){}

    /**
     * @return Predis\Client
     */
    static public function GetMasterPredis()
    {
        if(!self::$_master_predis)
        {
            self::$_master_predis = new Predis\Client(REDIS_MASTER_CONN);
        }

        return self::$_master_predis;
    }

    /**
     * @return Predis\Client
     */
    static public function GetClientPredis()
    {
        if(!self::$_client_predis)
        {
            self::$_client_predis = new Predis\Client(REDIS_CLIENT_CONN);
        }

        return self::$_client_predis;
    }

}