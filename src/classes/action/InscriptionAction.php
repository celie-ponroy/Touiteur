<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;

/**
 * class InscriptionAction
 */
class InscriptionAction extends Action {

    /**
     * Constructeur
     */
    public function __construct(){
        parent::__construct();

    }

    /**
     * Méthode execute renvoie l'affichage du formulaire d'inscription
     * @return string code html
     */
    public function execute() : string{
        $debutform = " <form id='f1' action='?action=inscription' method='post'>
                    <h2>Registration</h2>
                    <p>Name :</p>
                    <input type='text' placeholder='Name' name='nom'>
                    <p>Surname :</p>
                    <input type='text' placeholder='Surname' name='prenom'>
                    <p>Email address :</p>
                    <input type='email' placeholder='Email address' name='email'>
                    <p>Password :</p>
                    <input type='password' placeholder='Password' name='pass'>
                    <p>Confirm password:</p>
                    <input type='password' placeholder='Confirm password' name='passconfirm'>
                     <button type='submit'>Submit</button>";
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];
        if($methode ==='GET'){
            $html =$debutform."</form>";
        }else if ($methode === 'POST') {   
          
            $nom = filter_var($_POST['nom'],FILTER_SANITIZE_STRING);
            $prenom = filter_var($_POST['prenom'],FILTER_SANITIZE_STRING);
            $mdp1 = password_hash($_POST['pass'], PASSWORD_DEFAULT, ['cost'=> 12]);
            $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);

            
            if(!empty($email)&&!empty($mdp1)&&!empty($_POST['passconfirm'])&&!empty($nom)&&!empty($prenom)){
                
                if($_POST['pass']===$_POST['passconfirm']){
                    try {
                        $html = UserAuthentifie::inscription($nom, $prenom, $email, $mdp1);
                        $usAuth = new UserAuthentifie($email);
                        $usAuth->connectUser();
                        $_SESSION["email"]= $email;
                        header('Location: index.php?action=liste_touite');
                    }catch(\PDOException $ex){
                        $html =$debutform."
                        <p class='form-error'>User arleady sign in.</p>
                        </form>";
                    }
                    
                }else{
                    $html =$debutform."
                    <p class='form-error'>Passwords do not match.</p>
                    </form>";
                }
               
            }else{
                $html =$debutform."
                        <p class='form-error'>All fields must be completed.</p>
                        </form>";
            }
        }
        return $html;
    }
    
}