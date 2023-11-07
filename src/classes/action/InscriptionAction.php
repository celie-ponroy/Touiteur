<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use iutnc\touiteur\auth\Auth;
use iutnc\touiteur\bd\ConnectionFactory;

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
            var_dump($_POST);
            echo "<br>";
            $nom = filter_var($_POST['nom'],FILTER_SANITIZE_STRING);
            $prenom = filter_var($_POST['prenom'],FILTER_SANITIZE_STRING);

            $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $mdp = password_hash($_POST['pass'], PASSWORD_DEFAULT, ['cost'=> 12]);
            $role = 1;

            $pdo = ConnectionFactory::makeConnection();

            $query = "INSERT INTO Utilisateur (nom, prenom, password, email, role) VALUES (:nom, :prenom, :mdp, :email, :role)";


            $stmt = $pdo->prepare($query);

            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':prenom', $prenom);

            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':mdp', $mdp);
            $stmt->bindParam(':role', $role);

            if ($stmt->execute()) {
                $html = " ajout user<br> Email:".$email.", Nom:".$nom." Prenom:".$prenom;
            } else {
                $html = "INSERT ERROR: " . $stmt->errorInfo()[2];
            }

            $stmt = null;

            $pdo = null;
        }
        return $html;
    }
    
}