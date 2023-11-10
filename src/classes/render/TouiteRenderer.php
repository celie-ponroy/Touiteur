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
                '<a class="nomuser" href="?action=???????????">' . $this->touite->__get('user')->__get('prenom').'</a>' . //nom
                '<i> @' . $this->touite->__get('user')->__get('nom') . ' </i> ' . //identifiant
                '<strong class="date"> · ' . $this->touite->__get('date')->format('d M. H:i') . '</strong>' . //date
                '<br> ';

        // Bouton Follow/Unfollow
        $user = UserAuthentifie::getUser();
        $userToFollow = $this->touite->__get('user');
        $followText = 'Follow';



        if ($user !== null && $user->__get('email') !== $userToFollow->__get('email')) {
            if ($user->etreAbonneUser($userToFollow)) {
                $followText = 'Unfollow';
            }

            $formAction = $followText === 'Follow' ? 'Follow' : 'Unfollow';

            $res .= '<form class="follow-form" action="?action=follow&us=' . $userToFollow . '" method="post">'.
                '<input type="hidden" name="redirect_to" value="' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) . '">' .
                '<button class="follow"  type="submit">' . $followText . '</button>'.
                '</form>';

        }

        $res .= '</header>';

        $res .= '<p class="text">' . htmlspecialchars($this->touite->__get('texte'), ENT_QUOTES) . '</p>';
        $tags = $this->touite->__get('tags');
        if($tags!==null){
            $res.='<div class=trend-container>';
            //tags
            foreach ($this->touite->__get('tags') as &$t) {
                $res .= "<a class='trend' " . "href=?action=recherche&tag=%23$t>#" . $t . '</a>';
            }
            $res.='</div>';
        }
        $user = unserialize($_SESSION["User"]);
        $noter = new Note($user);

        //fonctions du touite
        if($methode === 'GET'){
            
        $res.=' <div class="fonctions">
        <form method="post" action="?action='.$actionUrl.'">
            <button type="submit" name="action" value="like'.$this->touite->__get('idtouite').'"><img class="imNote" src="image/like_empty.svg" ></button>' .
            '<p>'.$this->touite->__get('nblikes').'</p>' .
            '<button type="submit" name="action" value="dislike'.$this->touite->__get('idtouite').'"><img class="imNote" src="image/dislike_empty.svg" ></button>' .
            '<p>'.$this->touite->__get('nbdislike').'</p>  </form>'
        .'</div>';
            
        }elseif ($methode === 'POST') {
            $action = isset($_POST['action']) ? $_POST['action'] : '';
            
            $noteUser=-8;
            if ($action === 'like'.$this->touite->__get('idtouite')){
                $noteUser=1;
             }elseif ($action === 'dislike'.$this->touite->__get('idtouite')) {
                $noteUser=(-1);
             }

            $arraynote=$noter->noterTouite($this->touite->__get('idtouite'), $noteUser);

            $res.=' <div class="fonctions">
            <form method="post" action="?action='.$actionUrl.'">
            <button type="submit" name="action" value="like'.$this->touite->__get('idtouite').'">';
            
            if($arraynote[2]==='ajouter-like'||$arraynote[2]==='ajouter-like-dislike')
                $res.= '<img class="imNote" src="image/like_full.svg" >';
            else
                $res.= '<img class="imNote" src="image/like_empty.svg" >';
            
            $res.='</button><p>';
          
           
            $res.=$arraynote[0];
            //echo $this->touite->__get('idtouite');
           
            $res.='</p>';


            $res.='<button type="submit" name="action" value="dislike'.$this->touite->__get('idtouite').'">';
            if($arraynote[2]==='ajouter-dislike'||$arraynote[2]==='ajouter-dislike-like')
                $res.= '<img class="imNote" src="image/dislike_full.svg" >';
            else
                $res.= '<img class="imNote" src="image/dislike_empty.svg" >';
            
            $res.='</button><p>';

            $res.=$arraynote[1];
            //echo $this->touite->__get('idtouite');
            
            
            $res.='</p> </form>'

        .'</div>';
           
        }
         /*etc....... */
        // Fermez la balise <a> avec ID "compact" ici
        $res .= '<a id="compact" class="TouiteShow" href="?action=touite-en-detail&id=' . $this->touite->__get('idtouite') . '">voir plus</a>';
        //button delete
        if($this->touite->appartientUserAuth()){
            $res .= '<form class="follow-form" action="?action=touite-del&id=' . $this->touite->__get('idtouite'). '" method="post">'.
//            $res .= '<a  href="?action=touite-del&id=' . $this->touite->__get('idtouite') . '">delete post</a>'.
                '<input type="hidden" name="redirect_to" value="' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) . '">' .
                '<button type="submit">' . 'delete post' . '</button>'.
                '</form>';
        }
        $res .=    '<p class="underline"></p></div><br>';

        return $res;

    }

    /**
     * function renderLong : rendu HTML Long
     */
    public function renderLong():string {
        // Code HTML pour l'affichage en mode long
        $methode = $_SERVER['REQUEST_METHOD'];
        $actionUrl = $_GET['action'];

        $res= '<div class="touite-container"><header class="entete">' .

        '<a class="nomuser" href="?action=???????????">' . $this->touite->__get('user')->__get('prenom').'</a>' . //nom
        '<i> @' . $this->touite->__get('user')->__get('nom') . ' </i> ' . //identifiant

        '<strong class="date"> · ' . $this->touite->__get('date')->format('d M. H:i') . '</strong>' . //date
        '<br> ' .
        '</header>';

        $res .= '<p class="text">' . htmlspecialchars($this->touite->__get('texte'), ENT_QUOTES) . '</p>';
        $res .= '<img class="touite-image" src="'.$this->touite->__get('pathpicture').'" >';

        if($this->touite->__get('tags')!==null){
            $res.='<div class=trend-container>';
        
            foreach ($this->touite->__get('tags') as &$t) {
                $res .= '<a class="trend" href="?action=????????????">#' . $t . '</a>';
            }
            $res.='</div>';
        }

        $user=unserialize($_SESSION["User"]);

        $noter=new Note($user);

        //fonctions du touite
        if($methode === 'GET'){

        $res.=' <div class="fonctions">
        <form method="post" action="?action='.$actionUrl.'&id='.$this->touite->__get('idtouite').'">
            <button type="submit" name="action" value="like'.$this->touite->__get('idtouite').'">Like</button>' .
            '<p>'.$this->touite->__get('nblikes').'</p>' .
            '<button type="submit" name="action" value="dislike'.$this->touite->__get('idtouite').'">Dislike</button>' .
            '<p>'.$this->touite->__get('nbdislike').'</p>  </form>'
        .'</div>';

 
            if( $this->touite->appartientUserAuth() ){
                $res .= '<form class="follow-form" action="?action=touite-del&id=' . $this->touite->__get('idtouite'). '" method="post">'.
                    '<input type="hidden" name="redirect_to" value="' . htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES) . '">' .
                    '<button type="submit">' . 'delete post' . '</button>'.
                    '</form>';
            }
        }elseif ($methode === 'POST') {
            $action = isset($_POST['action']) ? $_POST['action'] : '';
            
            $noteUser=-8;
            if ($action === 'like'.$this->touite->__get('idtouite')){
                $noteUser=1;
                
             }elseif ($action === 'dislike'.$this->touite->__get('idtouite')) {
                $noteUser=(-1);
             }
             echo'<p>'.$noteUser.'</p>';

            $res.=' <div class="fonctions">
            <form method="post" action="?action='.$actionUrl.'&id='.$this->touite->__get('idtouite').'">
            <button type="submit" name="action" value="like'.$this->touite->__get('idtouite').'">Like</button>' .
            '<p>';
          
            $arraynote=$noter->noterTouite($this->touite->__get('idtouite'), $noteUser);
           
            $res.=$arraynote[0];
           
            $res.='</p>';




            $res.='<button type="submit" name="action" value="dislike'.$this->touite->__get('idtouite').'">Dislike</button>' .
            '<p>';
            $res.=$arraynote[1];
            //echo $this->touite->__get('idtouite');
            
            
            $res.='</p> </form>';

            $res .= '</div>';
           
        }

        // Fermez la balise <a> avec ID "compact" ici
        $res .= '<p class="underline"></p></div><br>';

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

