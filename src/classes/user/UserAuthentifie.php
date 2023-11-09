<?php

/**
 * déclarations des namespaces
 */
namespace iutnc\touiteur\user;
use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\excetion\AbonnementException;
use iutnc\touiteur\touite\Touite;

class UserAuthentifie extends User{

    /**
     * déclarations des attributs
     */
    protected string $email, $nom, $prenom;
    protected int $role;

    /**
     * Constructeur
     * on récupère les informations de la base de donées pour initialiser les attributs
     */

    public function __construct(string $email){
        $this->email = $email;

        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT role from Utilisateur Where email = ?';
        $st = $pdo->prepare($query);
        $st->execute([$email]);
        $role = $st->fetch()['role'];

        $query = 'SELECT nom from Utilisateur Where email = ?';
        $st = $pdo->prepare($query);
        $st->execute([$email]);
        $nom = $st->fetch()['nom'];

        $query = 'SELECT prenom from Utilisateur Where email = ?';
        $st = $pdo->prepare($query);
        $st->execute([$email]);
        $prenom = $st->fetch()['prenom'];



        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->role = $role;
    }

    /**
     * Methode permetant d'inscrire un User dans la base de données 
     */
    public static function inscription(string $nom , string $prenom , string $email, string $mdp):string {
        $role = 1;//le User n'est pas admin par défault

        $pdo = ConnectionFactory::makeConnection();

        $query = "INSERT INTO Utilisateur (nom, prenom, password, email, role) VALUES (:nom, :prenom, :mdp, :email, :role)";


        $stmt = $pdo->prepare($query);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);

        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mdp', $mdp);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            $html = " ajout user<br> Email:".$email.", Nom:".$nom." Prenom:".$prenom;
        } else {
            $html = "INSERT ERROR: " . $stmt->errorInfo()[2];
        }
        return $html;
    }

    /*
     * Méthode qui permet de vérifier que l'utilisateur est authentifié
     */
    public static function isUserConnected(): bool
    {
        return isset($_SESSION['User']);
    }


