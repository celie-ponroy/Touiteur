<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use iutnc\touiteur\auth\Auth;
use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;

class ConnectionAction extends Action {


    public function __construct(){
        parent::__construct();

    }

    public function execute() : string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];

        if(isset($_SESSION['email'])){
            return "Already auth";
        }

        if($methode ==='GET'){
            $html =" <form id='f1' action='?action=connection' method='post'>
            <input type='email' placeholder='<email>' name='email'>
            <input type='text' placeholder='<mdp>' name='mdp'>
            <button type='submit'>Valider</button>
          </form>";
        }else if ($methode === 'POST') {

            $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $mdp = $_POST['mdp'];

            //Auth OK
            if(Auth::authenticate($email, $mdp)){

                $usAuth = new UserAuthentifie($email);
                $usAuth->connectUser();
                $html = "Auth OK";
                $_SESSION["email"]= $email;
            } else{
                $html = "Auth not ok";
            }
        }
        return $html;
    }
}