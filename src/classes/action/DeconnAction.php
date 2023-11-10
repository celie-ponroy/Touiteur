<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;

class DeconnAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }
      /**Permet de se s'inscrire */

    public function execute(): string
    {
        unset($_SESSION['User']);
        header('Location: index.php?action=connection');
        return "disconnected";
    }
}