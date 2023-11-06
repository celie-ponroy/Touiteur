<?php
declare(strict_types=1);

namespace iutnc\deefy\dispatch;
use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastAction;
use iutnc\deefy\action\AddUserAction;
use iutnc\deefy\action\SigninAction;

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
            case 'add-user':
                $addUserAction = new AddUserAction();
                $html = $addUserAction->execute();
                break;
            case 'add-playlist':
                $addPlaylist = new AddPlaylistAction();
                $html = $addPlaylist->execute();
                break;
            case 'add-podcasttrack':
                $addpodcast = new AddPodcastAction();
                $html = $addpodcast->execute();
                break;
            case "signin":
                $signin = new SigninAction();
                $html = $signin->execute();
                break;
            default:
                echo 'bienvenue<br>';
                break;

        }

        echo "<!DOCTYPE html>
        <html lang = fr>
            <head>
                <title> Titre </title>
            </head>
            <body>
            <a href = '?action=add-user'>inscription </a><br>
            <a href = '?action=add-playlist'>ajout d'une playlist </a><br>
            <a href = '?action=add-podcasttrack'>ajout d'une track </a><br>
            <a href = '?action=signin'>signin </a><br>
            $html
            </body>
            </html>";
    }
}
