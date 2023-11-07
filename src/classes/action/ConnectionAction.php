<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use iutnc\touiteur\auth\Auth;
use iutnc\touiteur\bd\ConnectionFactory;

class ConnectionAction extends Action {

    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;
   
    public function __construct(){
        parent::__construct();
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    public function execute() : string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];
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
                $query = 'SELECT role from Users Where email = ?';
                $st = $pdo->prepare($query);
                $st->execute([$email]);
                $row = $st->fetchAll();

                $_SESSION['email'] = $email;
                $_SESSION['role'] = $row[0]['role'];
                var_dump($_SESSION);
                $html = "Auth OK";
            } else{
                $html = "Auth not ok";
            }
        }
        return $html;
    }
}