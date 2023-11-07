<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use iutnc\touiteur\auth\Auth;
use iutnc\touiteur\bd\ConnectionFactory;

class InscriptionAction extends Action {

    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;
    public function __construct(){
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }
    public function execute() : string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];
        if($methode ==='GET'){
            $html =" <form id='f1' action='?action=inscription' method='post'>
            <input type='text' placeholder='<nom>' name='nom'>
            <input type='email' placeholder='<email>' name='email'>
            <input type='text' placeholder='<pass>' name='pass'>
            <button type='submit'>Valider</button>
          </form>";
        }else if ($methode === 'POST') {
            var_dump($_POST);
            echo "<br>";
            $nom = filter_var($_POST['nom'],FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $mdp = password_hash($_POST['pass'], PASSWORD_DEFAULT, ['cost'=> 12]);
            $date = (new \DateTime())->format('Y-m-d');
            var_dump($date);
            $role = 1;

            $pdo = ConnectionFactory::makeConnection();

            $query = "INSERT INTO USERS (name, email, password, dateRegister, role) VALUES (:nom, :email, :mdp, :date, :role)";

            $stmt = $pdo->prepare($query);

            $stmt->bindParam(':nom', $nom);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':mdp', $mdp);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':role', $role);

            if ($stmt->execute()) {
                $html = " ajout user<br> Email:".$email.", Nom:".$nom.", Date: ".$date;
            } else {
                $html = "INSERT ERROR: " . $stmt->errorInfo()[2];
            }

            $stmt = null;

            $pdo = null;
        }
        return $html;
    }
    
}