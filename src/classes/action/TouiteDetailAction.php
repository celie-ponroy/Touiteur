<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use  iutnc\touiteur\bd\ConnectionFactory as ConnectionFactory;
use PDO;

class TouiteDetailAction extends Action {
    public function execute(): string {
        $db = ConnectionFactory::makeConnection();

        $html = "";
        if (isset($_GET['id'])) {
            $id_touite = $_GET['id'];

            // Récupère les informations du touite
            $sql = "SELECT t.texte, t.datePublication, t.email, u.nom, u.prenom
                    FROM touite t
                    JOIN utilisateur u ON t.email = u.email
                    WHERE t.idTouite = :id_touite";

            $touiteDetails = $db->prepare($sql);
            $touiteDetails->bindParam(':id_touite', $id_touite, PDO::PARAM_INT);
            $touiteDetails->execute();

            if ($touiteDetails->rowCount() > 0) {
                $row = $touiteDetails->fetch(PDO::FETCH_ASSOC);
                echo "<h1>Touite de " . $row['Prénom'] . " " . $row['Nom'] . "</h1>";
                echo "<p>Date : " . $row['Date'] . "</p>";
                echo "<p>Texte : " . $row['Texte'] . "</p>";


                // Affiche l'image si elle existe en utilisant la classe d'image
                if (!empty($row['ImagePath'])) {
                    echo '<div class="TouiteImage"><img src="' . $row['ImagePath'] . '" alt="Image du touite"></div>'; //A VERIFIER
                }

                // Affiche les hashtags si il y en a
                $sql1 = "SELECT t.libelle
                        FROM tag t
                        JOIN tag2touite t2t ON t.idTag = t2t.idTag
                        WHERE t2t.idTouite = :id_touite";

                $hashtags = $db->prepare($sql1);
                $hashtags->bindParam(':id_touite', $id_touite, PDO::PARAM_INT);
                $hashtags->execute();

                if ($hashtags->rowCount() > 0) {
                    echo "<p>Hashtags : ";
                    while ($row = $hashtags->fetch(PDO::FETCH_ASSOC)) {
                        echo "#" . $row['Texte'] . " ";
                    }
                    echo "</p>";
                }

                //Affiche le nombre de likes
                $sql2 = "SELECT COUNT(*) AS nb_likes
                        FROM note n
                        WHERE idTouite = :id_touite and n.note = 1";

                //Affiche le nombre de dislikes
                $sql3 = "SELECT COUNT(*) AS nb_dislikes
                        FROM note n
                        WHERE idTouite = :id_touite and n.note = -1";

                $likes = $db->prepare($sql2);
                $likes->bindParam(':id_touite', $id_touite, PDO::PARAM_INT);
                $likes->execute();

                if ($likes->rowCount() > 0) {
                    $row = $likes->fetch(PDO::FETCH_ASSOC);
                    echo "<p>Nombre de likes : " . $row['nb_likes'] . "</p>";
                    echo "<p>Nombre de dislikes : " . $row['nb_dislikes'] . "</p>";
                }

            } else {
                $html = "Touite non trouvé.";
            }
        }

        return $html;
    }
}

// Créez une instance de la classe TouiteDetailAction
$action = new TouiteDetailAction();

// Exécutez l'action
$html = $action->execute();

// Affichez le résultat HTML
echo $html;

?>
