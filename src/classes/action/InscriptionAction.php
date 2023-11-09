<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;

class InscriptionAction extends Action {

    public function __construct(){
        parent::__construct();

    }
    public function execute() : string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];
        if($methode ==='GET'){
            $html =" <form id='f1' action='?action=inscription' method='post'>
            <input type='text' placeholder='<nom>' name='nom'>
            <input type='text' placeholder='<prenom>' name='prenom'>
            <input type='email' placeholder='<email>' name='email'>
            <input type='text' placeholder='<pass>' name='pass'>
            <button type='submit'>Valider</button>
          </form>";
        }else if ($methode === 'POST') {
            echo "<br>";
            $nom = filter_var($_POST['nom'],FILTER_SANITIZE_STRING);
            $prenom = filter_var($_POST['prenom'],FILTER_SANITIZE_STRING);

            $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $mdp = password_hash($_POST['pass'], PASSWORD_DEFAULT, ['cost'=> 12]);
            $html = UserAuthentifie::inscription($nom,$prenom,$email,$mdp);
        }
        return $html;
    }
    
}