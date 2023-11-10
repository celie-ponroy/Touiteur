<?php

namespace iutnc\touiteur\user;

/**
 * Class User
 */
class User{

    /**
     * Constructeur de la classe User
     */
    public function __construct()
    {

    }

    /**
     * Méthode permettant de récupérer un utilisateur à partir de son email
     * @param $name string nom de la propriété
     * @return User l'utilisateur en question
     */

    public function __get($name): mixed{
        if(property_exists($this, $name)){
            return $this->$name;
        }else{
            echo "Get invalide";
            echo"$name";
            return null;
        }
    }

    /**
     * Méthode isAdmin qui renvoie true si l'utilisateur est un administrateur
     * @return bool true si l'utilisateur est un administrateur
     */
    function isAdmin():bool{
        return false;
    }

    /**
     * Méthode __toString qui renvoie l'email de l'utilisateur
     * @return User|mixed l'email de l'utilisateur
     */
    function __toString(){
        return $this->email;
    }

}




