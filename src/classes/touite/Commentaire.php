<?php
declare(strict_types= 1);

namespace iutnc\touiter\touite;
use iutnc\touiteur\user\User;

/**
 * class Comentaire
*/
class Commentaire extends Touite{
    private $touite;
    
    /**
     * constructeur
     */
    public function __construct(String $texte ,Touite $touite, User $user){
        parent::__construct($texte, $user);
        $this->touite = $touite;   
    }




}