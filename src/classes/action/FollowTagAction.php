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

        $user->followTag((new Tag( $tagLibel))->__get('id'));

        if (isset($_POST['redirect_to'])) {
            header('Location: ' . $_POST['redirect_to']);
            exit;
        }
        return '';
    }
}