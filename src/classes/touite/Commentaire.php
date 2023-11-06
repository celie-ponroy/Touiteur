<?php
declare(strict_types= 1);

namespace iutnc\touiter\touite;
class Commentaire extends Touite{
    private $touite;
    

    public function __construct(String $texte ,Touite $touite, User $user){
        parent::__construct($texte, $user);
        $this->touite = $touite;   
    }

}