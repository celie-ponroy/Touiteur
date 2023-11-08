<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\render\Renderer;
use iutnc\touiteur\render\TouiteRenderer;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\action\Action;
use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;
use PDO;

class TouitePost extends Action {
   
    public function __construct(){
        parent::__construct();
    }
    
    public function execute() : string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];

        if($methode === 'GET'){
            $html = "<form class='Touite' action='?action=touite-post' method='post' enctype='multipart/form-data'>
                <input type='textarea' placeholder='What is happening?!' name='touite' autocomplete='off'>
                <input type='file' placeholder='<choose file>' name='image'>
                <button type='submit'>Poster</button>
                </form>";
    } else if ($methode === 'POST') {
        // Vérifier si le fichier a été téléchargé avec succès
            $touite = filter_var($_POST['touite'], FILTER_SANITIZE_STRING);
            
            // Gestion de l'image
            $uploadDir = "image/"; // Remplacez par le chemin réel de votre répertoire
            $uploadFile = $uploadDir . basename($_FILES['image']['name']);
            $pathfile = $uploadFile;
            // Déplacer le fichier téléchargé vers le répertoire de destination
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);
            
            // Email, nom, prénom, rôle, texte, path, tag
            $tags = array('');
            $touiteobject = new Touite(new UserAuthentifie($_SESSION["email"]), $touite, $tags, $pathfile);
            // Ajouter l'image
            $touiteobject->publierTouite();
            
            if (!empty($touite)) {
                $html .= "<h3>Touite x: " . $touite . "</h3>";
            } else {
                $html .= "<h3>Vous n'avez sélectionné ni une image, ni saisi de texte</h3>";
            }
        
    }
    
            
        
        
        return $html;
    }
    
}
