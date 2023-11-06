<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
class InscriptionAction extends Action {

    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;
   
    public function __construct(){
        
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }
    
    public function execute() : string{//a modifier
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];
        if($methode ==='GET'){
            $html =" <form id='f1' action='?action=add-user' method='post'>
            <input type='text' placeholder='<nom>' name='nom'>
            <input type='number' placeholder='<age>' name='age'>
            <input type='email' placeholder='<email>' name='email'>
            <input type='text' placeholder='<genre>'name='genre'>
            <button type='submit'>Valider</button>
          </form>";
        }else if ($methode === 'POST') {
            var_dump($_POST);
            echo "<br>";
            $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $age = filter_var($_POST['age'],FILTER_SANITIZE_NUMBER_INT);
            $genre = filter_var($_POST['genre'],FILTER_SANITIZE_STRING);
            $html = " ajout util traitement du formulaire<br> Email:".$email.", Age:".$age." ans, Genre musical:".$genre;}
        
        return $html;
    }
    
}
?>