<?php

namespace iutnc\touiteur\render;

use iutnc\touiteur\touite\Touite;

/**
 * Class TouiteRenderer
 */
class PaginerTouitesRender
{

    public const TOUITE_MAX_COUNT = 8;
    public array $touites;

    /**
     * Constructeur
     * @param array $touites tableau de touites
     */

    public function __construct(array $touites)
    {
        $this->touites = $touites;
    }


    /**
     * Méthode render qui affiche le code html
     * @param $numpage int numero de la page
     * @return string code html
     */
    public function render($numpage): string
    {
        $html = "";
        $count = 0;
        $arrT = array();
        //on parcourt les touites
        foreach ($this->touites as $t){
            //les touites avant
            if ($count<$numpage*self::TOUITE_MAX_COUNT){
                $count++;
                continue;
            }

            //on ajoute la touite à la liste
            array_push($arrT, $t);

            //si on a atteint le nombre max de touites par page
            if (($count++)-($numpage*self::TOUITE_MAX_COUNT) >= self::TOUITE_MAX_COUNT-1){
                break;
            }

        }
        $html = TouiteRenderer::renderListe($arrT);
        return $html;
    }
}