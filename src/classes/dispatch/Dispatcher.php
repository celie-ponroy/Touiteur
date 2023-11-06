<?php
declare(strict_types=1);

namespace iutnc\touiteur\dispatch;

use iutnc\touiteur\action\RechercheAction;
use iutnc\touiteur\action\ConnectionAction;
use iutnc\touiteur\action\InscriptionAction;

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
                $addUserAction = new ConnectionAction();
                $html = $addUserAction->execute();
                break;
            case 'inscription':
                $addPlaylist = new InscriptionAction();
                $html = $addPlaylist->execute();
                break;
            case 'recherche':
                $addpodcast = new RechercheAction();
                $html = $addpodcast->execute();
                break;
            default:
                echo 'bienvenue<br>';
                break;

        }

        echo "<!DOCTYPE html>
        <html lang = fr>
            <head>
                <title> Touiter </title>
            </head>
            <body>
                <a href = '?action=connection'> connection </a><br>
                <a href = '?action=inscription'> inscription </a><br>
                <a href = '?action=recherche'> recherche</a><br>
            </body>
        </html>";
    }
}
