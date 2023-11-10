<?php

/**
 * déclarations des namespaces
 */
namespace iutnc\touiteur\user;
use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\excetion\AbonnementException;
use iutnc\touiteur\touite\Touite;

/**
 * class UserAuthentifie
 */

class UserAuthentifie extends User{

    /**
     * déclarations des attributs
     */
    protected string $email, $nom, $prenom;
    protected int $role;

    /**
     * Constructeur
     * on récupère les informations de la base de donées pour initialiser les attributs
     * @param string $email l'email de l'utilisateur
     * @return void
     */

    public function __construct(string $email){
        $this->email = $email;

        $pdo = ConnectionFactory::makeConnection();

        $query = 'SELECT role,nom, prenom from Utilisateur Where email = ?';
        $st = $pdo->prepare($query);
        $st->execute([$email]);
        $fetch = $st->fetch();

        $role = $fetch['role'];
        $nom = $fetch['nom'];
        $prenom = $fetch['prenom'];

        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->role = $role;
    }

    /**
     * Methode permetant d'inscrire un User dans la base de données
     * @param string $nom le nom de l'utilisateur
     * @param string $prenom le prenom de l'utilisateur
     * @param string $email l'email de l'utilisateur
     * @param string $mdp le mot de passe de l'utilisateur
     * @return string le message de confirmation de l'inscription
     */
    public static function inscription(string $nom , string $prenom , string $email, string $mdp):string {
        $role = 1;//le User n'est pas admin par défault

        //connexion a la base de donées
        $pdo = ConnectionFactory::makeConnection();

        //on regarde si l'utilisateur existe déjà
        $query = "INSERT INTO Utilisateur (nom, prenom, password, email, role) VALUES (:nom, :prenom, :mdp, :email, :role)";


        $stmt = $pdo->prepare($query);

        //attribution des paramètres
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mdp', $mdp);
        $stmt->bindParam(':role', $role);

        //exécution
        if ($stmt->execute()) {
            $html = " ajout user<br> Email:".$email.", Nom:".$nom." Prenom:".$prenom;
        } else {
            $html = "INSERT ERROR: " . $stmt->errorInfo()[2];
        }
        return $html;
    }

    /**
     * Méthode isUserConnected qui permet de vérifier que l'utilisateur est authentifié
     * @return bool true si l'utilisateur est authentifié
     */
    public static function isUserConnected(): bool
    {
        return isset($_SESSION['User']);
    }


/**
 * Méthode getTouite qui permet de récupérer les touites d'un utilisateur
 * @return array tableau de touites
 */

    public function getTouites(){

        //connexion a la base de donées
        $pdo = ConnectionFactory::makeConnection();

        //création aray de tags
        $query = 'SELECT * from Tag
        Left join Tag2Touite on Tag2Touite.idTag=Tag.idTag
        Left join Touite on Touite.idTouite=Tag2Touite.idTouite
        Where email = ?';


        $st = $pdo->prepare($query);
        $st->execute([$this->email]);
        $tags = array();

        //on récupère les tags
        foreach($st->fetchAll() as $row){
        array_push($tags,$row["libelle"]);
        }

        //cration du touite
        $query = 'SELECT * from Touite
                    Left join Image on Touite.idIm=Image.idIm
                    Where email = ?';

        $st = $pdo->prepare($query);
        $st->execute([$this->email]);
        $res = array();

        //on récupère les touites
        foreach($st->fetchAll() as $row){
            array_push($res,new Touite(intval($row["idTouite"])));
        }
        return $res;
    }



    /**
     * Méthode connectUser qui permet de connecter un utilisateur
     * @return void
     */
    public function connectUser(){
        $_SESSION['User'] = serialize($this);
    }


    /**
     * Méthode getUser qui permet de récupérer l'objet UserAuthentifie de l'utilisateur connecté
     * @return UserAuthentifie|null l'utilisateur connecté
     */
    public static function getUser(): ?UserAuthentifie {
        if (self::isUserConnected()) {
            if (isset($_SESSION['User'])) {
                return unserialize($_SESSION['User']);
            }
        }
        return null;
    }

    /**
     * Méthode followUser qui permet de suivre un utilisateur entré en paramètre
     * @param User $userToFollow l'utilisateur à suivre
     * @return bool true si le suivi a été effectué
     */
    public function followUser(User $userToFollow) {

        // si l'utilisateur est identifié
        if (self::isUserConnected()) {

            // Ajoutez une entrée dans la table Abonnement pour enregistrer la relation de suivi.
            $db = ConnectionFactory::makeConnection();

            //on regarde si this suis déjà l'utilisateur

            $res = $this->etreAbonneUser($userToFollow);

            // Si la requête renvoie 0, cela signifie que la relation de suivi n'existe pas encore, l'utilisateur va follow
            if (!$res) {
                // La relation de suivi n'existe pas encore, nous pouvons donc l'ajouter
                $sql = "INSERT INTO Abonnement (idSuivi, idAbonne) VALUES (:idSuivi, :idAbonne)";
            } else {//unfollow
                // La relation de suivi existe déjà
                $sql = "DELETE FROM Abonnement WHERE idSuivi = :idSuivi AND idAbonne = :idAbonne";
            }
            $stmt = $db->prepare($sql);
            $idsuiv =  $userToFollow->__get('email');
            $idabo = $this->__get('email');
            $stmt->bindParam(':idSuivi', $idsuiv);
            $stmt->bindParam(':idAbonne', $idabo);

            //exécution
            if ($stmt->execute()) {
                // Suivi réussi
                return true;
            } else {
                throw new AbonnementException();
            }
        }

    }


