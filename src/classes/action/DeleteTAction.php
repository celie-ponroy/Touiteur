<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\user\UserAuthentifie;

class DeleteTAction extends Action
{
    /** 
     * Supprime un Touite
    */
    public function execute(): string
    {
        $TId = $_GET['id'];
        $Touite = new Touite($TId);
        $Touite->deleteT();

        var_dump($_POST['redirect_to']);
        if (isset($_POST['redirect_to'])) {
            header('Location: ' . $_POST['redirect_to']);
            exit;
        }
        return '';
    }
}