<?php

namespace iutnc\touiteur\action;
use iutnc\touiteur\auth\Auth;
use iutnc\touiteur\user\UserAuthentifie;

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
      * Affiche le profil de l'utilisateur
      */
    public function execute(): string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];
        $user = unserialize($_SERVER['User']);
        $html .= '<h1> @'.$user->get('email').'</h2>';
        $html .= '<h2>Abonéss:<h2>';
        $html .= '<div>';;
        $listeabo = $user->listeAbo();
        foreach ($listeabo as $abo) {
            $html .= '<li>'.$abo['prenom'].' '.$abo['nom'].'  @'.$abo['email'].'</li>';
        }
        $html .= '</div>';
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