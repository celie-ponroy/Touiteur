<?php
declare(strict_types=1);

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action\AccueilAction;
use iutnc\touiteur\action\DeleteTAction;
use iutnc\touiteur\action\FollowAction;
use iutnc\touiteur\action\FollowTagAction;
use iutnc\touiteur\action\RechercheAction;
use iutnc\touiteur\action\ConnectionAction;
use iutnc\touiteur\action\DeconnAction;
use iutnc\touiteur\action\InscriptionAction;
use iutnc\touiteur\action\TouitePost;
use iutnc\touiteur\action\ListeTouiteAction;
use iutnc\touiteur\action\ProfilAction;
use iutnc\touiteur\action\RentabiliserAction;
use iutnc\touiteur\action\UserListeTouitesAction;
use iutnc\touiteur\action\TouiteDetailAction;
use iutnc\touiteur\user\UserAdmin;
use iutnc\touiteur\user\UserAuthentifie;

/**
 * Class Dispatcher
 */

class Dispatcher {
    private string $action;

    /**
     * Constructeur
     */
    public function __construct( ){
        //on récupère l'action
        if( isset($_GET['action'])){
            $this->action = $_GET['action'];
        }else{
            $this->action = '';
        }
    }

    /**
     * Méthode run qui exécute l'action
     * @return void
     */
    public function run( ): void {

        $html = '';
        $html_recherche='';
        //on récupère l'action
        switch($this->action){
            case 'deconnection':
                $_SESSION['CurrentPage'] = "Disconn";
                $deco = new DeconnAction();
                $html = $deco->execute();
                break;

            case 'connection':
                $_SESSION['CurrentPage'] = "Connect";
                $connection = new ConnectionAction();
                $html = $connection->execute();
                break;

            case 'inscription':
                $_SESSION['CurrentPage'] = "Insc";
                $inscription = new InscriptionAction();
                $html = $inscription->execute();
                break;

            case 'recherche':
                $_SESSION['CurrentPage'] = "PRech";
                $_SESSION['pageCour']=0;
                $recherche = new RechercheAction();
                $html = $recherche->execute();
                $_SESSION['ListAaff'] = serialize($recherche);
                break;

            case 'touite-en-detail':
                $touiteEnDetail = new TouiteDetailAction();
                $html = $touiteEnDetail->execute();
                break;

            case 'touite-post':
                //si l'utilisateur n'est pas connecté on le redirige vers la page de connection
                if (!UserAuthentifie::isUserConnected()){
                    $_SESSION['CurrentPage'] = "PAcc";
                    $html = "<h2>To access this page please log in: </h2> <br>";
                    $html.= "<a class='action' href = '?action=connection'><img  class='img-action' src='image/profile.svg' > Connection </a><br>";
                    break;
                }
                $_SESSION['CurrentPage'] = "TPost";
                $touitepost = new TouitePost();
                $html = $touitepost->execute();
                break;

            case 'touite-del':
                (new DeleteTAction())->execute();
                break;

            case 'liste_touite':
                $_SESSION['CurrentPage'] = "Home";
                $_SESSION['pageCour']=0;

                $listeT = new ListeTouiteAction();
                $_SESSION['ListAaff']= serialize($listeT);
                $html = $listeT->execute();
                break;

            case 'user_liste_touite':
                $_SESSION['CurrentPage'] = "MesT";
                $_SESSION['pageCour']=0;

                $UlisteT = new UserListeTouitesAction();
                $_SESSION['ListAaff'] = serialize($UlisteT);
                $html = $UlisteT->execute();
                break;

            case 'followTag':
                (new FollowTagAction())->execute();
                break;

            case 'page_accueil':
                $_SESSION['CurrentPage'] = "PAcc";
                //si l'utilisateur est connecté on affiche la page d'accueil
                if(UserAuthentifie::isUserConnected()) {
                    $pageA = new AccueilAction();
                    $html = $pageA->execute();
                }
                else{
                    $html = "<h2>To access this page please log in: </h2> <br>";

                    $html.= "<a class='action' href = '?action=connection'><img  class='img-action' src='image/profile.svg' > Connection </a><br>";

                }
                break;

            case 'follow':
                (new FollowAction())->execute();
                break;

            case 'page':
                $_SESSION['pageCour'] = !isset($_GET['page_num'])  ? $_SESSION['pageCour'] : $_GET['page_num']-1 ;
                $listeT = unserialize($_SESSION['ListAaff']);
                $html = $listeT->execute();
                break;

            case 'next_page':
                $_SESSION['pageCour'] += 1;

                $listeT = unserialize($_SESSION['ListAaff']);
                $html = $listeT->execute();
                break;

            case 'prev_page':
                $_SESSION['pageCour'] -= 1;

                $listeT = unserialize($_SESSION['ListAaff']);
                $html = $listeT->execute();
                break;

            case 'fa1':
                $html = UserAdmin::tendances();
                break;

            case 'fa2':
                $html = UserAdmin::trouveInfluenceurs();
                break;

            case 'user_narcissique':
                $profil = new ProfilAction();
                $html = $profil->execute();
                break;

            case 'rentabiliser':
                $renta = new RentabiliserAction();
                        $html = $renta->execute();
                
                break;
        }
        //Partie HTML

        echo "<!DOCTYPE html>
        <html lang = fr>
            <head>
                <meta charset='UTF-8'>
                <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                <meta name='viewport' content='width=device-width', initial-scale='1.0'>
                <title>Home - Touiteur</title>
                <link rel='stylesheet' type='text/css' href='css/index_style.css'>
                <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Pacifico|Dancing+Script|Patrick+Hand|Shadows+Into+Light|Amatic+SC&display=swap'>
            </head>
            <body>


            <div class='tableau'>
            
                <nav class='navigation'>

                    <a class='logo-action' href='index.php?action=liste_touite'><img class='imgLogo' src='image/icon-oiseau.png' ><h2 class='logo'>Touiteur</h2></a> 
                    <div class='container-action-button'>
                    

                        <a class='action' href = '?action=liste_touite'> <img class='img-action' src='image/home.svg' > Home</a><br>
                        <a class='action' href = '?action=page_accueil'><img class='img-action' src='image/loupe.svg' > Explore</a><br>";
                        if (UserAuthentifie::isUserConnected()){
                            echo "<a class='action' href = '?action=user_liste_touite'><img class='img-action' src='image/mestouites.svg' >  My Touites</a><br>
                            <a class='action' href = '?action=user_narcissique'><img class='img-action' src='image/profile.svg' >  Profile</a><br>";
                            $user = unserialize($_SESSION['User']);
                            if($user->isAdmin()){
                               echo" <a class='action' href = '?action=rentabiliser'><img class='img-action' src='image/admin.svg' >  Administrator Page</a><br>";
                            }
                        }
                        echo"
                        
                    <a class='action-post' href = '?action=touite-post'> Post</a><br>
                    </div>


                    <div class='connexion'>";
                   if (UserAuthentifie::isUserConnected()){
                        echo "<a class='action-connect' href = '?action=deconnection'> Disconnection </a><br>";
                    }else{

                        echo "<a class='fonction-connect' href = '?action=connection'> Connection </a><br>";
                        echo"<a class='fonction-connect' href = '?action=inscription'> Inscription </a><br>";
                    }
                    echo "</div>
                   
                </nav>
                
                <div class='content'>
                    $html
                </div>


                <div class='foruser'>

                    

                    <div class='research'>

                        <form  action='?action=recherche' method='post'>
                        <input type='textarea' placeholder='Search' name='research'>
                        </form>

                        $html_recherche

                    </div>

                    <div class='list-trends'>";
                        echo UserAdmin::tendances();
                        echo "

                    </div>

                    <div class='other'>
                        <p class='titreTrends'>The most famous :<br>";
                         echo UserAdmin::trouveInfluenceurs();
                        echo"</p>
                    </div>

                </div>
            </div>
            </body>
        </html>";
    }
}
