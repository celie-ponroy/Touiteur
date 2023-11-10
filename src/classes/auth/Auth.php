<?php

declare(strict_types=1);

namespace iutnc\touiteur\auth;
//use  iutnc\touiter\exception\AuthException;
use  iutnc\touiteur\bd\ConnectionFactory as ConnectionFactory;
use PDO;

/**
 * Class Auth
 */
class Auth{
    private ConnectionFactory $connection;
    public function __construct(){
        $this->connection = new ConnectionFactory();
        $this->connection::setConfig('../../../conf/conf.ini');
    }

    /**
     * Méthoe authenticate qui vérifie si le mot de passe est correct
     * @param string $email email de l'utilisateur
     * @param string $passwd2check mot de passe à vérifier
     * @return bool true si le mot de passe est correct, false sinon
     */
    public static function authenticate(string $email,string $passwd2check): bool {

        //on récupère le mot de passe de l'utilisateur
        $db = ConnectionFactory::makeConnection();

        //on récupère le mot de passe de l'utilisateur
        $sql = "SELECT password FROM Utilisateur WHERE email = ? ";
        $resultset = $db->prepare( $sql );
        $resultset->bindParam(1,$email);
        $resultset->execute();

        //on vérifie si le mot de passe est correct
        $ligne = $resultset->fetch(PDO::FETCH_ASSOC);

        //si l'email n'existe pas
        if(!isset($ligne['password'])){
            return false;
        }else{
            //si l'email existe on vérifie le mot de passe
            $hash = $ligne['password'];
            return password_verify($passwd2check, $hash);
        }
        
    }
}
