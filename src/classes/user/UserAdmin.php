<?php

namespace iutnc\touiteur\user;

use iutnc\touiteur\bd\ConnectionFactory;
use PDO;
class UserAdmin extends UserAuthentifie
{
    public static int $limite=5;
    public function __construct(string $email)
    {
        parent::__construct($email);
    }

    public static function trouveInfluenceurs():string{
        $pdo = ConnectionFactory::makeConnection();

        $sql = "SELECT idSuivi, COUNT(idAbonne) AS followers_count
                FROM Abonnement
                GROUP BY idSuivi
                ORDER BY followers_count DESC
                LIMIT :limite";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limite', UserAdmin::$limite, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $html = '';
        $i = 0;
        foreach ($users as $user) {
            $i++;
            $html .= "<br> $i - " . $user['idSuivi'] . " has " . $user['followers_count'] . " followers.";
        }

        return $html;
    }

    public static function tendances():string{
        $pdo = ConnectionFactory::makeConnection();

        $sql = "SELECT t.idTag, t.libelle, COUNT(t2.idTouite) AS mention_count
        FROM Tag t
        LEFT JOIN Tag2Touite t2 ON t.idTag = t2.idTag
        GROUP BY t.idTag, t.libelle
        ORDER BY mention_count DESC
        LIMIT :limite";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limite', UserAdmin::$limite, PDO::PARAM_INT);
        $stmt->execute();

        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $html= '';
        $html .= "<h2 class='titreTrends'>Top Trends</h2>";
        $i = 0;
        
        foreach ($tags as $tag) {
            $i++;
            $html .= "<h3 class = 'adminT' ><br>".($i)." -<a class='trend' " . "href=?action=recherche&tag=%23".$tag['libelle']."> #" . $tag['libelle'] ."</a>";
            $html .= " <p class='trend-mention'>  Mentions: " . $tag['mention_count'] . "</p></h3><br/>";
        }
        
        return $html;
    }
}