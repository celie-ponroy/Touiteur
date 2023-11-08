<?php

namespace iutnc\touiteur\action;
use iutnc\touiteur\render\Renderer;
use iutnc\touiteur\render\TouiteRenderer;
use iutnc\touiteur\user\UserAuthentifie;

class UserListeTouitesAction extends Action
{
    private UserAuthentifie $user;

    // заменить для любого юзера
    public function __construct()
    {
        parent::__construct();
        $user = unserialize($_SESSION['User']);
        $this->user = $user;
    }

    public function execute(): string
    {
        $html = '';
        if (isset($this->user)){
            $touites = $this->user->getTouites();
            foreach ($touites as $t){
                    //affichage (id)
                    $tmp= new TouiteRenderer($t);
                    $html.=$tmp->render(Renderer::COMPACT);
            }
        }
        return $html;
    }
}