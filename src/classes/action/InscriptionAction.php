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
                    <h2>Inscription</h2>
                    <p>Nom :</p>
                    <input type='text' placeholder='Nom' name='nom'>
                    <p>Prénom :</p>
                    <input type='text' placeholder='Prénom' name='prenom'>
                    <p>Adresse e-mail :</p>
                    <input type='email' placeholder='Adresse e-mail' name='email'>
                    <p>Mot de passe :</p>
                    <input type='password' placeholder='Mot de passe' name='pass'>
                    <p>Confirmer mot de passe :</p>
                    <input type='password' placeholder='Confirmer mot de passe' name='passconfirm'>

                    <button type='submit'>Valider</button>
                    </form>";
        }else if ($methode === 'POST') {   
          
            $nom = filter_var($_POST['nom'],FILTER_SANITIZE_STRING);
            $prenom = filter_var($_POST['prenom'],FILTER_SANITIZE_STRING);
            $mdp1 = password_hash($_POST['pass'], PASSWORD_DEFAULT, ['cost'=> 12]);
            $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);

            
            if(!empty($email)&&!empty($mdp1)&&!empty($_POST['passconfirm'])&&!empty($nom)&&!empty($prenom)){
                
                if($_POST['pass']===$_POST['passconfirm']){
                    $html = UserAuthentifie::inscription($nom,$prenom,$email,$mdp1);
                    $usAuth = new UserAuthentifie($email);
                    $usAuth->connectUser();
                    $_SESSION["email"]= $email;
                    header('Location: index.php?action=liste_touite');
                }else{
                    $html =" <form id='f1' action='?action=inscription' method='post'>
                    <h2>Inscription</h2>
                    <p>Nom :</p>
                    <input type='text' placeholder='Nom' name='nom'>
                    <p>Prénom :</p>
                    <input type='text' placeholder='Prénom' name='prenom'>
                    <p>Adresse e-mail :</p>
                    <input type='email' placeholder='Adresse e-mail' name='email'>
                    <p>Mot de passe :</p>
                    <input type='password' placeholder='Mot de passe' name='pass'>
                    <p>Confirmer mot de passe :</p>
                    <input type='password' placeholder='Confirmer mot de passe' name='passconfirm'>
                    <button type='submit'>Valider</button>
                    <p class='form-error'>Les mots de passes ne correspondent pas.</p>
                    </form>";
                }
               
            }else{
                $html =" <form id='f1' action='?action=inscription' method='post'>
                        <h2>Inscription</h2>
                        <p>Nom :</p>
                        <input type='text' placeholder='Nom' name='nom'>
                        <p>Prénom :</p>
                        <input type='text' placeholder='Prénom' name='prenom'>
                        <p>Adresse e-mail :</p>
                        <input type='email' placeholder='Adresse e-mail' name='email'>
                        <p>Mot de passe :</p>
                        <input type='password' placeholder='Mot de passe' name='pass'>
                        <p>Confirmer mot de passe :</p>
                        <input type='password' placeholder='Confirmer mot de passe' name='passconfirm'>
                        <button type='submit'>Valider</button>
                        <p class='form-error'>Tous les champs doivent être remplis.</p>
                        </form>";
            }
        }
        return $html;
    }
    
}