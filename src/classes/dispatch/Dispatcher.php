<?php
declare(strict_types=1);

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action\RechercheAction;
use iutnc\touiteur\action\ConnectionAction;
use iutnc\touiteur\action\InscriptionAction;
use iutnc\touiteur\action\TouitePost;
use iutnc\touiteur\action\ListeTouiteAction;
use iutnc\touiteur\action\TouiteDetailAction;

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
        switch($this->action){
            case 'connection':
                $connection = new ConnectionAction();
                $html = $connection->execute();
                break;
            case 'inscription':
                $inscription = new InscriptionAction();
                $html = $inscription->execute();
                break;
            case 'recherche':
                $recherche = new RechercheAction();
                $html = $recherche->execute();
                break;
            case 'touite-en-detail':
                $touiteEnDetail = new TouiteDetailAction();
                $html = $touiteEnDetail->execute();

            case 'touite-post':
                $touitepost = new TouitePost();
                $html = $touitepost->execute();
                break;

            case 'liste_touite':
                $listeT = new ListeTouiteAction();
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
                    <h2 class='logo'><a href='index.php'><img src='mon_image.jpg' ></a></h2> 
                   
                    <a class='action' href = '?action=connection'><img src='mon_image.jpg' > Connection </a><br>
                    <a class='action' href = '?action=inscription'><img src='mon_image.jpg' > Inscription </a><br>
                    <a class='action' href = '?action=recherche'><img src='mon_image.jpg' > Explore</a><br>
                    <a class='action' href = '?action=touite-en-detail'><img src='mon_image.jpg' > Touite en détail</a><br>
                    <a class='action' href = '?action=liste_touite'> <img src='mon_image.jpg' >For u page</a><br>
                    <a class='action-post' href = '?action=touite-post'> Post</a><br>
                   

                </nav>
                
                <div class='content'>
                    $html
                </div>


                <div class='foruser'>

                    <div class='research'>

                    </div>

                    <div class='research'>
                        <a class='research' href = '?action=recherche'> Recherche</a><br>
                    </div>

                    <div class='research'>
                    </div>

                </div>
            </div>
            </body>
        </html>";
    }
}
