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
    /**Permet de se connecter */

    public function execute() : string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];

        if(isset($_SESSION['User'])){
            return "Already auth";
        }

        if($methode ==='GET'){
            $html =" <form id='f1' action='?action=connection' method='post'>
                    <h2>Connection</h2>
                    <p>Email address :</p>
                    <input type='email' placeholder='Email address' name='email'>
                    <p>Password :</p>
                    <input type='password' placeholder='Password' name='mdp'>
                    <button type='submit'>Submit</button>
                    </form>";
        }else if ($methode === 'POST') {

            $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);
            $mdp = $_POST['mdp'];
    
            if(!empty($email)&&!empty($mdp)){
                //verification de la connection
                if(Auth::authenticate($email, $mdp)){
                    //cas connecté
                    $usAuth = new UserAuthentifie($email);
                    $usAuth->connectUser();
                    $html = "Vous êtes connecté.e ;)";
                    $_SESSION["email"]= $email;
                    header('Location: index.php?action=liste_touite');
                } else{
                    $html =" <form id='f1' action='?action=connection' method='post'>
                            <h2>Connection</h2>
                            <p>Email address :</p>
                            <input type='email' placeholder='Email address' name='email'>
                            <p>Mot de passe :</p>
                            <input type='password' placeholder='Password' name='mdp'>
                            <button type='submit'>Submit</button>
                            
                   <p class='form-error'>The connection failed, the password or email address is incorrect.</p>
                    </form>";
                }
            }else{
                $html =" <form id='f1' action='?action=connection' method='post'>
                <h2>Connection</h2>
                <p>Email address :</p>
                <input type='email' placeholder='Email address' name='email'>
                <p>Password :</p>
                <input type='password' placeholder='Password' name='mdp'>
                <button type='submit'>Submit</button>
                
                <p class='form-error'>All fields must be completed.</p>
                </form>";
            }
        }
        return $html;
    }
}