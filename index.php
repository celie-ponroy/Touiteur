<?php
session_start();

require_once 'vendor/autoload.php';

use iutnc\touiteur\dispatch\Dispatcher;
use iutnc\touiteur\bd\ConnectionFactory;

ConnectionFactory::setConfig('conf/conf.ini');

(new Dispatcher())->run();



