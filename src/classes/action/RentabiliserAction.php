<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;

class RentabiliserAction extends Action{

    public function __construct(?string $tag=null){
        parent::__construct();
    }
    
    public function execute() : string{
        $html = "<h1>Administrator Page</h1>";
        $html .= "<h2>Influencers:</h2>";
        $html .= "<h2>Trends:</h2>";
        $html .= "<h2>Back-office:</h2>";
        return $html;
    }
}