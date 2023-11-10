<?php

namespace iutnc\touiteur\touite;

use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\touite\Touite;
class TouiteLike
{
    private Touite $touite;

    public function __construct($touite)
    {
        $this->touite = $touite;
    }

    public function modifNote($email, $note)
    {
        $pdo = ConnectionFactory::makeConnection();

        $query = "SELECT note FROM Note WHERE idTouite = :idTouite AND email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['idTouite' => $this->touite->__get('id'), 'email' => $email]);
        $existingNote = $stmt->fetch();

        if ($existingNote) {
            if ((int)$existingNote['note'] === $note) {
                $query = "DELETE FROM Note WHERE idTouite = :idTouite AND email = :email";
                $stmt = $pdo->prepare($query);
                $stmt->execute(['idTouite' => $this->touite->__get('id'), 'email' => $email]);
            } else {

                $query = "UPDATE Note SET note = :note WHERE idTouite = :idTouite AND email = :email";
                $stmt = $pdo->prepare($query);
                $stmt->execute(['note' => $note, 'idTouite' => $this->touite->__get('id'), 'email' => $email]);
            }
        } else {

            $query = "INSERT INTO Note (idTouite, email, note) VALUES (:idTouite, :email, :note)";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['idTouite' => $this->touite->__get('id'), 'email' => $email, 'note' => $note]);
        }
        $pdo=null;
    }




}