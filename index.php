<?php
session_start();

require_once 'vendor/autoload.php';
//require_once 'src/classes/dispatch/Dispatcher.php';
use iutnc\touiteur\dispatch\Dispatcher;
use iutnc\touiteur\bd\ConnectionFactory;

ConnectionFactory::setConfig('conf/conf.ini');
//pw789


(new Dispatcher())->run();




//
//// Придуманный пароль
//$password = "a1";
//
//// Генерация хеша для этого пароля
//$hash = password_hash($password, PASSWORD_DEFAULT);
//
//echo "Пароль: " . $password . "<br>";
//echo "Хеш: " . $hash . "<br>";
//
//// Предположим, что $userInput - это пароль, введенный пользователем
//$userInput = "a1";
//
//// Предположим, что $hash - это хеш, который вы получили и сохранили ранее
////$hash = '$2y$10$umi/KUCBeDZCLtqvUmVJPe/W/DSsz64LIgJZpEq8qpGp44Lu3NCbS';
//
//// Проверка введенного пароля
//if (password_verify($userInput, $hash)) {
//    echo "Пароль верный! <br>";
//} else {
//    echo "Пароль неверный! <br>";
//}