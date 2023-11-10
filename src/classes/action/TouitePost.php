<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\action\Action;


class TouitePost extends Action {
   
    public function __construct(){
        parent::__construct();
    }
    
    public function execute() : string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];


        if($methode === 'GET'){
            $html = "<form class='Touite' action='?action=touite-post' method='post' enctype='multipart/form-data'>
                <input type='textarea' placeholder='What is happening ?!' name='touite' autocomplete='off'>
                <input type='file' placeholder='<choose file>' name='image'>
                <button type='submit'>Post</button>
                </form>";
    } else if ($methode === 'POST') {
        // Vérifier si le fichier a été téléchargé avec succès
            $touite = filter_var($_POST['touite'], FILTER_SANITIZE_STRING);



            // Utiliser preg_match_all pour trouver toutes les occurrences de motifs commençant par #
            preg_match_all('/#([A-Za-z0-9_]+)/', htmlspecialchars_decode($touite, ENT_QUOTES), $matches);
            // Enlever les dièses (#) de chaque élément de l'Array

            $hashtags = array_filter(array_map(function($match) {
                return trim($match, '#');
            }, $matches[0]), 'strlen');
                        
            
            // Gestion de l'image
            $uploadDir = "image/"; // Remplacez par le chemin réel de votre répertoire
            $uploadFile = $uploadDir . basename($_FILES['image']['name']);
            $pathfile = $uploadFile;
            // Déplacer le fichier téléchargé vers le répertoire de destination
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);
            
            // Email, nom, prénom, rôle, texte, path, tag
            $tags= array('');
            if($pathfile==='image/'){
                $imm = "";
            }else{
                $imm = $pathfile; 
            }
            $user = unserialize($_SESSION['User']);
            Touite::publierTouite($user,$touite,$hashtags,$imm);//cree un touite //ajouter image et tags
            
            if (!empty($touite)) {
                $html .= "<h3>Touite x: " . $touite . "</h3>";
            } else {
                $html .= "<h3>You don't have select any picture and text.</h3>";
            }
        
    }
        
        return $html;
    }
    
}
