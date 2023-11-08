<?php

namespace iutnc\touiteur\user;

class Profil{

    /**
     * déclarations des attributs
     */
    protected string $email, $nom, $prenom;

    //Profil de la personne a laquelle on veut acceder et éventuellement s'abonner
    public function __construct($email){
        $this->email = $email;

        //Nom de la personne a laquelle on veut acceder et éventuellement s'abonner
        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT nom from utilisateur Where email = ?';
        $st = $pdo->prepare($query);
        $st->execute([$email]);
        $nom = $st->fetch()['nom'];

        //Prénom de la personne a laquelle on veut acceder et éventuellement s'abonner
        $query = 'SELECT prenom from utilisateur Where email = ?';
        $st = $pdo->prepare($query);
        $st->execute([$email]);
        $prenom = $st->fetch()['prenom'];

        //Liste des touites de la personne a laquelle on veut acceder et éventuellement s'abonner
        $query = 'SELECT idTouite from Touite Where email = ?';
        $st = $pdo->prepare($query);
        $st->execute([$email]);
        $touites = $st->fetchAll();

        $pdo=null;

        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->touites = $touites;
    }




}




