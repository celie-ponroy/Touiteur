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
            $html .= "User " . $user['idSuivi'] . " has " . $user['followers_count'] . " followers.<br/>";
        }

        return $html;
    }

    public static function tendances():string{
        $pdo = ConnectionFactory::makeConnection();

        $sql = "SELECT t.idTag, t.libelle, COUNT(t2.idTouite) AS mention_count
        FROM tag t
        LEFT JOIN tag2touite t2 ON t.idTag = t2.idTag
        GROUP BY t.idTag, t.libelle
        ORDER BY mention_count DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $html= '';
        foreach ($tags as $tag) {
            $html .= "Tag: " . $tag['libelle'] . " - Mentions: " . $tag['mention_count'] . "<br/>";
        }
        return $html;
    }
}