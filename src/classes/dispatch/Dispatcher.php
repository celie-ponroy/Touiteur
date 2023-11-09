<?php
declare(strict_types=1);

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action\AccueilAction;
use iutnc\touiteur\action\RechercheAction;
use iutnc\touiteur\action\ConnectionAction;
use iutnc\touiteur\action\DeconnAction;
use iutnc\touiteur\action\InscriptionAction;
use iutnc\touiteur\action\SuivreAction;
use iutnc\touiteur\action\TouiteDeTagAction;
use iutnc\touiteur\action\TouitePost;
use iutnc\touiteur\action\ListeTouiteAction;
use iutnc\touiteur\action\UserListeTouitesAction;
use iutnc\touiteur\action\TouiteDetailAction;
use iutnc\touiteur\user\UserAuthentifie;


class Dispatcher {
    private string $action;
    public function __construct( ){
        if( isset($_GET['action'])){
            $this->action = $_GET['action'];
        }else{
            $this->action = '';
        }
    }
    public function run( ): void {//a modifier

        $html = '';
        $html_recherche='';
        switch($this->action){
            case 'deconnection':
                $deco = new DeconnAction();
                $html = $deco->execute();
                break;
            case 'connection':
                $connection = new ConnectionAction();
                $html = $connection->execute();
                break;
            case 'inscription':
                $inscription = new InscriptionAction();
                $html = $inscription->execute();
                break;
            case 'recherche':
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
                $touitepost = new TouitePost();
                $html = $touitepost->execute();
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
            case 'tag_liste_touite':
                $_SESSION['CurrentPage'] = "tagged_list";
                $_SESSION['pageCour'] = 0;

                $TaglisteT = new TouiteDeTagAction(1);
                $_SESSION['ListAaff'] = serialize($TaglisteT);
                $html = $TaglisteT->execute();
                break;

            case 'suivre_user':
                $followUser = new SuivreAction();
                $html = $followUser->execute();
                break;
            case 'page_accueil':
                if(UserAuthentifie::isUserConnected()) {
                    $pageA = new AccueilAction();
                    $html = $pageA->execute();
                }
                else{
                    $html = "<h2>Pour acceder à cette page veillez vous connecter:</h2> <br>";
                    $html.= "<a class='action' href = '?action=connection'><img src='mon_image.jpg' > Connection </a><br>";
                }
                break;
            case 'page':
                $_SESSION['pageCour'] =  $_GET['page_num']-1;
                $listeT = unserialize($_SESSION['ListAaff']);
                var_dump($listeT);
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
        }

        echo "<!DOCTYPE html>
        <html lang = fr>
            <head>
                <meta charset='UTF-8'>
                <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                <meta name='viewport' content='width=device-width', initial-scale='1.0'>
                <title>Accueil - Touiteur</title>
                <link rel='stylesheet' type='text/css' href='css/index_style.css'>
            </head>
            <body>


            <div class='tableau'>
            
                <nav class='navigation'>

                    <h2 class='logo'><a href='index.php'><img src='mon_image.jpg' >Touiteur</a></h2> 
                    <div class='container-action-button'>
                    <div class='connexion'>";
                   if (UserAuthentifie::isUserConnected()){
                        echo "<a class='action' href = '?action=deconnection'> Deconnection </a><br>"; 
                    }else{

                        echo "<a class='action' href = '?action=connection'> Connection </a><br>";
                        echo"<a class='action' href = '?action=inscription'><img src='mon_image.jpg' > Inscription </a><br>";
                    }
                    echo"

                    </div>
                        <a class='action' href = '?action=page_accueil'><img class='img-action' src='image/loupe.png' > Explore</a><br>
                        <a class='action' href = '?action=liste_touite'> <img class='img-action' src='image/home.png' > Home</a><br>
                        <a class='action' href = '?action=user_liste_touite'><img class='img-action' src='image/????????' >  Mes Touites</a><br>
                    <a class='action-post' href = '?action=touite-post'> Post</a><br>
                    </div>
                   
                </nav>
                
                <div class='content'>
                    $html
                </div>


                <div class='foruser'>

                    

                    <div class='research'>

                        <form  action='?action=recherche' method='post'>
                        <input type='textarea' placeholder='Chercher' name='research'>
                        </form>

                        $html_recherche

                    </div>

                    <div class='list-trends'>
                        <p>Tendances</p>
                    </div>

                    <div class='other'>
                        <p>Other</p>
                    </div>

                </div>
            </div>
            </body>
        </html>";
    }
}