    /**
     * Méthode userExists qui permet de savoir si un utilisateur existe
     * @param $uEmail string l'email de l'utilisateur
     * @return bool true si l'utilisateur existe
     */
    public static function userExists($uEmail):bool{

        //connexion a la base de donées
        $pdo = ConnectionFactory::makeConnection();

        //on regarde si l'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT * FROM Utilisateur WHERE email = :email");
        $stmt->bindParam(':email', $uEmail);

        $stmt->execute();

        return $stmt->rowCount() > 0;

    }


    /**
     * Méthode etreAbonnerUser qui permet de savoir si l'utilisateur connecté suit l'utilisateur entré en paramètre
     * @param User $userToFollow l'utilisateur à suivre
     * @return bool true si l'utilisateur connecté suit l'utilisateur entré en paramètre
     */
    public function etreAbonneUser(User $userToFollow):bool{

        //connexion a la base de donées
        $db = ConnectionFactory::makeConnection();

        //on regarde si this suis déjà l'utilisateur
        $sql = "SELECT COUNT(*) FROM Abonnement WHERE idSuivi = :idSuivi AND idAbonne = :idAbonne";

        //préparation des données
        $stmt = $db->prepare($sql);
        $idsuiv =  $userToFollow->__get('email');
        $idabo = $this->__get('email');

        //attribution des paramètres
        $stmt->bindParam(':idSuivi', $idsuiv);
        $stmt->bindParam(':idAbonne', $idabo);

        //exécution
        $stmt->execute();
        return !$stmt->fetchColumn() == 0;
    }
     /**
     * Méthode etreAbonneTag qui permet de savoir si l'utilisateur connecté suit tag entré en paramètre
      * @param int $idTag l'id du tag
      * @return bool true si l'utilisateur connecté suit le tag entré en paramètre
     */
    public function etreAbonneTag(int $idTag):bool{
        $db = ConnectionFactory::makeConnection();

        $sql = "SELECT COUNT(*) FROM AbonnementTag WHERE idTag = :idTag AND email = :email";
            //préparation des données 
            $stmt = $db->prepare($sql);
            $email = $this->__get('email');
            //attribution des paramètres
            $stmt->bindParam(':idTag', $idTag);
            $stmt->bindParam(':email', $email);
            //exécution
            $stmt->execute();
        return !$stmt->fetchColumn() == 0;
    }

    /**
     * Methode followTag qui permet de suivre un Tag
     * @param int $idTag l'id du tag
     * @return bool true si le suivi a été effectué
     * @throws AbonnementException si le suivi n'a pas été effectué
     */
    public function followTag(int $idTag) {

        // si l'utilisateur est identifié
        if (self::isUserConnected()) {
            $db = ConnectionFactory::makeConnection();

            $email = $this->__get('email');
            $abo = $this->etreAbonneTag($idTag);

            // Si la requête renvoie 0, cela signifie que la relation de suivi n'existe pas encore, l'utilisateur va follow
            if (!$abo) {
                $sql = "INSERT INTO AbonnementTag (idTag,email) VALUES (:idTag, :email)";
            }else{
                // La relation de suivi existe déjà, l'utilisateur va unfollow:
                $sql = "DELETE FROM AbonnementTag
                            WHERE idTag = :idTag AND email = :email ;";
                
            }
            $stmt = $db->prepare($sql);


            $idT = (int)$idTag;
            var_dump($idT);
            $stmt->bindParam(':idTag',$idT);
            $stmt->bindParam(':email', $email); 

            if ($stmt->execute()) {
                // Suivi réussi
                return true;
            } else {
                //échec du suivi
                //throw exception
                throw new AbonnementException();
            }
        }
    }


    /**
     * Méthode listeAbo qui permet de récupérer la liste des tags suivis par l'utilisateur
     * @return array tableau de tags
     */
    public function listeAbo():array{

        //connexion a la base de donées
        $db = ConnectionFactory::makeConnection();

        //création array de tags
        $sql = "SELECT email , nom, prenom
        FROM Abonnement inner join Utilisateur on Utilisateur.email = Abonnement.idAbonne
        WHERE Abonnement.idSuivi = :email ";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();
        $res = array();

        //on récupère les touites
        foreach($stmt->fetchAll() as $row){
            array_push($res, $row);
        }
        return $res;
    }

    /**
     * Méthode isAdmin qui renvoie true si l'utilisateur est un administrateur
     * @return bool true si l'utilisateur est un administrateur
     */
    function isAdmin():bool{
        return $this->role==2;
    }

    /**
     * Méthode __get qui permet de récupérer la valeur d'un attribut
     * @param $name string nom de la propriété
     * @return mixed la valeur de l'attribut
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