<?php

namespace iutnc\touiteur\user;

class User{

    public function __construct()
    {

    }


    public function __get($name): mixed{
        if(property_exists($this, $name)){
            return $this->$name;
        }else{
            echo "Get invalide";
            echo"$name";
            return null;
        }
    }
    function isAdmin():bool{
        return false;
    }

    function __toString(){
        return $this->email;
    }

}




