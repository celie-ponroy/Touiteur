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