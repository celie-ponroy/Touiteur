<?php
declare(strict_types= 1);

namespace iutnc\touiteur\touite;

use iutnc\touiteur\bd\ConnectionFactory;
use PDO;

class Tag{
    private $id;
    private $libelle;
    private $desciption;
    
    function __construct(string $id){

        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT libelle from Tag Where idTag = ?';
        $st = $pdo->prepare($query);
        $st->execute([$id]);
        $libelle = $st->fetch()['libelle'];

        $query = 'SELECT description from Tag Where idTag = ?';
        $st = $pdo->prepare($query);
        $st->execute([$id]);
        $desciption = $st->fetch()['description'];



        $this->id = $id;
        $this->libelle = $libelle;
        $this->desciption = $desciption;
    }

    public function findTaggedTw(){
        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT Touite.idTouite from Touite 
                    inner join Tag2Touite on Touite.idTouite = Tag2Touite.idTouite  
                    inner join Tag on Tag.idTag = Tag2Touite.idTag
                    Where Tag.idTag = ?';

        $st = $pdo->prepare($query);
        $st->execute([$this->id]);


        $tags = [];
        $results = $st->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $tags[] = new Touite($row['idTouite']);
        }

        return $tags;
    }
    
}