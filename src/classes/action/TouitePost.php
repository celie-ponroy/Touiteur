<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;
use iutnc\touiter\touite\Touite;
use iutnc\touiteur\action\Action;
use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;
use PDO;

class TouitePost extends Action {

    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;
   
    public function __construct(){
        parent::__construct();
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }
    
    public function execute() : string{
        $html = "";
        $methode = $_SERVER['REQUEST_METHOD'];

        if($methode ==='GET'){
            $html ="    <form class='Touite' action='?action=touite-post' method='post'>
                        <input type='textarea' placeholder='What is happening?!' name='touite'autocomplete='off'>
                       
                        <input type='file' placeholder='<choose file>' name='image'>
                        <button type='submit'>Post</button>
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
    
}
?>