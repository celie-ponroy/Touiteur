<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

use iutnc\touiteur\dispatch\Dispatcher;

$disp = new Dispatcher();
$disp->run();
?>