/*
 * Méthode qui permet de récupérer les touites d'un utilisateur
 */

 /*     Left join Tag2Touite on Tag2Touite.idTouite=Touite.idTouite
                    Left join Tag on Tag.idTag=Tag2Touite.idTag */
    public function getTouites(){
        //connexion a la base de donées
        $pdo = ConnectionFactory::makeConnection();
        /*creation array tag */
        $query = 'SELECT * from Tag
        Left join Tag2Touite on Tag2Touite.idTag=Tag.idTag
        Left join Touite on Touite.idTouite=Tag2Touite.idTouite
        Where email = ?';


        $st = $pdo->prepare($query);
        $st->execute([$this->email]);
        $tags = array();
        foreach($st->fetchAll() as $row){
        array_push($tags,$row["libelle"]);
        }
        /*creation du touite */
        $query = 'SELECT * from Touite
                    Left join Image on Touite.idIm=Image.idIm
                    Where email = ?';

        $st = $pdo->prepare($query);
        $st->execute([$this->email]);
        $res = array();

        $user = unserialize($_SESSION['User']);
        foreach($st->fetchAll() as $row){
            array_push($res,new Touite(intval($row["idTouite"])));
        }
        return $res;
    }



    /*
     * Méthode qui permet de connecter un utilisateur
     */
    public function connectUser(){
        $_SESSION['User'] = serialize($this);
    }


    /*
     * Méthode qui permmet de récupérer l'ID de l'utilisateur
     */
    public function getId(): string {
        // Remplacez 'id' par l'attribut contenant l'email de l'utilisateur
        return $this->email;
    }

    /*
     * Méthode qui permet de récupérer l'objet UserAuthentifie de l'utilisateur connecté
     */
    public static function getUser(): ?UserAuthentifie {
        if (self::isUserConnected()) {
            if (isset($_SESSION['User'])) {
                return unserialize($_SESSION['User']);
            }
        }
        return null;
    }

    /*
     * Méthode qui permet de suivre un utilisateur entré en paramètre
     */
    public function followUser(User $userToFollow) {
        // si l'utilisateur est identifié
        if (self::isUserConnected()) {
            // Ajoutez une entrée dans la table Abonnement pour enregistrer la relation de suivi.
            $db = ConnectionFactory::makeConnection();

            //on regarde si this suis déjà l'utilisateur
            $sql = "SELECT COUNT(*) FROM Abonnement WHERE idSuivi = :idSuivi AND idAbonné = :idAbonné";
            //préparation des données 
            $stmt = $db->prepare($sql);
            $idsuiv =  $userToFollow->__get('id');
            $idabo = $this->__get('id');
            //attribution des paramètres
            $stmt->bindParam(':idSuivi', $idsuiv);
            $stmt->bindParam(':idAbonné', $idabo);
            //exécution
            $stmt->execute();

            // Si la requête renvoie 0, cela signifie que la relation de suivi n'existe pas encore, l'utilisateur va follow
            if ($stmt->fetchColumn() == 0) {
                // La relation de suivi n'existe pas encore, nous pouvons donc l'ajouter
                $sql = "INSERT INTO Abonnement (idSuivi, idAbonné) VALUES (:idSuivi, :idAbonné)";
            } else {//unfollow
                // La relation de suivi existe déjà
                $sql = "DELETE FROM Abonnement WHERE idSuivi = :idSuivi AND idAbonné = :idAbonné";
            }
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':idSuivi', $idsuiv);
            $stmt->bindParam(':idAbonné', $idabo); 

            if ($stmt->execute()) {
                // Suivi réussi
                return true;
            } else {
                throw new AbonnementException();
            }
        }

    }

    /*
     * Méthode qui permet de savoir si l'utilisateur connecté suit l'utilisateur entré en paramètre
     */
    public function etreAbonne(User $userToFollow):bool{
        $db = ConnectionFactory::makeConnection();

        //on regarde si this suis déjà l'utilisateur
        $sql = "SELECT COUNT(*) FROM Abonnement WHERE idSuivi = :idSuivi AND idAbonne = :idAbonne";
        //préparation des données
        $stmt = $db->prepare($sql);
        $idsuiv =  $userToFollow->__get('id');
        $idabo = $this->__get('id');
        //attribution des paramètres
        $stmt->bindParam(':idSuivi', $idsuiv);
        $stmt->bindParam(':idAbonne', $idabo);
        //exécution
        $stmt->execute();
        return !$stmt->fetchColumn() == 0;
    }
     /*
     * Méthode qui permet de savoir si l'utilisateur connecté suit tag entré en paramètre
     */
    public function etreAbonneTag(int $idTag):bool{
        $db = ConnectionFactory::makeConnection();

        //on regarde si this suis déjà l'utilisateur
        $sql = "SELECT COUNT(*) FROM Abonnement WHERE idSuivi = :idSuivi AND idAbonne = :idAbonne";
        //préparation des données
        $stmt = $db->prepare($sql);
        $idsuiv =  $userToFollow->__get('id');
        $idabo = $this->__get('id');
        //attribution des paramètres
        $stmt->bindParam(':idSuivi', $idsuiv);
        $stmt->bindParam(':idAbonne', $idabo);
        //exécution
        $stmt->execute();
        return !$stmt->fetchColumn() == 0;
    }

    /**
     * Methode qui permet de suivre un Tag
     */
    public function followTag(int $idTag) {
        // si l'utilisateur est identifié
        if (self::isUserConnected()) {
            $db = ConnectionFactory::makeConnection();

            //on regarde si this suis déjà le tag
            $sql = "SELECT COUNT(*) FROM AbonnementTag WHERE idTag = :idTag AND email = :email";
            //préparation des données 
            $stmt = $db->prepare($sql);
            $email = $this->__get('id');
            //attribution des paramètres
            $stmt->bindParam(':idTag', $idTag);
            $stmt->bindParam(':email', $email);
            //exécution
            $stmt->execute();

            // Si la requête renvoie 0, cela signifie que la relation de suivi n'existe pas encore, l'utilisateur va follow
            if ($stmt->fetchColumn() == 0) {
                $sql = "INSERT INTO AbonnementTag (idTag,email) VALUES (:idTag, :email)";
            }else{
                // La relation de suivi existe déjà, l'utilisateur va unfollow:
                $sql = "DELETE FROM AbonnementTag
                            WHERE idTag = :idTag AND email = :email ;";
                
            }
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':idtag', $idTag);
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
}