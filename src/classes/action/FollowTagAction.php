<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\touite\Tag;
use iutnc\touiteur\user\UserAuthentifie;

class FollowTagAction extends Action
{


    public function execute(): string
    {
       $user = UserAuthentifie::getUser();

        $tagLibel = substr($_GET['tag'], 1);
        $urlAvant = filter_var($_POST['redirect_to'], FILTER_SANITIZE_URL);
        $user->followTag((new Tag( $tagLibel))->__get('id'));
        //retourne sur url d'avant
            if (isset($urlAvant)) {
                header('Location: ' . $urlAvant);
                exit;
            }
        return '';
    }
}