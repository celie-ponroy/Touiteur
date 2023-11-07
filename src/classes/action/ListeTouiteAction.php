<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use  iutnc\touiteur\bd\ConnectionFactory as ConnectionFactory;

class ListeTouiteAction extends Action {

    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;
   

    public function __construct(){
        parent::__construct();
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
        ConnectionFactory::setConfig("conf/conf.ini");
    }
    
    public function execute() : string{

        $db = ConnectionFactory::makeConnection();
        //récupère les données des touites
        $sql ="SELECT * FROM Touite order by Touite.datePublication;";
        $resultset = $db->prepare($sql);
        //exécute la requète sql
        $resultset->execute(); 
        //initialise le html
        $html = "";
        //affiche chaque Touite
        foreach ($resultset->fetchAll() as $row) {
            echo $row["idTouite"];
            $html.="   <a class='action' href = '?action=touite-en-detail&id=".$row["idTouite"]."'>@".$row["email"]." : ".$row["texte"]."</a><br>";
        }
        return $html;
    }
    
}
?>