<?php

namespace iutnc\touiteur\action;
use iutnc\touiteur\render\Renderer;
use iutnc\touiteur\render\TouiteRenderer;
use iutnc\touiteur\touite\ListTouite;
use iutnc\touiteur\user\UserAuthentifie;

class UserListeTouitesAction extends Action
{
    private UserAuthentifie $user;

    public function __construct()
    {
        parent::__construct();
        $user = unserialize($_SESSION['User']);
        $this->user = $user;
    }
    /** affiche les touites le l'utilisateur*/
    public function execute(): string
    {
        $html = '';
        if (isset($this->user)){
            $touites = $this->user->getTouites();
            $html = (new ListTouite($touites))->afficher();
        }
        return $html;
    }
}