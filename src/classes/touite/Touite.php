<?php
declare(strict_types= 1);

namespace iutnc\touiteur\touite;
use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\user\User;
/**
 * class Touite
 */
class Touite{
    protected $texte;//contenu du touite
    protected $user ; //l'auteur
    protected $date;//date de publication du touite
    protected $tags; // table de tags

    protected int $idtouite;

    /**
     * contructeur
     */
    function __construct( string $texte, User $user){
        $this->texte = $texte;
        $this->user = $user;
        $date = new \DateTime();
    }

    /**
     * toString 
     */
    function __toString(){
        $res = "@".$this->user;
        return $res."<br>\n".$this-> texte;
        //ajouter les tags
        
        
    }

    public function findTaggedTw(){
        $pdo = ConnectionFactory::makeConnection();
        $query = 'SELECT idTouite from Touite 
                    inner join Tag2Touite on Touite.idTouite = Tag2Touite.idTouite  
                    inner join Tag on Tag.idTag = Tag2Touite.idTag
                    Where tags = ?';

        $st = $pdo->prepare($query);
        $st->execute([$this->email]);

        return $st->fetchAll();
    }
}