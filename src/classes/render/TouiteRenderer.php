<?php
namespace iutnc\touiteur\render;

use iutnc\touiteur\touite\Note;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\user\UserAuthentifie;

class TouiteRenderer implements Renderer{

    //déclarations des attributs
    private Touite $touite;

    /**
     * constructor
     */
    public function __construct(Touite $touite) {
        $this->touite = $touite;
    }


    public function render($selector):string{
        if($selector===Renderer::COMPACT){
            return $this->renderCompact();
        }elseif($selector==Renderer::LONG){
            return $this->renderLong();
        }else{
            return "unknow";
        }
    }
    /**
     * function renderCompact : rendu HTML Compact
     */
    public function renderCompact():string {
        // Code HTML pour l'affichage compact
        $methode = $_SERVER['REQUEST_METHOD'];
        $actionUrl = $_GET['action'];
    
        //entete
        $res= '<div class="touite-container"><header class="entete">' .
                '<a class="nomuser">' . $this->touite->__get('user')->__get('prenom').'</a>' . //nom
                '<i> @' . $this->touite->__get('user')->__get('nom') . ' </i> ' . //identifiant
                '<strong class="date"> · ' . $this->touite->__get('date')->format('d M. H:i') . '</strong>' . //date
                '<br> ';

        // Bouton Follow/Unfollow
        $user = UserAuthentifie::getUser();
        $userToFollow = $this->touite->__get('user');
        $followText= '<button class="follow"  type="submit">Follow</button>';



        if ($user !== null && $user->__get('email') !== $userToFollow->__get('email')) {
            if ($user->etreAbonneUser($userToFollow)) {
                $followText= '<button class="unfollow"  type="submit">Unfollow</button>';
            }

            $formAction = $followText === '<button class="follow"  type="submit">Follow</button>' ? '<button class="follow"  type="submit">Follow</button>' : '<button class="unfollow"  type="submit">Unfollow</button>';

            $res .= '<form class="follow-form" action="?action=follow&us=' . $userToFollow . '" method="post">'.
                '<input type="hidden" name="redirect_to" value="' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) . '">'
                .$followText.
                '</form>';

        }

        $res .= '</header>';

        $res .= '<p class="text">' . html_entity_decode($this->touite->__get('texte'), ENT_QUOTES, 'UTF-8') . '</p>';
        $tags = $this->touite->__get('tags');
        if($tags!==null){
            $res.='<div class=trend-container>';
            //tags
            foreach ($this->touite->__get('tags') as &$t) {
                $res .= "<a class='trend' " . "href=?action=recherche&tag=%23$t>#" . $t . '</a>';
            }
            $res.='</div>';
        }


            if (isset($_SESSION["User"])){
                $user=unserialize($_SESSION["User"]);

                $noter=new Note($user);
            }

        //fonctions du touite

