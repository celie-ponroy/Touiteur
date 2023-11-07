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
                $pdo = ConnectionFactory::makeConnection();
                $query = 'SELECT role from utilisateur Where email = ?';
                $st = $pdo->prepare($query);
                $st->execute([$email]);
                $role = $st->fetchAll();

                $query = 'SELECT nom from utilisateur Where email = ?';
                $st = $pdo->prepare($query);
                $st->execute([$email]);
                $nom = $st->fetchAll();

                $query = 'SELECT prenom from utilisateur Where email = ?';
                $st = $pdo->prepare($query);
                $st->execute([$email]);
                $prenom = $st->fetchAll();

                $_SESSION['User'] = serialize(new UserAuthentifie($email, $nom[0]['nom'], $prenom[0]['prenom'], $role[0]['role']));

                $html = "Auth OK";
            } else{
                $html = "Auth not ok";
            }
        }
        return $html;
    }
}