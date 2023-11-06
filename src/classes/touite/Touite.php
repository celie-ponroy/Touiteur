<?php
namespace iutnc\touiter\touite;
declare(strict_types= 1);
class Touite{
    protected $texte;//contenu du touite
    protected $user ; //l'auteur
    
    function __construct(string $texte, User $user){
        $this->texte = $texte;
        $this->user = $user;
    }
    function __toString(){
        $res = "@".$this->user;
        return $res."<br>\n".$this-> texte;
    }
}

?>