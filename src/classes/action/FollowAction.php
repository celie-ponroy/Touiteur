<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\user\UserAuthentifie;

class FollowAction extends Action
{
    public function __construct()
    {
        parent::__construct();

    }
    /**
     * Permet de Follow un User
     */
    public function execute(): string
    {
        $user2follow = $_GET['us'];
        $userA = UserAuthentifie::getUser();
        $userA->followUser(new UserAuthentifie($user2follow));
        $urlAvant = filter_var($_POST['redirect_to'], FILTER_SANITIZE_URL);
        //retourne sur url d'avant
        if (isset($urlAvant) ){
            header('Location: ' . $urlAvant);
            exit;
        }
        return '';
    }
}