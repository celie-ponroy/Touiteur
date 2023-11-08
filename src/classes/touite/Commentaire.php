<?php
declare(strict_types= 1);

namespace iutnc\touiter\touite;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\user\User;
use iutnc\touiteur\user\UserAuthentifie;

/**
 * class Comentaire
*/
class Commentaire extends Touite{
    private $touite;
    
    /**
     * constructeur
     */
    public function __construct(int $id ,Touite $touite, UserAuthentifie $user){
        parent::__construct($user,$id);
        $this->touite = $touite;   
    }




}