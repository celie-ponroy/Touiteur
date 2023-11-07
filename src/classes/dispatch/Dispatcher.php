<?php
declare(strict_types=1);

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action\RechercheAction;
use iutnc\touiteur\action\ConnectionAction;
use iutnc\touiteur\action\InscriptionAction;
use iutnc\touiteur\action\TouitePost;
use iutnc\touiteur\action\ListeTouiteAction;

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

            case 'touite-post':
                $touitepost = new TouitePost();
                $html = $touitepost->execute();
                break;

            case 'liste_touite':
                $listeT = new ListeTouiteAction();
                $html = $listeT->execute();
                break;
            default:
                echo 'Bienvenue<br>';
                break;

        }

        echo "<!DOCTYPE html>
        <html lang = fr>
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Accueil - Touiteur</title>
                <link rel="stylesheet" type="text/css" href="css/index_stylecss">
            </head>
            <body>
            <header>
                <h2 class="logo"><a href='index.php'>Touiteur</a></h2>
            </header>
            <nav class='navigation'>
                <a href = '?action=connection'> Connection </a><br>
                <a href = '?action=inscription'> Inscription </a><br>
                <a href = '?action=recherche'> Recherche</a><br>
                <a href = '?action=touite-post'> Poster un touite</a><br>
            </nav>
                <a href = '?action=liste_touite'> For u page</a><br>
                $html       
            </body>
        </html>";
    }
}
