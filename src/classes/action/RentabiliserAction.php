<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;

use iutnc\touiteur\user\UserAdmin;
use iutnc\touiteur\user\UserAuthentifie;

class RentabiliserAction extends Action{

    public function __construct(?string $tag=null){
        parent::__construct();
    }
    /** renvoi l'affichage du back office */
    public function execute() : string{
        if(UserAuthentifie::isUserConnected()){
            $user = unserialize($_SESSION['User']);
            if($user->isAdmin()){
                $html   = "<h1>Back-office:</h1>";
                $html .= "<h2>Influencers:</h2>";
                $influ = UserAdmin::trouveInfluenceurs();
                $html .= $influ;
                $html .= UserAdmin::tendances();
        }
        
        }else{
            $html ="<h1>!!!ACCESS FORBIDEN!!!</h1>" ; 
        }
    return $html;
}
}