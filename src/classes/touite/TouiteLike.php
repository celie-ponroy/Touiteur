<?php

namespace iutnc\touiteur\touite;

use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\touite\Touite;

/**
 * Class TouiteLike
 */
class TouiteLike
{
    private Touite $touite;

    /**
     * Constructeur
     * @param Touite $touite
     */
    public function __construct($touite)
    {
        $this->touite = $touite;
    }

    /**
     * Méhtode modifNote qui modifie la note d'un touite
     * @param $email email de l'utilisateur
     * @param $note note de l'utilisateur
     * @return void
     */
    public function modifNote($email, $note)
    {
        // Connexion à la base de données
        $pdo = ConnectionFactory::makeConnection();

        // Vérification de l'existence d'une note pour le touite
        $query = "SELECT note FROM Note WHERE idTouite = :idTouite AND email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['idTouite' => $this->touite->__get('id'), 'email' => $email]);
        $existingNote = $stmt->fetch();

        // Si une note existe déjà pour le touite
        if ($existingNote) {
            if ((int)$existingNote['note'] === $note) {
                // Si la note est la même que celle déjà existante, on supprime la note
                $query = "DELETE FROM Note WHERE idTouite = :idTouite AND email = :email";
                $stmt = $pdo->prepare($query);
                $stmt->execute(['idTouite' => $this->touite->__get('id'), 'email' => $email]);
            } else {
                // Sinon, on modifie la note
                $query = "UPDATE Note SET note = :note WHERE idTouite = :idTouite AND email = :email";
                $stmt = $pdo->prepare($query);
                $stmt->execute(['note' => $note, 'idTouite' => $this->touite->__get('id'), 'email' => $email]);
            }
        } else {

            // Sinon, on ajoute la note
            $query = "INSERT INTO Note (idTouite, email, note) VALUES (:idTouite, :email, :note)";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['idTouite' => $this->touite->__get('id'), 'email' => $email, 'note' => $note]);
        }
        $pdo=null;
    }




}