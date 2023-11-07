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

        $pdo=null;

        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->role = $role;
    }

    public static function inscription(string $nom , string $prenom , string $email, string $mdp){
        $role = 1;

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

            $stmt = null;

            $pdo = null;
    }




    public function getTouites(){
        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT idTouite from Touite Where email = ?';

        $st = $pdo->prepare($query);
        $st->execute([$this->email]);

        return $st->fetchAll();
    }



    public function connectUser(){
        $_SESSION['User'] = serialize($this);

    }

}