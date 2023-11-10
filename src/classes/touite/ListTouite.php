<?php

namespace iutnc\touiteur\touite;

use iutnc\touiteur\render\PaginerTouitesRender;

/**
 * Class ListTouite
 */
class ListTouite
{
    const MAX_PAGES = 5;
    private array $tList;

    /**
     * Constructeur
     * @param $tList array tableau de touites
     */
    public function __construct($tList)
    {
        $this->tList = $tList;
    }


    /**
     * Méthode afficher qui affiche la liste des touites
     * @return string code html
     */
    public function afficher():string{

        //on récupere la liste des touites
        $_SESSION['tList'] =  serialize($this->tList);

        //nombre de pages total
        $_SESSION['pMaxCount'] = ceil(count($this->tList)/PaginerTouitesRender::TOUITE_MAX_COUNT);
        $html = (new PaginerTouitesRender($this->tList))->render($_SESSION['pageCour']);

        //si pageCour >= 1 alors on affiche button prevPage
        $html .= "<h2 class='container-pagination'>";
        if ($_SESSION['pageCour']-1 >= 0) {
            $html .= " <a class='pagination' href = '?action=prev_page'> < </a>";
        }

        $total_pages = $_SESSION['pMaxCount'];
        $pCour = $_SESSION['pageCour']+1;

        //nb de pages qu'on affiche avant et apres la pageCour
        $half_max_display = intval(self::MAX_PAGES / 2);
        $start_page = max(1, $pCour - $half_max_display);
        $end_page = min($total_pages, $pCour + $half_max_display);

        //si pageCour < half_max_display alors on affiche les pages de 1 à max_pages
        if ($pCour - $half_max_display < 1) {
            $start_page = 1;
            $end_page = min(self::MAX_PAGES, $total_pages);
        }

        //si pageCour > total_pages - half_max_display alors on affiche les pages de total_pages - max_pages à total_pages
        if ($pCour + $half_max_display > $total_pages) {
            $end_page = $total_pages;
            $start_page = max(1, $total_pages - self::MAX_PAGES + 1);
        }

        //si avant trop de pages
        if ($start_page > 1) {
            $html .= "... ";
        }

        //l'ajout des pages
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $pCour) {
                $html .= " <a class='paginationcourante' href = '?action=page&page_num=". $i ."'>" . "$i" . "</a>";
            } else {
                $html .= " <a class='pagination' href = '?action=page&page_num=". $i ."'>" . $i . " </a>";
            }
        }

        //si apres trop de pages
        if ($end_page < $total_pages) {
            $html .= "...";

        }

        //si pageCour < MaxPages alors on affiche button nextPage
        if ($_SESSION['pageCour']+1 < $_SESSION['pMaxCount']) {
            $html .=  "<a class='pagination' href = '?action=next_page'> > </a>";
        }



        $html .= "</h2>";

        return $html;
    }


}