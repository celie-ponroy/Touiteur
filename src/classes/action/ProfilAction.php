<?php

namespace iutnc\touiteur\action;

/*
 * Classe ProfilAction qui permet d'accéder au profil d'un utilisateur et de pouvoir s'abonner à
 * son profil grâce à un bouton
 */
 class ProfilAction extends Action{

    //constructeur

    public function __construct(){
        parent::__construct();
    }

     /*
      * Affiche le profil de l'utilisateur (son nom, prénom et sa liste de touites)
      *  et permet de s'abonner à son profil
      */
    public function execute(): string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];

        if($methode ==='GET'){
            $html =" <form id='f1' action='?action=profil' method='post'>
            <input type='email' placeholder='<email>' name='email'>
            <button type='submit'>Valider</button>
          </form>";
        }else if ($methode === 'POST') {

            $email = filter_var($_POST['email'],FILTER_SANITIZE_EMAIL);

            //verification de la connection
            if(Auth::authenticate($email, $mdp)){
                //cas connecté
                $usAuth = new UserAuthentifie($email);
                $usAuth->connectUser();
                $html = "Vous êtes connecté.e ;)";
                $_SESSION["email"]= $email;
            } else{
                //cas échec
                $html = "La connection a échoué, le mot de passe ou l'adresse mail est incorecte";
            }
        }
        return $html;
    }
    /*
     * public function execute() : string{
        $db = ConnectionFactory::makeConnection();

        $sql ="SELECT * FROM Touite
        right join Abonnement on Touite.email = Abonnement.idSuivi
        where idAbonné = :email
        order by Touite.datePublication;";
        $resultset = $db->prepare($sql);
        $user = unserialize($_SESSION['User']);
        $resultset->bindParam(':email', $user->email);
        $resultset->execute();
        $html = "";
        foreach ($resultset->fetchAll() as $row) {
            $html.=("@".$row["email"]." : ".$row["texte"])."<br>";
        }
        return $html;
    }
     */
}