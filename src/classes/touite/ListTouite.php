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
            $html .= " <a class='pagination' href = '?action=prev_page'> < </a>";
        }

        for($i=1; $i<$_SESSION['pMaxCount']+1; $i++)
        {
                if(isset($_GET["page_num"])&&$i==$_GET["page_num"]){
                    $html .= " <a class='paginationCourante' href = '?action=page&page_num=". $i ."'>" . $i . "</a>";
                }else{
                    $html .= " <a class='pagination' href = '?action=page&page_num=". $i ."'>" . $i . " </a>";
                }
           
        }


        if ($_SESSION['pageCour']+1 < $_SESSION['pMaxCount']) {
            $html .=  "<a class='pagination' href = '?action=next_page'> > </a>";
        }



        $html .= "</h2>";

        return $html;
    }


}