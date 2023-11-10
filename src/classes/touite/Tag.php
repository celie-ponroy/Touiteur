<?php
declare(strict_types= 1);

namespace iutnc\touiteur\touite;

use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;
use PDO;

class Tag{
    private $id;
    private $libelle;
    private $desciption;
    
    function __construct(?string $libelle=null, ?string $id=null){

        $pdo = ConnectionFactory::makeConnection();
        if ($libelle !== null){
            $query = 'SELECT idTag from Tag Where libelle = ?';
            $st = $pdo->prepare($query);
            $st->execute([$libelle]);
            $id = $st->fetch()['idTag'];
        }
        else {
            $query = 'SELECT libelle from Tag Where idTag = ?';
            $st = $pdo->prepare($query);
            $st->execute([$id]);
            $libelle = $st->fetch()['libelle'];
        }
        $query = 'SELECT description from Tag Where idTag = ?';
        $st = $pdo->prepare($query);
        $st->execute([$id]);
        $desciption = $st->fetch()['description'];

        $this->id = $id;
        $this->libelle = $libelle;
        $this->desciption = $desciption;
    }

    /*retourn les touites associes a this (tag) */
    public function findTaggedTw(){
        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT Touite.idTouite from Touite 
                    inner join Tag2Touite on Touite.idTouite = Tag2Touite.idTouite  
                    inner join Tag on Tag.idTag = Tag2Touite.idTag
                    Where Tag.idTag = ?';

        $st = $pdo->prepare($query);
        $st->execute([$this->id]);


        $tags = array();
        $results = $st->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            array_push($tags,  new Touite($row['idTouite']));
        }

        return $tags;
    }

    /* retourne true si this (tag) followed par $user */
    public function isTagFollowed(UserAuthentifie $user):bool{

        $pdo = ConnectionFactory::makeConnection();
        $sql = "SELECT COUNT(*) FROM AbonnementTag WHERE email = :email AND idTag = :idTag";
        $stmt = $pdo->prepare($sql);
        $email = $user->__get('email');
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':idTag', $this->id);

        $stmt->execute();

        $isSubscribed = $stmt->fetchColumn() > 0;

        return $isSubscribed;
    }


    public function __get($name): mixed{
        if(property_exists($this, $name)){
            return $this->$name;
        }else{
            echo "Get invalide";
            return null;
        }
    }
}