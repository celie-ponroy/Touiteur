<?php
declare(strict_types= 1);

namespace iutnc\touiteur\touite;

use DateTime;
use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\user\User;
use iutnc\touiteur\user\UserAuthentifie;
use PDO;

/**
 * class Touite
 */
class Touite{

    protected string $texte;
    protected ?string $pathpicture;//contenu du touite
    protected  $user ; //l'auteur
    protected DateTime $date;//date de publication du touite
    protected ?array $tags; // table de tags


    protected int $idtouite;

    protected int $nblikes, $nbdislike;
    /**
     * contructeur
     */

    function __construct(int $id){
        $this->idtouite = $id;

        $db = ConnectionFactory::makeConnection();
        $query = 'SELECT texte, idIm , datePublication, email
                FROM Touite
                WHERE idTouite = ?';
        $resultset = $db->prepare($query);
        $resultset->bindParam(1,$id, PDO::PARAM_INT);
        $resultset->execute();
        $fetch = $resultset->fetch();
        $this->texte = $fetch['texte'];
        $this->user = new UserAuthentifie($fetch['email']);
        $format = "Y-m-d H:i:s";

        
        $date = DateTime::createFromFormat($format, $fetch['datePublication']);
      
        if($date===false){
            $this->date = new \DateTime();
        }else{$this->date = $date ;}
        
        $idIm = $fetch['idIm'];
        if($idIm!==NULL){
            $db = ConnectionFactory::makeConnection();
            $query = 'SELECT cheminFichier 
                    FROM Image
                    WHERE idIm = ?';
            $resultset = $db->prepare($query);
            $resultset->bindParam(1,$idIm, PDO::PARAM_INT);
            $resultset->execute();

            $fich=$resultset->fetch();
        
            $this->pathpicture=$fich['cheminFichier'];
        
        }else{
            $this->pathpicture="";
        }
        
        $sqltag="SELECT libelle FROM Tag2Touite INNER JOIN  Tag on Tag.idTag = Tag2Touite.idTag WHERE idTouite = ?";
        $resultset = $db->prepare($sqltag);
        $resultset->bindParam(1,$id);
        $resultset->execute();
        
        $idtags=$resultset->fetchAll();
        if($idtags!==false){
            $this->tags  = array();
            foreach($idtags as $idt){
                array_push($this->tags,$idt['libelle']);
            }
        }else{
            $this->tags=null;
        }

        //note
        $query = 'SELECT count(*) as nbnote
                FROM Note
                WHERE idTouite = ?
                group by note
                order by nbnote DESC';

            $resultset = $db->prepare($query);
            $resultset->bindParam(1,$this->idtouite, PDO::PARAM_INT);
            $resultset->execute();

            $row=$resultset->fetch();
     
            if($row!=false){
                $this->nblikes = $row['nbnote'];//like
                $row=$resultset->fetch();
                if($row!=false){
                    $this->nbdislike = $row['nbnote'];//like
                }else{
                    $this->nbdislike= 0;
                }
            }else{
                $this->nblikes= 0;
            }
            
            
        
        
        //array tag


    }
    public static function publierTouite(UserAuthentifie $user, string $texte,  ?array $tag=null,?string $pathpicture=""){
        $db = ConnectionFactory::makeConnection();

        $date = new \DateTime();
        

        $sql2 = "SELECT max(idIm) as maxid FROM Image;";
        $idpicture = null;

        if($pathpicture!=""){

            //sql pour ajouter image dans Image
            $idimage = $db->prepare($sql2);
            $idimage->execute();
            $resimage = $idimage->fetch();

            $idpicture = $resimage["maxid"]+1;

            /*maj de l'image*/
            $sql ="Insert into Image values(?,null,?);";
            $resultset = $db->prepare($sql);
            $resultset->bindParam(1,$idpicture, PDO::PARAM_INT);
            $resultset->bindParam(2,$pathpicture, PDO::PARAM_STR);
            $resultset->execute();
        }
        
        /*maj du Touite */
            
        $sql2 = "SELECT max(idTouite) as maxid FROM Touite;";

        $id = $db->prepare($sql2);
        $id->execute();
        $res = $id->fetch();


        $idtouite=$res["maxid"]+1;
        $datePublication=$date->format('Y-m-d H:i:s');
        $email = $user->__get("email");
        
        $sql = "INSERT INTO Touite (idTouite,texte,email,idIm, datePublication) 
                VALUES (:idTouite,:texte,:email,:idIm,:datePubi)";
        $resultset = $db->prepare($sql);
        $resultset->bindParam(':idTouite',$idtouite, PDO::PARAM_INT);
        $resultset->bindParam(':texte',$texte, PDO::PARAM_STR);
        $resultset->bindParam(':datePubi',$datePublication, PDO::PARAM_STR);
        $resultset->bindParam(':email',$email, PDO::PARAM_STR);
        $resultset->bindParam(':idIm',$idpicture, PDO::PARAM_INT);
        
        
        
        //sql pour insert le touite
        
        if ($resultset->execute()) {
            $html ="touite publié";
        } else {
            $html = "INSERT ERROR: " . $resultset->errorInfo()[2];
        }
        
        if(isset($tag)){
            foreach ($tag as $t) {
                $lib=$t;
                //chercher si le tag existe 
                $sql = "SELECT COUNT(*) as compte  FROM Tag WHERE libelle = ?";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(1, $lib);
                $stmt->execute();
                $tmp = $stmt->fetch();
                $idTag = 0;
                //si il existe pas 
                if($tmp['compte']==0){  
                    $sql = "SELECT max(idTag) as max FROM Tag";
                    $stmt = $db->prepare($sql);
                    $stmt->execute();
                    $tab = $stmt->fetch();
                    $idTag = $tab['max'] +1;
                    //sql pour ajouter les tags dans Tag
                    $insert = "INSERT INTO Tag(idTag, description , libelle) VALUES (:idTag, :descr, :lib)";
                    $stmt = $db->prepare($insert);
                    $stmt->bindParam(':idTag', $idTag);
                    $stmt->bindParam(':descr', $lib);
                    $stmt->bindParam(':lib', $lib);
                    $stmt->execute();
                }else{
                    $sql = "SELECT idTag FROM Tag WHERE libelle = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(1, $lib);
                    $stmt->execute();
                    $tab = $stmt->fetch();
                    $idTag = $tab['idTag'] ;
                }
                //sql pour ajouter tag dans Tag2Touite
                $insertT2T = "INSERT INTO Tag2Touite (idTag, idTouite) VALUES(?,?);";
                $stmt = $db->prepare($insertT2T);
                $stmt->bindParam(1, $idTag, PDO::PARAM_INT);
                $stmt->bindParam(2, $idtouite, PDO::PARAM_INT);
                $stmt->execute();
            }
            
        }
    }

