<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use  iutnc\touiteur\bd\ConnectionFactory as ConnectionFactory;
use iutnc\touiteur\render\Renderer;
use iutnc\touiteur\render\TouiteRenderer;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\user\UserAuthentifie;
use PDO;

class TouiteDetailAction extends Action {
    public function execute(): string {
        $db = ConnectionFactory::makeConnection();

        $html = "";
        $tags=array();
        if (isset($_GET['id'])) {
            $id_touite = $_GET['id'];

            // Récupère les informations du touite
            $sql = "SELECT t.texte, t.datePublication, t.email, u.nom, u.prenom
                    FROM Touite t
                    inner JOIN Utilisateur u ON t.email = u.email
                    WHERE t.idTouite = :id_touite";

            $touiteDetails = $db->prepare($sql);
            $touiteDetails->bindParam(':id_touite', $id_touite, PDO::PARAM_INT);
            $touiteDetails->execute();

            if ($touiteDetails->rowCount() > 0) {
                $row = $touiteDetails->fetch(PDO::FETCH_ASSOC);
                $email=$row['email'];
                $texte=$row['texte'];
               
                // Affiche l'image si elle existe en utilisant la classe d'image
                if (!empty($row['ImagePath'])) {
                    $imagepath=$row['ImagePath'];
                }

                // Affiche les hashtags si il y en a
                $sql1 = "SELECT t.libelle
                        FROM Tag t
                        JOIN Tag2Touite t2t ON t.idTag = t2t.idTag
                        WHERE t2t.idTouite = :id_touite";

                $hashtags = $db->prepare($sql1);
                $hashtags->bindParam(':id_touite', $id_touite, PDO::PARAM_INT);
                $hashtags->execute();

                if ($hashtags->rowCount() > 0) {
                  
                    while ($row = $hashtags->fetch(PDO::FETCH_ASSOC)) {
                        array_push($tags,$row['libelle']);
                    }
                }

                //Affiche le nombre de likes
                $sql2 = "SELECT COUNT(*) AS nb_likes
                        FROM Note n
                        WHERE idTouite = :id_touite and n.note = 1";

                //Affiche le nombre de dislikes
                $sql3 = "SELECT COUNT(*) AS nb_dislikes
                        FROM Note n
                        WHERE idTouite = :id_touite and n.note = -1";

                $likes = $db->prepare($sql2);
                $likes->bindParam(':id_touite', $id_touite, PDO::PARAM_INT);
                $likes->execute();


                if ($likes->rowCount() > 0) {
                    $row = $likes->fetch(PDO::FETCH_ASSOC);
                    $nblike=$row['nb_likes'];
                }


                //Affiche le nombre de dislikes
                $sql3 = "SELECT COUNT(*) AS nb_dislikes
                        FROM Note n
                        WHERE idTouite = :id_touite and n.note = -1";

                $likes = $db->prepare($sql3);
                $likes->bindParam(':id_touite', $id_touite, PDO::PARAM_INT);
                $likes->execute();

                if ($likes->rowCount() > 0) {
                    $row = $likes->fetch(PDO::FETCH_ASSOC);
                    $nbdislike= $row['nb_dislikes'];
                }


                $sql = "SELECT cheminFichier 
                FROM Image
                left join Touite on Touite.idIm=Image.idIm
                where email=? and idTouite=?;";
                $idtouite=intval($id_touite);
                $resultset = $db->prepare($sql);
                $resultset->bindParam(1,$email, PDO::PARAM_STR);
                $resultset->bindParam(2,$id_touite, PDO::PARAM_INT);
                $resultset->execute();
                $res = $resultset->fetch();

                
                $user = unserialize($_SESSION['User']);
                $touiteRenderlong = new TouiteRenderer(new Touite($idtouite));//---, $imagepath, $tags, intval($id_touite)

                $html.=$touiteRenderlong->render(Renderer::LONG);
            }
        }
        return $html;
    }
}