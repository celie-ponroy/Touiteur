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
        
        $sql ="SELECT * FROM Touite;";
        $resultset = $db->prepare($sql);
        $resultset->execute();
        $html = "";
        foreach ($resultset->fetchAll() as $row) {
            $html.=("@".$row["email"]." : ".$row["text"])."<br>";
        }
        return $html;
    }
    
}
?>