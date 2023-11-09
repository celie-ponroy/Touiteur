<?php

namespace iutnc\touiteur\touite;

use iutnc\touiteur\render\PaginerTouitesRender;

class ListTouite
{
    private array $tList;

    public function __construct($tList)
    {
        $this->tList = $tList;
    }

    public function afficher():string{
        $_SESSION['tList'] =  serialize($this->tList);

        $_SESSION['pMaxCount'] = ceil(count($this->tList)/PaginerTouitesRender::TOUITE_MAX_COUNT);
        $html = (new PaginerTouitesRender($this->tList))->render($_SESSION['pageCour']);

        $html .= "<h2>";
        if ($_SESSION['pageCour']-1 >= 0) {
            $html .= " <a class='' href = '?action=prev_page'> previous </a>";
        }

        for($i=1; $i<$_SESSION['pMaxCount']+1; $i++)
        {
            $html .= " <a class='' href = '?action=page&page_num=". $i ."'>" . $i . " </a>";
        }


        if ($_SESSION['pageCour']+1 < $_SESSION['pMaxCount']) {
            $html .=  "<a class='' href = '?action=next_page'> next </a>";
        }



        $html .= "</h2>";

        return $html;
    }


}