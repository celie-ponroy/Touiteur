<?php
declare(strict_types= 1);

namespace iutnc\touiter\touite;
use iutnc\touiteur\user\User;
/**
 * class Touite
 */
class Touite{
    protected $texte;//contenu du touite
    protected $user ; //l'auteur
    private $date;//date de publication du touite
    /**
     * contructeur
     */
    function __construct(string $texte, User $user){
        $this->texte = $texte;
        $this->user = $user;
        $date = new \DateTime();
    }

    /**
     * toString 
     */
    function __toString(){
        $res = "@".$this->user;
        return $res."<br>\n".$this-> texte;
    }


}

?>