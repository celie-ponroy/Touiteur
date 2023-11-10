<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use  iutnc\touiteur\bd\ConnectionFactory as ConnectionFactory;
use iutnc\touiteur\touite\ListTouite;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\user\UserAuthentifie;

/**
 * Class AcceuilAction
 */
class AccueilAction extends Action {


    public function __construct(){
        parent::__construct();
        ConnectionFactory::setConfig("conf/conf.ini");
    }
    /**
     * Méthode execute qui cherche les touite des abonements tags et utilisateur
     * @return string code html
     */
    public function execute() : string{
        $db = ConnectionFactory::makeConnection();

        //requète sql
        $sql ="SELECT Touite.texte, Touite.idTouite, Touite.datePublication, Touite.email, NULL as idTag 
           FROM Touite 
           INNER JOIN Abonnement ON Touite.email = Abonnement.idSuivi
           WHERE Abonnement.idAbonne = ?
           UNION
           SELECT Touite.texte, Touite.idTouite, Touite.datePublication, Touite.email, Tag2Touite.idTag 
           FROM Touite 
           INNER JOIN Tag2Touite ON Tag2Touite.idTouite = Touite.idTouite
           INNER JOIN Tag ON Tag2Touite.idTag = Tag.idTag
           INNER JOIN AbonnementTag ON Tag.idTag = AbonnementTag.idTag
           WHERE AbonnementTag.email = ?
           ORDER BY datePublication DESC;";
    
        $resultset = $db->prepare($sql);
        
        $user = unserialize($_SESSION['User']);
        $email=$user->__get('email');

        $resultset->bindParam(1,$email );
        $resultset->bindParam(2,$email );
        $resultset->execute();

        $touiteAafficher = array();
        
        $html = "";
        //affiche chaque Touite
        foreach ($resultset->fetchAll() as $row) {
            array_push($touiteAafficher, new Touite($row["idTouite"]));
        }
        $html = (new ListTouite($touiteAafficher))->afficher();
        return $html;
        
    }
        
}