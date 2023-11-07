<?php

declare(strict_types=1);

namespace iutnc\touiteur\auth;
//use  iutnc\touiter\exception\AuthException;
use  iutnc\touiteur\bd\ConnectionFactory as ConnectionFactory;
use PDO;
class Auth{
    private ConnectionFactory $connection;
    public function __construct(){
        $this->connection = new ConnectionFactory();
        $this->connection::setConfig('../../../conf/conf.ini');
    } 


    public static function authenticate(string $email,string $passwd2check): bool {
        $db = ConnectionFactory::makeConnection();
        $sql = "select password from Utilisateur where email = ? ";
        $resultset = $db->prepare( $sql );
        $resultset->bindParam(1,$email);
        $resultset->execute();
       
        $ligne = $resultset->fetch(PDO::FETCH_ASSOC);
        $hash = $ligne['password'];
//        print("1 : ".$hash."<br>2 : ".$passwd2check."<br>");
        return password_verify($passwd2check, $hash);
    }
}
