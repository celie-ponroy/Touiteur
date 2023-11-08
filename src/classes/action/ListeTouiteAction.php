<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use  iutnc\touiteur\bd\ConnectionFactory as ConnectionFactory;
use iutnc\touiteur\render\Renderer;
use iutnc\touiteur\render\TouiteRenderer;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\user\User;
use iutnc\touiteur\user\UserAuthentifie;
use PDO;

class ListeTouiteAction extends Action {

    public function __construct(){
        parent::__construct();
        ConnectionFactory::setConfig("conf/conf.ini");
    }
    
    public function execute() : string{

        $db = ConnectionFactory::makeConnection();
        
        $sql ="SELECT * FROM Touite
        left join Image on Image.idIm=Touite.idIm;";
        $resultset = $db->prepare($sql);
        //exécute la requète sql
        $resultset->execute(); 
        //initialise le html
        $html = "";
        //affiche chaque Touite
        foreach ($resultset->fetchAll() as $row) {
            
            // Affiche les hashtags si il y en a
            $sql1 = "SELECT t.libelle
            FROM tag t
            JOIN tag2touite t2t ON t.idTag = t2t.idTag
            WHERE t2t.idTouite = :id_touite";

            $hashtags = $db->prepare($sql1);
            $hashtags->bindParam(':id_touite', $row["idTouite"], PDO::PARAM_INT);
            $hashtags->execute();
            $tags=array();

            if ($hashtags->rowCount() > 0) {
                
                while ($row2 = $hashtags->fetch(PDO::FETCH_ASSOC)) {
                    array_push($tags,$row2['libelle']);
                }
            }
            $touiteobject=new TouiteRenderer(new Touite(new UserAuthentifie($row["email"]),$row["texte"],$row["cheminFichier"],$tags,$row["idTouite"]));
            $html.=$touiteobject->render(Renderer::COMPACT);
        }
        return $html;
    }
    
}
?>