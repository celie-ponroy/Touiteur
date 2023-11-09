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
        var_dump(count($this->tList));
        $html = (new PaginerTouitesRender($this->tList))->render($_SESSION['pageCour']);

        $html .= "<h2>  Pages:";
        if ($_SESSION['pageCour']-1 >= 0) {
            $html .= " <a class='action' href = '?action=prev_page'> prev </a>";
        }

        for($i=1; $i<$_SESSION['pMaxCount']+1; $i++)
        {
            $html .= " <a class='action' href = '?action=page&page_num=". $i ."'>" . $i . " </a>";
        }


        if ($_SESSION['pageCour']+1 < $_SESSION['pMaxCount']) {
            $html .=  "<a class='action' href = '?action=next_page'> next </a>";
        }


        $html .= "</h2>";

        return $html;
    }


}