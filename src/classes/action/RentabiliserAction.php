<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;

use iutnc\touiteur\user\UserAdmin;

class RentabiliserAction extends Action{

    public function __construct(?string $tag=null){
        parent::__construct();
    }
    
    public function execute() : string{
        $html   = "<h1>Back-office:</h1>";
        $html .= "<h2>Influencers:</h2>";
        $influ = UserAdmin::trouveInfluenceurs();
        $html .= $influ;
        $html .= "<h2>Trends:</h2>";
        $html .= UserAdmin::tendances();
       
        return $html;
    }
}