        $res.=' <div class="fonctions">';    
        //button delete
        if($this->touite->appartientUserAuth()){
            $res .= '<form class="follow-form" action="?action=touite-del&id=' . $this->touite->__get('idtouite'). '" method="post">'.
                    '<input type="hidden" name="redirect_to" value="' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) . '">' .
                    '<button class="delete-button"type="submit">' . 'delete' . '</button>'.
                    '</form>';
        }
        $res.='</div>';
        $res .= '<a id="compact" class="TouiteShow" href="?action=touite-en-detail&id=' . $this->touite->__get('idtouite') . '">voir plus</a>';
        $res .= '</div><br>';

        return $res;
    }

    /**
     * function renderLong : rendu HTML Long
     */
    public function renderLong():string {
        // Code HTML pour l'affichage compact
        $methode = $_SERVER['REQUEST_METHOD'];
        $actionUrl = $_GET['action'];
    
        //entete
        $res= '<div class="touite-container"><header class="entete">' .
                '<a class="nomuser">' . $this->touite->__get('user')->__get('prenom').'</a>' . //nom
                '<i> @' . $this->touite->__get('user')->__get('nom') . ' </i> ' . //identifiant
                '<strong class="date"> · ' . $this->touite->__get('date')->format('d M. H:i') . '</strong>' . //date
                '<br> ';

        // Bouton Follow/Unfollow
        $user = UserAuthentifie::getUser();
        $userToFollow = $this->touite->__get('user');
        $followText= '<button class="follow"  type="submit">Follow</button>';



        if ($user !== null && $user->__get('email') !== $userToFollow->__get('email')) {
            if ($user->etreAbonneUser($userToFollow)) {
                $followText= '<button class="unfollow"  type="submit">Unfollow</button>';
            }

            $formAction = $followText === '<button class="follow"  type="submit">Follow</button>' ? '<button class="follow"  type="submit">Follow</button>' : '<button class="unfollow"  type="submit">Unfollow</button>';

            $res .= '<form class="follow-form" action="?action=follow&us=' . $userToFollow . '" method="post">'.
                '<input type="hidden" name="redirect_to" value="' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) . '">'
                .$followText.
                '</form>';

        }

        $res .= '</header>';
        $res .= '<p class="text">' . html_entity_decode($this->touite->__get('texte'), ENT_QUOTES, 'UTF-8') . '</p>';


        if($this->touite->__get('pathpicture')!==''){
            $res.='<img class="touite-image" src="'.$this->touite->__get('pathpicture').'" >';
                
        }

        $tags = $this->touite->__get('tags');
        if($tags!==null){
            $res.='<div class=trend-container>';

            foreach ($this->touite->__get('tags') as &$t) {
                $res .= "<a class='trend' " . "href=?action=recherche&tag=%23$t>#" . $t . '</a>';
            }
            $res.='</div>';
        }



            if (isset($_SESSION["User"])){
                $noter=new Note(UserAuthentifie::getUser());
            }

        //fonctions du touite
        if($methode === 'GET' && UserAuthentifie::isUserConnected()){
        $res.=' <div class="fonctions">
        <form method="post" action="?action=touite-en-detail&id='.$this->touite->__get('idtouite').'">
            
            <button type="submit" name="action" value="like'.$this->touite->__get('idtouite').'">
            <img class="imNote" src="'.$noter->__getLikeInitial($this->touite->__get('idtouite'))[0].'" ></button>'.
            '<p>'.Note::getnbLike($this->touite->__get('idtouite')).'</p>'.

            '<button type="submit" name="action" value="dislike'.$this->touite->__get('idtouite').'">
            <img class="imNote" src="'.$noter->__getLikeInitial($this->touite->__get('idtouite'))[1].'" ></button>' .
            '<p>'.Note::getnbDislike($this->touite->__get('idtouite')).'</p>  </form>';

            if( $this->touite->appartientUserAuth() ){
                $res .= '<form class="follow-form" action="?action=touite-del&id=' . $this->touite->__get('idtouite'). '" method="post">'.
                    '<input type="hidden" name="redirect_to" value="' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) . '">' .
                    '<button class="delete-button" type="submit">' . 'delete' . '</button>'.
                    '</form>';
            }
            $res.='</div>';
       
            
        }elseif ($methode === 'POST' && UserAuthentifie::isUserConnected()) {
            $action = isset($_POST['action']) ? $_POST['action'] : '';

            $noteUser=-8;
            if ($action === 'like'.$this->touite->__get('idtouite')){
                $noteUser=1;

             }elseif ($action === 'dislike'.$this->touite->__get('idtouite')) {
                $noteUser=(-1);
             }


            $arraynote=$noter->noterTouite($this->touite->__get('idtouite'), $noteUser);

            $res.=' <div class="fonctions">
            <form method="post" action="?action=touite-en-detail&id='.$this->touite->__get('idtouite').'">
            <button type="submit" name="action" value="like'.$this->touite->__get('idtouite').'">';

            /*$res.='<img class="imNote" src="image/like_empty.svg" >';*/
             if($arraynote[2]==='ajouter-like'||$arraynote[2]==='ajouter-like-dislike')
                $res.= '<img class="imNote" src="image/like_full.svg" >';
            else
                $res.= '<img class="imNote" src="'.$noter->__getLikeInitial($this->touite->__get('idtouite'))[0].'" >';
            
            $res.='</button><p>';
          
           
            $res.=$arraynote[0];
            //echo $this->touite->__get('idtouite');
           
            $res.='</p>';


            $res.='<button type="submit" name="action" value="dislike'.$this->touite->__get('idtouite').'">';
            
            if($arraynote[2]==='ajouter-dislike'||$arraynote[2]==='ajouter-dislike-like'){
                $res.= '<img class="imNote" src="image/dislike_full.svg" >';
            }else
                $res.= '<img class="imNote" src="'.$noter->__getLikeInitial($this->touite->__get('idtouite'))[1].'" >';
                /*$res.='<img class="imNote" src="image/dislike_empty.svg" >';*/
            $res.='</button><p>';

            $res.=$arraynote[1];
            //echo $this->touite->__get('idtouite');

            
            //*
            $res.='</p> </form>';

            //button delete
            if($this->touite->appartientUserAuth()){
            $res .= '<form class="follow-form" action="?action=touite-del&id=' . $this->touite->__get('idtouite'). '" method="post">'.
                    '<input type="hidden" name="redirect_to" value="' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) . '">' .
                    '<button class="delete-button" type="submit">' . 'delete' . '</button>'.
                    '</form>';
            }

        $res.='</div>';
            
        }
        
        $res .=    '</div><br>';

        return $res;

    }

    public static function renderListe(array $touites):string{
        $html = '';
        foreach ($touites as $t){
            $html.= (new TouiteRenderer($t))->render(Renderer::COMPACT);
        }
        return $html;
    }
}

