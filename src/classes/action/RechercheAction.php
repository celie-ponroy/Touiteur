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
            $recherche = filter_var($_POST['research'],FILTER_SANITIZE_STRING);
            $this->tag = $recherche;
            //recherche par tag
    //            $res = '';
            if($recherche[0] === '#'){
                $tag = new Tag(substr($recherche, 1));
                $taggedTouites = $tag->findTaggedTw();

                $html = (new ListTouite($taggedTouites))->afficher();
            }
            else{
                $taggedTouites = new UserAuthentifie($recherche);
//                var_dump($taggedTouites);
                $html =  (new ListTouite ($taggedTouites->getTouites()))->afficher();

            }
        }
        else{
            $recherche = $this->tag;
            $taggedTouites = new UserAuthentifie($recherche);
            $html =  (new ListTouite ($taggedTouites->getTouites()))->afficher();
        }

    return $html;

    }

//    public function __sleep(){
//        return array('tag');
//    }
}

