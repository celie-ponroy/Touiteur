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
    protected  $user ; //l'auteur
    protected DateTime $date;//date de publication du touite
    protected ?array $tags; // table de tags

    protected int $idtouite;

    protected int $nblikes, $nbdislike;
    /**
     * contructeur ( reprends les données dans la base de donnés )
     */

    function __construct(int $id){
        $this->idtouite = $id;
        //recupere récupère les données
        $db = ConnectionFactory::makeConnection();

        //requete sql pour récupérer les données du touite
        $query = 'SELECT texte, idIm , datePublication, email
                FROM Touite
                WHERE idTouite = ?';
        $resultset = $db->prepare($query);
        $resultset->bindParam(1,$id, PDO::PARAM_INT);
        $resultset->execute();
        $fetch = $resultset->fetch();
        //recupère les données
        $this->texte = $fetch['texte'];
        $this->user = new UserAuthentifie($fetch['email']);
        $format = "Y-m-d H:i:s";
        $date = DateTime::createFromFormat($format, $fetch['datePublication']);

        //si la date n'est pas valide
        if($date===false){
            $this->date = new \DateTime();
        }else{$this->date = $date ;}
        //recherche le chemin de l'image si le touite possède une image
        $idIm = $fetch['idIm'];
        if($idIm!==NULL){
            $db = ConnectionFactory::makeConnection();

            //requete sql pour récupérer le chemin de l'image
            $query = 'SELECT cheminFichier 
                    FROM Image
                    WHERE idIm = ?';
            $resultset = $db->prepare($query);
            $resultset->bindParam(1,$idIm, PDO::PARAM_INT);
            $resultset->execute();

            $fich=$resultset->fetch();

            //recupère le chemin de l'image
            $this->pathpicture=$fich['cheminFichier'];
        
        }else{
            //si il n'y a pas d'image
            $this->pathpicture="";
        }

        //recherche le chemin de l'image si le touite possède des tags
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

        //recherche le nombre de like et de dislike
        $query = 'SELECT count(*) as nbnote
                FROM Note
                WHERE idTouite = ?
                group by note
                order by nbnote DESC';

            $resultset = $db->prepare($query);
            $resultset->bindParam(1,$this->idtouite, PDO::PARAM_INT);
            $resultset->execute();

            $row=$resultset->fetch();

            //recupère les données
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



    }
    /**
     * Méthode publierTouite qui publie un touite dans la base de données
     * @param UserAuthentifie $user utlisatuer authentifié
     * @param string $texte texte du touite
     * @param array|null $tag tableau de tag
     * @param string $pathpicture chemin de l'image
     * @return void
     */
    public static function publierTouite(UserAuthentifie $user, string $texte,  ?array $tag=null,?string $pathpicture=""){

        //connexion à la base de données
        $db = ConnectionFactory::makeConnection();

        $date = new \DateTime();
        

        $idpicture = null;

        //si il y a une image
        if($pathpicture!=""){

            //ajouter l'image dans la base de données
            $sql ="Insert into Image(description, cheminFichier) values(null,?);";
            $resultset = $db->prepare($sql);
            $resultset->bindParam(1,$pathpicture);
            $resultset->execute();
            //recupère l'id de l'image
            $idpicture = $db->lastInsertId();
        }
        
        //formatage de la date
        $datePublication=$date->format('Y-m-d H:i:s');

        //recupère l'email de l'utilisateur
        $email = $user->__get("email");

        //requete sql pour insert le touite
        $sql = "INSERT INTO Touite (texte,email,idIm, datePublication) 
                VALUES (:texte,:email,:idIm,:datePubi)";
        $resultset = $db->prepare($sql);
        $resultset->bindParam(':texte',$texte, PDO::PARAM_STR);
        $resultset->bindParam(':datePubi',$datePublication, PDO::PARAM_STR);
        $resultset->bindParam(':email',$email, PDO::PARAM_STR);
        $resultset->bindParam(':idIm',$idpicture, PDO::PARAM_INT);
        

        //si le touite est bien inséré
        if ($resultset->execute()) {
           $idtouite = $db->lastInsertId();
           //si il y a des tags
            if(isset($tag)){
                //ajouter les tags dans la base de données
                foreach ($tag as $t) {
                    $lib=$t;
                    //chercher si le tag existe 
                    $sql = "SELECT COUNT(*) as compte  FROM Tag WHERE libelle = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(1, $lib);
                    $stmt->execute();
                    $tmp = $stmt->fetch();
                    $idTag = 0;

                    //si le tag n'existe pas
                    if($tmp['compte']==0){  
                        //requete sql pour ajouter les tags dans Tag
                        $insert = "INSERT INTO Tag( description , libelle) VALUES ( :descr, :lib)";
                        $stmt = $db->prepare($insert);
                        $stmt->bindParam(':descr', $lib);
                        $stmt->bindParam(':lib', $lib);
                        $stmt->execute();
                        $idTag = $db->lastInsertId();
                    }else{
                        //requete sql pour récupérer l'id du tag
                        $sql = "SELECT idTag FROM Tag WHERE libelle = ?";
                        $stmt = $db->prepare($sql);
                        $stmt->bindParam(1, $lib);
                        $stmt->execute();
                        $tab = $stmt->fetch();
                        $idTag = $tab['idTag'] ;
                    }
                    //requete sql pour ajouter tag dans Tag2Touite
                    $insertT2T = "INSERT INTO Tag2Touite (idTag, idTouite) VALUES(?,?);";
                    $stmt = $db->prepare($insertT2T);
                    $stmt->bindParam(1, $idTag, PDO::PARAM_INT);
                    $stmt->bindParam(2, $idtouite, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        } else {
            //si le touite n'est pas inséré
            $html = "INSERT ERROR: " . $resultset->errorInfo()[2];
        }
            
            
        
    }

    /**
     * Méthode deleteT qui supprime un touite dans la base de données
     * @return void
     */

    public function deleteT():void{
        $pdo = ConnectionFactory::makeConnection();

        //supprime l'image
        $pdo->prepare("DELETE FROM Note WHERE idTouite = ?")->execute([$this->idtouite]);

        //supprime les tags
        $pdo->prepare("DELETE FROM Tag2Touite WHERE idTouite = ?")->execute([$this->idtouite]);

        //supprime le touite
        $pdo->prepare("DELETE FROM Touite WHERE idTouite = ?")->execute([$this->idtouite]);
    }
    //GETTERS

    /**
     * Méthode __get qui permet de récupérer les attributs de la classe
     * @param $name nom de l'attribut
     * @return mixed valeur de l'attribut
     */
    public function __get($name): mixed{
        if(($name==='ndlikes'||$name==='nbdislike')&&property_exists($this, $name)){
            if($name==='nblikes'&&isset($this->nblikes)){
                    return $this->nblikes;
            }elseif($name==='nbdislike'&&isset($this->nbdislike)){
                    return $this->nbdislike;
            }else{
                return 0;
            }
        }elseif(property_exists($this, $name)){
                return $this->$name;
        }
        return null;
        
    }


/**
 * Méthode appartientUserAuth retourne true si le touite appartient à l'utilisateur authentifié
 * @return bool true si le touite appartient à l'utilisateur authentifié
 */
    public function appartientUserAuth():bool{

        //si l'utilisateur n'est pas connecté
        if (!UserAuthentifie::isUserConnected())
            return false;

        //connexion à la base de données
        $pdo = ConnectionFactory::makeConnection();

        //requete sql pour récupérer le nombre de touite
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Touite WHERE idTouite = :tweetId AND email = :email");

        //recupère l'email de l'utilisateur
        $email= UserAuthentifie::getUser()->__get('email');
        $id = $this->idtouite;

        $stmt->bindParam(':tweetId', $id);
        $stmt->bindParam(':email', $email);

        $stmt->execute();

        $count = $stmt->fetchColumn();

        //si le touite appartient à l'utilisateur authentifié
        return $count > 0;
    }

    //SETTERS

    /**
     * Méthode __set qui permet de modifier les attributs de la classe
     * @param $name string nom de l'attribut
     * @param $value valeur de l'attribut
     * @return void
     */
    public function __set($name, mixed $value):void{
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }


    /**
     * Méthode __toString qui permet d'afficher le touite
     * @return string chaine de caractère qui représente le touite
     */
    function __toString(){
        $res = "@".$this->user;
      
        //ajouter les tags
        foreach ($this->tags as &$t) {
            $res .= $t;
        }
        return $res."<br>\n".$this-> texte;
    }

    /**
     * Méthode statistique qui renvoi l'indice de popularité du Touite
     */
    function statistique(){
        return (Note::getnbLike($this->idtouite) - Note::getnbDislike($this->idtouite));
    }



}