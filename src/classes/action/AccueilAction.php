<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use  iutnc\touiteur\bd\ConnectionFactory as ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;

class AccueilAction extends Action {


    public function __construct(){
        parent::__construct();
        ConnectionFactory::setConfig("conf/conf.ini");
    }
    
    public function execute() : string{
        $db = ConnectionFactory::makeConnection();
        
        $sql ="SELECT * FROM Touite 
            right join Abonnement on Touite.email = Abonnement.idSuivi
            inner join TouitetoTag on TouitetoTag.idTouite = idTag
            inner join Tag on TouitetoTag.idTouite = Tag.idTag
            right join AbonnementTag on Tag.idTag = AbonnementTag.idTag
            where idAbonné = :email
            order by Touite.datePublication;";
        $resultset = $db->prepare($sql);
        $user = unserialize($_SESSION['User']);
        $email=$user->__get('email');
        $resultset->bindParam(':email',$email );
        $resultset->execute();
        $html = "";
        foreach ($resultset->fetchAll() as $row) {
            $html.=("@".$row["email"]." : ".$row["texte"])."<br>";
        }
        return $html;
    }
        
}