<?php
declare(strict_types= 1);

namespace iutnc\touiteur\touite;

use iutnc\touiteur\bd\ConnectionFactory;
use iutnc\touiteur\user\UserAuthentifie;
use PDO;

class Note{
    protected UserAuthentifie $user;
    
    function __construct(UserAuthentifie $user){
        $this->user = $user;
    }



    //idtouite email note
    function noterTouite(int $idtouite, int $noteUser):array{

        //on récupere la potentiel note de l'utilisateur suivant un touite donné  
        $db = ConnectionFactory::makeConnection();
        //note
        $query = 'SELECT note, count(*) as nbnote
        FROM Note
        WHERE idTouite = ? AND email = ?
        group by note
        order by nbnote';
        $email=$this->user->__get('email');
        $resultset = $db->prepare($query);
        $resultset->bindParam(1,$idtouite, PDO::PARAM_INT);
        $resultset->bindParam(2,$email,PDO::PARAM_STR);
        $resultset->execute();
        $row=$resultset->fetch();
        
        //on mets à jour la variable note
        $note=null;
        if($row!=false){
            $note=$row['nbnote'];
            if($row['note']===-1)
                $note=$note*(-1);
        }

        $action='rien';
        $sql='';

        if($noteUser===1&& $note=== 1){ //cas où like =1 et on ajoute encore 1 alors suppr la ligne
            $action='supprimer-note-like';
            $sql='DELETE FROM Note 
                    WHERE  idTouite=:idTouite AND email=:email AND note=:note;';
        }elseif($noteUser=== -1&&$note===-1){ //cas où like = -1 et on ajoute encore -1 alors suppr la ligne
            $action='supprimer-note-dislike';
            $sql='DELETE FROM Note 
            WHERE  idTouite=:idTouite AND email=:email AND note=:note;';
        }elseif($noteUser=== 1&&$note===null){ //cas ou il n'y a pas de like et on en ajoute un
            $action= 'ajouter-like';
            $sql='Insert into Note values(:idTouite,:email,:note);';
        }elseif ($noteUser=== -1&&$note===null) { //cas ou il n'y a pas de dislike et on en ajoute un
            $action= 'ajouter-dislike';
            $sql='Insert into Note values(:idTouite,:email,:note);';
        }elseif ($noteUser=== 1&&$note===-1) { //cas ou on ajoute un like alors qu'il y a un dislike
            $action= 'ajouter-like-dislike';
            $sql='UPDATE Note
                SET note = :note
                WHERE email=:email AND idTouite=:idTouite;';
                 
        }
        elseif ($noteUser=== -1&&$note===1) { //cas ou on ajoute un dislike alors qu'il y a un like
            $action= 'ajouter-dislike-like';
            $sql='UPDATE Note
            SET note = :note
            WHERE email= :email AND idTouite= :idTouite;';
            
        }
        //echo $action;
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




    static function __getLikeInitial(int $idtouite):array{

        $db = ConnectionFactory::makeConnection();
         //dislikeee
         $query = 'SELECT count(*) as nbnote
         FROM Note
         WHERE idTouite = ?';


         
         $resultset = $db->prepare($query);
         $resultset->bindParam(1,$idtouite, PDO::PARAM_INT);
         $resultset->execute();
 
         $row=$resultset->fetch();
 
         $note=null;
         $like=null;
         $dislike=null;

         if($row!=false){
             $note = $row['nbnote'];//like
         }

         if($note===1){
            $like='image/like_full.svg';
            $dislike='image/dislike_empty.svg';
         }elseif($note===-1){
            $like='image/like_empty.svg';
            $dislike='image/dislike_full.svg';
         }else{
            $note=null;
         }
  

 
         return array();
    }
    static function getnbLike(int $idtouite):int{
        $db = ConnectionFactory::makeConnection();

        $query = 'SELECT count(*) as nbnote
        FROM Note
        WHERE idTouite = ?
        And note =?;';
        $val=1;
        $resultset = $db->prepare($query);
        $resultset->bindParam(1,$idtouite, PDO::PARAM_INT);
        $resultset->bindParam(2, $val, PDO::PARAM_INT);
        $resultset->execute();

        $row=$resultset->fetch();
        
        $nblikes=null;
        if($row!=false){
            $nblikes = $row['nbnote'];//like
        }
        if($nblikes===null)
            $nblikes= 0;
        return $nblikes;
    }
    static function getnbDislike(int $idtouite):int{
        $db = ConnectionFactory::makeConnection();

        $query = 'SELECT count(*) as nbnote
        FROM Note
        WHERE idTouite = ?
        And note =?;';
        $val=-1;
        $resultset = $db->prepare($query);
        $resultset->bindParam(1,$idtouite, PDO::PARAM_INT);
        $resultset->bindParam(2,$val, PDO::PARAM_INT);
        $resultset->execute();

        $row=$resultset->fetch();

        $nbdislike=null;
        if($row!=false){
            $nbdislike = $row['nbnote'];//like
        }
        if($nbdislike===null)
            $nbdislike= 0;
    
    return $nbdislike;
    }
}