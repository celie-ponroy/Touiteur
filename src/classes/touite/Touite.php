<?php
declare(strict_types= 1);
namespace iutnc\touiter\touite;

class Touite{
    protected $texte;//contenu du touite
    protected $user ; //l'auteur
    private $date;//date de publication du touite
    
    function __construct(string $texte, User $user){
        $this->texte = $texte;
        $this->user = $user;
        $date = new \DateTime();
    }
    function __toString(){
        $res = "@".$this->user;
        return $res."<br>\n".$this-> texte;
        
    }
}

?>