<?php
/**
 * Created by JetBrains PhpStorm.
 * User: justin
 * Date: 4/26/12
 * Time: 9:25 AM
 * To change this template use File | Settings | File Templates.
 */

echo "**** INIT ****\n";

chdir(dirname(__FILE__));

echo "Starting Bootstrap...\n";

require '../bootstrap.php';

$internal_id = $argv[1];

echo "Starting Client Worker $internal_id...\n";

$master = new Client($internal_id);
$master->Run();