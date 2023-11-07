<?php
declare(strict_types= 1);

namespace iutnc\touiteur\touite;

use DateTime;
use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;
use PDO;

/**
 * class Touite
 */
class Touite{
    protected string $texte;
    protected ?string $pathpicture;//contenu du touite
    protected UserAuthentifie $user ; //l'auteur
    protected DateTime $date;//date de publication du touite
    protected array $tags; // table de tags

    protected int $idtouite, $nblikes, $nbdislike;
    /**
     * contructeur
     */
    function __construct(UserAuthentifie $user, string $texte, ?string $pathpicture="", array $tags,int $id){
        $this->texte = $texte;
        $this->user = $user;
        $this->date = new \DateTime();
        $this->tags = $tags;
        $this->pathpicture = $pathpicture;
        $this->idtouite = $id;
    }


    public function __get($name): mixed{
        if(property_exists($this, $name)){
            return $this->$name;
        }else{
            echo "Get invalide";
            return null;
        }
    }

    public function publierTouite(){

        $db = ConnectionFactory::makeConnection();

        $sql2 = "SELECT max(idTouite) as maxid FROM Touite;";

        $idimage = $db->prepare($sql2);
        $idimage->execute();
        $resimage = $idimage->fetch();

        $idpicture = $resimage["maxid"]+1;
        echo"<h1>".$idpicture."</h1>";

        /*maj de l'image*/
        $sql ="Insert into Image values(?,null,?);";
        $resultset = $db->prepare($sql);
        $resultset->bindParam(1,$idpicture, PDO::PARAM_INT);
        $resultset->bindParam(2,$this->pathpicture, PDO::PARAM_STR);
        $resultset->execute();
        
        /*maj du Touite */
            
        $sql2 = "SELECT max(idTouite) as maxid
                    FROM Touite;";

        $id = $db->prepare($sql2);
        $id->execute();
        $res = $id->fetch();


        $idtouite=$res["maxid"]+1;
        $texte=$this->texte;
        $datePublication=$this->date->format('Y-m-d H:i:s');
        $email=$this->user->__get("email");
        
        $sql ="Insert into Touite values(?,?,?,?,?);";
        $resultset = $db->prepare($sql);
        $resultset->bindParam(1,$idtouite, PDO::PARAM_INT);
        $resultset->bindParam(2,$texte, PDO::PARAM_STR);
        $resultset->bindParam(3,$datePublication, PDO::PARAM_STR);
        $resultset->bindParam(4,$email, PDO::PARAM_STR);
        $resultset->bindParam(5,$idpicture, PDO::PARAM_INT);
        $resultset->execute();
    }

    /**
     * toString 
     */
    function __toString(){
        $res = "@".$this->user;
        return $res."<br>\n".$this-> texte;
        //ajouter les tags
        
        
    }

    public function findTaggedTw(){
        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT idTouite from Touite 
                    inner join Tag2Touite on Touite.idTouite = Tag2Touite.idTouite  
                    inner join Tag on Tag.idTag = Tag2Touite.idTag
                    Where tags = ?';

        $st = $pdo->prepare($query);
        $st->execute([$this->email]);

        return $st->fetchAll();
    }
}