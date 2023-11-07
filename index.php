<?php
declare(strict_types=1);
session_start();

require_once 'vendor/autoload.php';

use iutnc\touiteur\dispatch\Dispatcher;
use iutnc\touiteur\bd\ConnectionFactory;

use iutnc\touiteur\user\UserAuthentifie;

ConnectionFactory::setConfig('conf/conf.ini');



        $disp = new Dispatcher();
        $disp->run();
        //if (isset($_SESSION)) var_dump($_SESSION['User']->getTouites());
?>