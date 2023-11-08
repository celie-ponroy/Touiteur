<?php
declare(strict_types= 1);

namespace iutnc\touiteur\touite;

use iutnc\touiteur\bd\ConnectionFactory;

class Tag{
    private $id;
    private $libelle;
    private $desciption;
    
    function __construct(string $id){

        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT libelle from  Where id = ?';
        $st = $pdo->prepare($query);
        $st->execute([$id]);
        $libelle = $st->fetch()['libelle'];

        $query = 'SELECT nom from Utilisateur Where id = ?';
        $st = $pdo->prepare($query);
        $st->execute([$id]);
        $desciption = $st->fetch()['description'];



        $this->id = $id;
        $this->libelle = $libelle;
        $this->desciption = $desciption;
    }

    public function findTaggedTw(){
        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT idTouite from Touite 
                    inner join Tag2Touite on Touite.idTouite = Tag2Touite.idTouite  
                    inner join Tag on Tag.idTag = Tag2Touite.idTag
                    Where tags = ?';

        $st = $pdo->prepare($query);
        $st->execute([$this->id]);

        return $st->fetchAll();
    }
    
}