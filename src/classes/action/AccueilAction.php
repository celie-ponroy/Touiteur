<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use  iutnc\touiteur\bd\ConnectionFactory as ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;

/**Class AcceuilAction  */
class AccueilAction extends Action {


    public function __construct(){
        parent::__construct();
        ConnectionFactory::setConfig("conf/conf.ini");
    }
    /** cherche les touite des abonements tags et utilisateur */
    public function execute() : string{
        $db = ConnectionFactory::makeConnection();
        
        $sql ="SELECT Touite.texte,Touite.idTouite ,Touite.datePublication, Touite.email, NULL as idTag 
        FROM Touite inner join Abonnement on Touite.email = Abonnement.idSuivi
        where Abonnement.idAbonne like ?
        UNION
        SELECT Touite.texte, Touite.idTouite ,Touite.datePublication, Touite.email ,Tag.idTag 
        FROM Touite inner join Tag2Touite on Tag2Touite.idTouite = Touite.idTouite
            inner join Tag on Tag2Touite.idTouite = Tag.idTag
            inner join AbonnementTag on Tag.idTag = AbonnementTag.idTag
        where AbonnementTag.email like ?
        order by datePublication;";
        $resultset = $db->prepare($sql);
        $user = unserialize($_SESSION['User']);
        $email=$user->__get('email');
        $resultset->bindParam(1,$email );
        $resultset->bindParam(2,$email );
        $resultset->execute();
        $html = "";
        foreach ($resultset->fetchAll() as $row) {
            $html.=("@".$row["email"]." : ".$row["texte"])."<br>";
        }
        return $html;
        
    }
        
}