<?php
declare(strict_types= 1);

namespace iutnc\touiteur\touite;

use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;
use PDO;

/**
 * Class Tag
 */
class Tag{
    private $id;
    private $libelle;
    private $desciption;

    /**
     * Constructeur
     * @param string|null $libelle libelle du tag
     * @param string|null $id id du tag
     */
    function __construct(?string $libelle=null, ?string $id=null){

        $pdo = ConnectionFactory::makeConnection();
        if ($libelle !== null){
            //si on a le libelle on cherche l'id
            $query = 'SELECT idTag from Tag Where libelle = ?';
            $st = $pdo->prepare($query);
            $st->execute([$libelle]);
            $id = $st->fetch()['idTag'];
        }
        else {
            //si on a l'id on cherche le libelle
            $query = 'SELECT libelle from Tag Where idTag = ?';
            $st = $pdo->prepare($query);
            $st->execute([$id]);
            $libelle = $st->fetch()['libelle'];
        }
        //on cherche la description
        $query = 'SELECT description from Tag Where idTag = ?';
        $st = $pdo->prepare($query);
        $st->execute([$id]);
        $desciption = $st->fetch()['description'];

        //on initialise les attributs
        $this->id = $id;
        $this->libelle = $libelle;
        $this->desciption = $desciption;
    }

    /**
     * Méthode findTaggedTw qui retourne les touites taggés par this (tag)
     * @return array tableau de touites
     */
    public function findTaggedTw(){

        //on se connecte à la base de données
        $pdo = ConnectionFactory::makeConnection();

        //on cherche les touites taggés par this (tag)
        $query = 'SELECT Touite.idTouite from Touite 
                    inner join Tag2Touite on Touite.idTouite = Tag2Touite.idTouite  
                    inner join Tag on Tag.idTag = Tag2Touite.idTag
                    Where Tag.idTag = ?';

        $st = $pdo->prepare($query);
        $st->execute([$this->id]);


        $tags = array();
        $results = $st->fetchAll(PDO::FETCH_ASSOC);

        //on crée un tableau de touites
        foreach ($results as $row) {
            array_push($tags,  new Touite($row['idTouite']));
        }

        return $tags;
    }

    /**
     * Méthode isTagFollowed qui retourne true si this (tag) followed par $user
     * @param UserAuthentifie $user utilisateur authentifié
     * @return bool true si this (tag) followed par $user
     */
    public function isTagFollowed(UserAuthentifie $user):bool{

        //on se connecte à la base de données
        $pdo = ConnectionFactory::makeConnection();

        //on cherche si $user suit this (tag)
        $sql = "SELECT COUNT(*) FROM AbonnementTag WHERE email = :email AND idTag = :idTag";
        $stmt = $pdo->prepare($sql);
        $email = $user->__get('email');
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':idTag', $this->id);

        $stmt->execute();

        //on retourne true si $user suit this (tag)
        $isSubscribed = $stmt->fetchColumn() > 0;

        return $isSubscribed;
    }

    /**
     * Méthode __get qui retourne la valeur de l'attribut $name
     * @param string $name nom de l'attribut
     * @return mixed valeur de l'attribut $name
     */
    public function __get($name): mixed{
        if(property_exists($this, $name)){
            return $this->$name;
        }else{
            echo "Get invalide";
            return null;
        }
    }
}