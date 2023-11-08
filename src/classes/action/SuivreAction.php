<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiteur\action\Action;
use  iutnc\touiteur\bd\ConnectionFactory as ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;

class SuivreAction extends Action
{


    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;


    public function __construct()
    {
        parent::__construct();
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    //A ADAPTER POUR SUIVRE UN UTILISATEUR GRACE A UN BOUTON S'ABONNER
    public function execute() : string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];

        if($methode ==='GET'){
            $html ="    <form class='Touite' action='?action=touite-post' method='post'>
                        <button type='submit'>S'abonner</button>
                        </form>";

        }else if ($methode === 'POST') {
            $touite = filter_var($_POST['touite'], FILTER_SANITIZE_STRING);

            //email nom prenom role texte path tag
            $tags= array('');

            $touiteobject=new Touite(new UserAuthentifie($_SESSION["email"],"","",1),$touite,$_POST['image'],$tags);
            $touiteobject->publierTouite();
            if(!empty($touite)){
                $html .= "<h3>Touite : " . $touite . "</h3>";
            }else{
                echo "<h3>Vous n'avez selectionnez ni une image, ni saisi un texte</h3>";
            }
        }

        return $html;
    }

    /*public function execute(): string
    {
        // Vérifiez si l'utilisateur est authentifié.
        if (UserAuthentifie::isUserConnected()) {
            // Assurez-vous que l'utilisateur est authentifié.
            $user = UserAuthentifie::connectUser();

            // Gérez le suivi de l'utilisateur cible (peut être passé via un formulaire POST).
            if (isset($_POST['userToFollowId'])) {
                $userToFollowId = $_POST['userToFollowId'];
                $db = ConnectionFactory::makeConnection();

                // Chargez l'utilisateur cible à partir de la base de données lorsque l'on clique sur le nom de l'utlisateur
                // pour afficher son profil.
                $sql = "SELECT * FROM utilisateur WHERE email = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$userToFollowId]);

                if ($stmt->rowCount() > 0) {
                    $userToFollow = $stmt->fetch();

                    // Appelez la méthode followUser pour permettre à l'utilisateur authentifié de suivre l'utilisateur cible grâce a un bouton
                    // "s'abonner" sur la page de profil de l'utilisateur cible.
                    $user->followUser($userToFollow);

                }

            }

            // Vous pouvez ajouter ici la logique pour afficher la liste d'utilisateurs
            // ou une interface permettant à l'utilisateur de choisir qui suivre.

            $html = '<h2>Page de Suivi</h2>';
            //Afficher le profil de l'utilisateur auquel on veut s'abonner
            $html .= '<form action="suivre.php" method="post">';
            $html .= '<label for="userToFollowId">Email de l\'utilisateur à suivre</label>';
            //Ajout du bouton s'abonner
            $html .= '<input type="submit" value="S\'abonner">';
            $html .= '</form>';
            $html .= '<hr>';
            //Affiche la liste des touites de l'utilisateur auquel on veut s'abonner à partir de la base de données


        }
        return $html;


    }*/
}