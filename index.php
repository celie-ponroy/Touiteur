<?php
<<<<<<< HEAD
declare(strict_types=1);
=======
session_start();
>>>>>>> CreerUser_Insc

require_once 'vendor/autoload.php';

use iutnc\touiteur\dispatch\Dispatcher;
<<<<<<< HEAD

$disp = new Dispatcher();
$disp->run();
?>
=======
use iutnc\touiteur\bd\ConnectionFactory;

ConnectionFactory::setConfig('conf/conf.ini');

(new Dispatcher())->run();



>>>>>>> CreerUser_Insc
