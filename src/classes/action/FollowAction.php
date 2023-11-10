<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\user\UserAuthentifie;

class FollowAction extends Action
{

    public function execute(): string
    {
        $user2follow = $_GET['us'];
        $userA = UserAuthentifie::getUser();
        $userA->followUser(new UserAuthentifie($user2follow));

        if (isset($_POST['redirect_to'])) {
            header('Location: ' . $_POST['redirect_to']);
            exit;
        }
        return '';
    }
}