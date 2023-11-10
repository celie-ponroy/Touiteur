<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use iutnc\touiteur\touite\ListTouite;
use iutnc\touiteur\touite\Tag;
use iutnc\touiteur\user\UserAuthentifie;

class RechercheAction extends Action {

    private ?string $tag;

    public function __construct(?string $tag=null){
        parent::__construct();
        $this->tag = $tag;
    }
    
    public function execute() : string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];

        if ($methode === 'POST') {
            $recherche = filter_var($_POST['research'], FILTER_SANITIZE_STRING);
            $this->tag = $recherche;
            $_SESSION['tag'] = $this->tag;
        }
        else if($methode === 'GET' && $this->tag === null){
            $recherche =  !isset($_GET['tag']) ?  $_SESSION['tag'] : $_GET['tag'] ;
            $this->tag = $recherche;
        }
        else{
           $recherche = $this->tag;
        }

        //recherche par tag
        if($recherche[0] === '#'){
            //user peut follow si auth
            if ( UserAuthentifie::isUserConnected() )  {
                $t = new  Tag(substr($this->tag, 1));
                $followText = $t->isTagFollowed(UserAuthentifie::getUser()) ? 'Unfollow ' : 'Follow ';
                $html .= '<form class="follow-form" action="?action=followTag&tag=%23' . substr($recherche, 1) . '" method="post">' .
                    '<input type="hidden" name="redirect_to" value="' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) . '">' .
                    '<button class="followtag-tag" type="submit">' . "$followText".substr($recherche, 1) . '</button>' .
                    '</form>';
            }
            $tag = new Tag(substr($recherche, 1));
            $taggedTouites = $tag->findTaggedTw();

            $html .= (new ListTouite($taggedTouites))->afficher();
        }
        //recherche par user
        else{
            //user peut follow si auth et n'est pas le meme user
            if ( UserAuthentifie::isUserConnected() && UserAuthentifie::getUser()->__get('email') !== $recherche && UserAuthentifie::userExists($recherche))  {
                $user = new  UserAuthentifie($recherche);
                $followText = (UserAuthentifie::getUser())->etreAbonneUser($user) ? 'Unfollow ' : 'Follow ';
                $html .= '<form class="follow-form" action="?action=follow&us=' . $recherche . '" method="post">' .
                    '<input type="hidden" name="redirect_to" value="' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) . '">' .
                    '<button class="followtag-tag" type="submit">' . "$followText".substr($recherche, 1) . '</button>' .
                    '</form>';
            }
            if(UserAuthentifie::userExists($recherche)) {
                $taggedTouites = new UserAuthentifie($recherche);

                $html .= (new ListTouite ($taggedTouites->getTouites()))->afficher();
            }
            else{
                $html .= 'Nothing was found';
            }
        }
        unset($_GET['tag']);

    return $html;

    }


}

