<?php
declare(strict_types= 1);

namespace iutnc\touiteur\touite;

use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;
use PDO;

/**
 * Class Note
 */
class Note{
    protected UserAuthentifie $user;

    /**
     * Constructeur
     * @param UserAuthentifie $user utilisateur authentifié
     */
    function __construct(UserAuthentifie $user){
        $this->user = $user;
    }

    /**
     * Méthode noterTouite qui permet de noter un touite
     * @param int $idtouite identifiant du touite
     * @param int $noteUser note de l'utilisateur
     * @return array tableau contenant le nombre de like et de dislike
     */
    function noterTouite(int $idtouite, int $noteUser):array{

        //on récupere la potentiel note de l'utilisateur suivant un touite donné  
        $db = ConnectionFactory::makeConnection();

        //requete pour récupérer la note de l'utilisateur
        $query = 'SELECT note, count(*) as nbnote
        FROM Note
        WHERE idTouite = ? AND email = ?
        group by note
        order by nbnote';

        //on récupere l'email de l'utilisateur
        $email=$this->user->__get('email');
        $resultset = $db->prepare($query);
        $resultset->bindParam(1,$idtouite, PDO::PARAM_INT);
        $resultset->bindParam(2,$email,PDO::PARAM_STR);
        $resultset->execute();
        $row=$resultset->fetch();
        
        //on met à jour la variable note
        $note=null;
        if($row!=false){
            $note=$row['nbnote'];
            if($row['note']===-1)
                $note=$note*(-1);
        }

        $action='rien';
        $sql='';

        //cas où like =1 et on ajoute encore 1 alors supprime la ligne
        if($noteUser===1&& $note=== 1){
            $action='supprimer-note-like';
            //requete pour supprimer la note
            $sql='DELETE FROM Note 
                    WHERE  idTouite=:idTouite AND email=:email AND note=:note;';
        }elseif($noteUser=== -1&&$note===-1){ //cas où like = -1 et on ajoute encore -1 alors suppr la ligne
            $action='supprimer-note-dislike';
            //requete pour supprimer la note
            $sql='DELETE FROM Note 
            WHERE  idTouite=:idTouite AND email=:email AND note=:note;';
        }elseif($noteUser=== 1&&$note===null){ //cas ou il n'y a pas de like et on en ajoute un
            $action= 'ajouter-like';
            //requete pour ajouter la note
            $sql='Insert into Note values(:idTouite,:email,:note);';
        }elseif ($noteUser=== -1&&$note===null) { //cas ou il n'y a pas de dislike et on en ajoute un
            $action= 'ajouter-dislike';
            //requete pour ajouter la note
            $sql='Insert into Note values(:idTouite,:email,:note);';
        }elseif ($noteUser=== 1&&$note===-1) { //cas ou on ajoute un like alors qu'il y a un dislike
            $action= 'ajouter-like-dislike';
            //requete pour mettre à jour la note
            $sql='UPDATE Note
                SET note = :note
                WHERE email=:email AND idTouite=:idTouite;';
                 
        }
        elseif ($noteUser=== -1&&$note===1) { //cas ou on ajoute un dislike alors qu'il y a un like
            $action= 'ajouter-dislike-like';
            //requete pour mettre à jour la note
            $sql='UPDATE Note
            SET note = :note
            WHERE email= :email AND idTouite= :idTouite;';
            
        }
        //on execute la requete
        if($sql!==''){
            $resultset = $db->prepare($sql);
            $resultset->bindParam(':idTouite',$idtouite, PDO::PARAM_INT);
            $resultset->bindParam(':email',$email,PDO::PARAM_STR);
            $resultset->bindParam(':note',$noteUser,PDO::PARAM_INT);
            $resultset->execute();
        }



        //nb like et dislike
        //likeee
        $nblikes= Note::getnbLike($idtouite);
        //dislikeee
        $nbdislike=Note::getnbDislike($idtouite);

        return array($nblikes,$nbdislike,$action);
    }

    /**
     * Méthode __getLikeInitial qui retourne le like initial d'un touite
     * @param int $idtouite identifiant du touite
     * @return string[] tableau contenant le like et le dislike initial
     */
    function __getLikeInitial(int $idtouite):array{

        //on récupere la potentiel note de l'utilisateur suivant un touite donné
        $db = ConnectionFactory::makeConnection();

        //requete pour récupérer la note de l'utilisateur
         $query = 'SELECT note 
         FROM Note
         WHERE idTouite = ? AND email = ?';


        $email=$this->user->__get('email');
        $resultset = $db->prepare($query);
        $resultset->bindParam(1,$idtouite, PDO::PARAM_INT);
        $resultset->bindParam(2,$email, PDO::PARAM_STR);
        $resultset->execute();

        //on met à jour la variable note
         $row=$resultset->fetch();
         $note=null;
         $like=null;
         $dislike=null;

         //on met à jour la variable note
         if($row!=false){
             $note = $row['note'];//like
         }

         //on met à jour les images en fonction de la note
         if($note===1){
            $like='image/like_full.svg';
            $dislike='image/dislike_empty.svg';
         }elseif($note===-1){
            $like='image/like_empty.svg';
            $dislike='image/dislike_full.svg';
         }else{
            $note=null;
            $like='image/like_empty.svg';
            $dislike='image/dislike_empty.svg';
         }
        
        return array($like,$dislike);
    }

    /**
     * Méhtode getnbLike qui retourne le nombre de like d'un touite
     * @param int $idtouite identifiant du touite
     * @return int nombre de like
     */
    static function getnbLike(int $idtouite):int{

        //on se connecte à la base de données
        $db = ConnectionFactory::makeConnection();

        //requete pour récupérer le nombre de like
        $query = 'SELECT count(*) as nbnote
        FROM Note
        WHERE idTouite = ?
        And note =?;';
        $val=1;
        $resultset = $db->prepare($query);
        $resultset->bindParam(1,$idtouite, PDO::PARAM_INT);
        $resultset->bindParam(2, $val, PDO::PARAM_INT);
        $resultset->execute();

        //on met à jour la variable note
        $row=$resultset->fetch();

        //on met à jour la variable note
        $nblikes=null;
        if($row!=false){
            $nblikes = $row['nbnote'];//like
        }
        if($nblikes===null)
            $nblikes= 0;
        return $nblikes;
    }

    /**
     * Méthode getnbDislike qui retourne le nombre de dislike d'un touite
     * @param int $idtouite identifiant du touite
     * @return int nombre de dislike
     */
    static function getnbDislike(int $idtouite):int{

        //on se connecte à la base de données
        $db = ConnectionFactory::makeConnection();

        //requete pour récupérer le nombre de dislike
        $query = 'SELECT count(*) as nbnote
        FROM Note
        WHERE idTouite = ?
        And note =?;';
        $val=-1;
        $resultset = $db->prepare($query);
        $resultset->bindParam(1,$idtouite, PDO::PARAM_INT);
        $resultset->bindParam(2,$val, PDO::PARAM_INT);
        $resultset->execute();

        //on met à jour la variable note
        $row=$resultset->fetch();

        //on met à jour la variable note
        $nbdislike=null;
        if($row!=false){
            $nbdislike = $row['nbnote'];//like
        }
        if($nbdislike===null)
            $nbdislike= 0;

    
    return $nbdislike;
    }
}