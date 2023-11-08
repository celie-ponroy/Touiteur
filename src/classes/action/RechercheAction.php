<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;

class RechercheAction extends Action {

    public function __construct(){
        parent::__construct();
    }
    
    public function execute() : string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];
        
        if ($methode === 'POST') {
         
            $recherche = filter_var($_POST['research'],FILTER_SANITIZE_STRING);
            $html='<p>'.$recherche.'</p>';
    }
    return $html;
   
    
}
}
?>