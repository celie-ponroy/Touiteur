<?php

namespace iutnc\touiteur\render;

use iutnc\touiteur\touite\Touite;

class PaginerTouitesRender
{

    public const TOUITE_MAX_COUNT = 4;
    public array $touites;

    public function __construct(array $touites)
    {
        $this->touites = $touites;
    }


    /**
     * return html d'un array des touite de la page
     */
    public function render($numpage): string
    {
        $html = "";
        $count = 0;
        $arrT = array();
        foreach ($this->touites as $t){
            //count = 1 element
            if ($count<$numpage*self::TOUITE_MAX_COUNT){
                $count++;
                continue;
            }

            array_push($arrT, $t);

            //end if count == MAX
            if (($count++)-($numpage*self::TOUITE_MAX_COUNT) >= self::TOUITE_MAX_COUNT-1){
                break;
            }

        }
        $html = TouiteRenderer::renderListe($arrT);
        return $html;
    }
}