<?php

/**
 * déclarations des namespaces
 */
namespace iutnc\touiteur\user;
use iutnc\touiteur\bd\ConnectionFactory;

class UserAuthentifie extends User{

    /**
     * déclarations des attributs
     */
    protected string $email,$nom, $prenom;
    protected int $role;

    /**
     * Constructeur
     */
    public function __construct(string $email, string $nom, string $prenom ,int $role){
        $this->email = $email;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->role = $role;
    }

    public function getTouites(){
        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT texte, chemin from Touite inner join Image on Touite.idIm = Image.idIm
                    Where email = ?';
        $st = $pdo->prepare($query);
        $st->execute([$this->email]);

        return $st->fetchAll();
    }


}