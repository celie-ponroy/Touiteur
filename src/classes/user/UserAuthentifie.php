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
    protected string $login, $date;
    protected int $role;

    /**
     * Constructeur
     */
    public function __construct(string $login, string $date, int $role){
        $this->login = $login;
        $this->date = $date;
        $this->role = $role;
    }

    public function getTouites(){
        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT message, pictureFile from touite Where email = ?';
        $st = $pdo->prepare($query);
        $st->execute([$this->login]);

        return $st->fetchAll();
    }


}