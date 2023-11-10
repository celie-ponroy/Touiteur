<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use  iutnc\touiteur\bd\ConnectionFactory as ConnectionFactory;
use iutnc\touiteur\touite\ListTouite;
use iutnc\touiteur\touite\Touite;
use PDO;

/**
 * Class ListeTouiteAction
 */

class ListeTouiteAction extends Action {

    /**
     * Constructeur
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Méthode execute renvoie l'affichage de la liste des touites
     * @return string code html
     */
    public function execute() : string{
        //initialise le html
        $html = "";
        $db = ConnectionFactory::makeConnection();
        $touiteAafficher = array();
        $sql = "SELECT * FROM Touite
        left join Image on Image.idIm=Touite.idIm;";
        $resultset = $db->prepare($sql);
        //exécute la requète sql
        $resultset->execute();

        //affiche chaque Touite
        foreach ($resultset->fetchAll() as $row) {
            array_push($touiteAafficher, new Touite($row["idTouite"]));
        }
        $html = (new ListTouite($touiteAafficher))->afficher();

        return $html;
    }
}