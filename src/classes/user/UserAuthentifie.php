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
    //оставить только емейл остальное брать с бд
    public function __construct(string $email, string $nom, string $prenom,int $role){
        $this->email = $email;
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

}