<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use  iutnc\touiteur\bd\ConnectionFactory as ConnectionFactory;
use iutnc\touiteur\touite\ListTouite;
use iutnc\touiteur\touite\Touite;
use PDO;

class ListeTouiteAction extends Action {

    public function __construct(){
        parent::__construct();
    }
    
    public function execute() : string{
        $html = "";
        $db = ConnectionFactory::makeConnection();
        $touiteAafficher = array();
        $sql = "SELECT * FROM Touite
        left join Image on Image.idIm=Touite.idIm;";
        $resultset = $db->prepare($sql);
        //exécute la requète sql
        $resultset->execute();
        //initialise le html
        //affiche chaque Touite
        foreach ($resultset->fetchAll() as $row) {
            array_push($touiteAafficher, new Touite($row["idTouite"]));
        }
        $html = (new ListTouite($touiteAafficher))->afficher();

        return $html;
    }
}