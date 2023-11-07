<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\user\UserAuthentifie;

class UserListeTouitesAction extends Action
{
    private UserAuthentifie $user;

    // заменить для любого юзера
    public function __construct()
    {
        parent::__construct();
        $user = unserialize($_SESSION['User']);
        var_dump($user);
        $this->user = $user;
    }

    public function execute(): string
    {
        $html = '';
        if (isset($this->user)){
            $touites = $this->user->getTouites();
            var_dump($touites);
            foreach ($touites as $t){
                    //affichage (id)
                    $html .= $t[0] . "<br>";
            }
        }
        return $html;
    }
}