<?php

/**
 * déclarations des namespaces
 */
namespace iutnc\touiteur\user;
use http\Encoding\Stream;
use iutnc\touiteur\bd\ConnectionFactory;

class UserAuthentifie extends User{

    /**
     * déclarations des attributs
     */
    protected string $email, $nom, $prenom;
    protected int $role;

    /**
     * Constructeur
     */

    public function __construct(string $email){
        $this->email = $email;

        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT role from utilisateur Where email = ?';
        $st = $pdo->prepare($query);
        $st->execute([$email]);
        $role = $st->fetch()['role'];

        $query = 'SELECT nom from utilisateur Where email = ?';
        $st = $pdo->prepare($query);
        $st->execute([$email]);
        $nom = $st->fetch()['nom'];

        $query = 'SELECT prenom from utilisateur Where email = ?';
        $st = $pdo->prepare($query);
        $st->execute([$email]);
        $prenom = $st->fetch()['prenom'];

        $pdo=null;

        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->role = $role;
    }


/*
 * Méthode qui permet de récupérer les touites d'un utilisateur
 */

    public function getTouites(){
        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT idTouite from Touite Where email = ?';

        $st = $pdo->prepare($query);
        $st->execute([$this->email]);

        return $st->fetchAll();
    }



    /*
     * Méthode qui permet de connecter un utilisateur
     */
    public function connectUser(){
        $_SESSION['User'] = serialize($this);
    }

    /*
     * Méthode qui permet de vérifier que l'utilisateur est authentifié
     */
    public static function isUserConnected(): bool
    {
        return isset($_SESSION['User']);
    }

    /*
     * Méthode qui permet de suivre un utilisateur entré en paramètre
     */
    public function followUser(User $userToFollow) {
        // Assurez-vous que $this représente l'utilisateur authentifié.
        if (self::isUserConnected()) {
            // Ajoutez une entrée dans la table Abonnement pour enregistrer la relation de suivi.
            $db = ConnectionFactory::makeConnection();

            // Vérifiez d'abord si la relation de suivi n'existe pas déjà
            $sql = "SELECT COUNT(*) FROM Abonnement WHERE idSuivi = :idSuivi AND idAbonné = :idAbonné";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':idSuivi', $userToFollow->getId());
            $stmt->bindParam(':idAbonné', $this->getId());
            $stmt->execute();

            // Si la requête renvoie 0, cela signifie que la relation de suivi n'existe pas encore
            if ($stmt->fetchColumn() == 0) {
                // La relation de suivi n'existe pas encore, nous pouvons donc l'ajouter
                $sql = "INSERT INTO Abonnement (idSuivi, idAbonné) VALUES (:idSuivi, :idAbonné)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':idSuivi', $userToFollow->getId());
                $stmt->bindParam(':idAbonné', $this->getId());

                if ($stmt->execute()) {
                    // Suivi réussi
                    return true;
                } else {
                    // Gérer l'erreur en cas d'échec du suivi
                    return false;
                }
            } else {
                // La relation de suivi existe déjà, vous pouvez gérer cette situation comme vous le souhaitez (par exemple, en affichant un message).
                echo "Vous suivez déjà cet utilisateur.";
                return false;
            }
        }

    }







}