    public function deleteT():void{
        $pdo = ConnectionFactory::makeConnection();

        $pdo->prepare("DELETE FROM note WHERE idTouite = ?")->execute([$this->idtouite]);

        $pdo->prepare("DELETE FROM tag2touite WHERE idTouite = ?")->execute([$this->idtouite]);

        $pdo->prepare("DELETE FROM touite WHERE idTouite = ?")->execute([$this->idtouite]);
    }

    public function __get($name): mixed{
        if(($name==='ndlikes'||$name==='nbdislike')&&property_exists($this, $name)){
            if($name==='nblikes'){
                if(isset($this->nblikes))
                    return $this->nblikes;
            }elseif($name==='nbdislike'){
                if(isset($this->nbdislike))
                    return $this->nbdislike;
                
            }else{
                return 0;
            }
        }elseif(property_exists($this, $name)){
                return $this->$name;
        }
        return null;
        
    }



    public function appartientUserAuth():bool{
        if (!UserAuthentifie::isUserConnected())
            return false;
        $pdo = ConnectionFactory::makeConnection();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Touite WHERE idTouite = :tweetId AND email = :email");

        $email= UserAuthentifie::getUser()->__get('email');
        $id = $this->idtouite;

        $stmt->bindParam(':tweetId', $id);
        $stmt->bindParam(':email', $email);

        $stmt->execute();

        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    public function __set($name, mixed $value):void{
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }


    /**
     * toString 
     */
    function __toString(){
        $res = "@".$this->user;
      
        //ajouter les tags
        foreach ($this->tags as &$t) {
            $res .= $t;
        }
        return $res."<br>\n".$this-> texte;
    }

    function statistique(){
        return (Note::getnbLike($this->idtouite) - Note::getnbDislike($this->idtouite));
    }



}