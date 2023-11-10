<?php

namespace iutnc\touiteur\user;

use iutnc\touiteur\bd\ConnectionFactory;
use PDO;
class UserAdmin extends UserAuthentifie
{

    public function __construct(string $email)
    {
        parent::__construct($email);
    }

    public static function trouveInfluenceurs():string{
        $pdo = ConnectionFactory::makeConnection();

        $sql = "SELECT idSuivi, COUNT(idAbonne) AS followers_count
                FROM Abonnement
                GROUP BY idSuivi
                ORDER BY followers_count DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $html = '';
        foreach ($users as $user) {
            $html .= "<li>User :" . $user['idSuivi'] . " has " . $user['followers_count'] . " followers.</li>";
        }

        return $html;
    }

    public static function tendances():string{
        $pdo = ConnectionFactory::makeConnection();

        $sql = "SELECT t.idTag, t.libelle, COUNT(t2.idTouite) AS mention_count
        FROM Tag t
        LEFT JOIN Tag2Touite t2 ON t.idTag = t2.idTag
        GROUP BY t.idTag, t.libelle
        ORDER BY mention_count DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $html= '';
        $html .=  "<div class='admin'>";
        for ( $i =0 ; $i<5;$i++) {
            $tag = $tags[$i];
            echo $tag['idTag'];
            $html .=  "<div class='admin-trend'>";
            $html .= "<p>".($i+1)." -</p><a class='trend' " . "href=?action=recherche&tag=%23".$tag['libelle']."> #" . $tag['libelle'] ."</a>";
            $html .= "<p>   Mentions: " . $tag['mention_count'] . "<br/></p>";
            $html .=  '</div>';
        }
        $html .=  '</div>';
        return $html;
    }
}