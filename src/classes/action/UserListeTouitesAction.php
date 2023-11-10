<?php

namespace iutnc\touiteur\action;
use iutnc\touiteur\render\Renderer;
use iutnc\touiteur\render\TouiteRenderer;
use iutnc\touiteur\touite\ListTouite;
use iutnc\touiteur\user\UserAuthentifie;

/**
 * Class UserListeTouitesAction
 */
class UserListeTouitesAction extends Action
{
    private UserAuthentifie $user;

    /**
     * Constructeur
     */
    public function __construct()
    {
        parent::__construct();
        $user = unserialize($_SESSION['User']);
        $this->user = $user;
    }

    /**
     * Méthode execute qui affiche les touites le l'utilisateur
     * @return string code html
     */
    public function execute(): string
    {
        $html = '';
        //si l'utilisateur est connecté
        if (isset($this->user)){
            //on récupère la liste des touites
            $touites = $this->user->getTouites();
            $html = (new ListTouite($touites))->afficher();
        }
        return $html;
    }
}