<?php
namespace iutnc\touiteur\render;

use iutnc\touiteur\touite\Touite;
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

                
        //entete
        $res= '<div class="touite-container"><header class="entete">' .
                '<a class="nomuser" href="?action=???????????">' . $this->touite->__get('user')->__get('prenom').'</a>' . //nom
                '<i> @' . $this->touite->__get('user')->__get('nom') . '</i>' . //identifiant
                '<strong class="date"> · ' . $this->touite->__get('date')->format('d M. H:i') . '</strong>' . //date
                '<br> ' .
                '</header>';


        $res .= '<p class="text">' . htmlspecialchars($this->touite->__get('texte'), ENT_QUOTES) . '</p>';
        $tags = $this->touite->__get('tags');
        if($tags!==null){
            $res.='<div class=trend-container>';
            foreach ($this->touite->__get('tags') as &$t) {
                $res .= '<a class="trend" href="?action=????????????">#' . $t . '</a>';
            }
            $res.='</div>';
        }
        // Fermez la balise <a> avec ID "compact" ici
        $res .= '<a id="compact" class="TouiteShow" href="?action=touite-en-detail&id=' . $this->touite->__get('idtouite') . '">voir plus</a><p class="underline"></p></div><br>';

        return $res;

    }

    /**
     * function renderLong : rendu HTML Long
     */
    public function renderLong():string {
        // Code HTML pour l'affichage en mode long
        $res= '<div class="touite-container"><header class="entete">' .
        '<a class="nomuser" href="?action=???????????">' . $this->touite->__get('user')->__get('prenom').'</a>' . //nom
        '<i> @' . $this->touite->__get('user')->__get('nom') . '</i>' . //identifiant
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

