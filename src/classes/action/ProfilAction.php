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
        $user = unserialize($_SESSION['User']);
        $html .= '<h1> @'.$user->__get('email').'</h2>';
        // affichage de la liste des abonés:
        $html .= '<h2>Followers:<h2>';
        $html .= '<div>';;
        $listeabo = $user->listeAbo();
        $avoirabo = false;
        foreach ($listeabo as $abo) {
            $avoirabo = true;
            $html .= '<li>'.$abo['prenom'].' '.$abo['nom'].':  @'.$abo['email'].'</li>';
        }
        if(!$avoirabo){
            $html .= "<p>You don't have any yet</p>";
        }
        $html .= '</div>';
        //affichage des statistiques:
        $touites = $user->getTouites();
        $stat = 0;
        foreach ($touites as $t) {
            $stat += $t->statistique();
        }
        $nombret = count($touites);
        if($nombret!=0){
            $stat = $stat/ count($touites);    
        }
        
        $html .= "<br><p> Indice de popularité de vos touites : $stat <br>";


        return $html;
    }
}