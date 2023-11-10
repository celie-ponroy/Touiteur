<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;

/**
 * Class DeconnAction
 */
class DeconnAction extends Action
{
    /**
     * Constructeur
     */
    public function __construct()
    {
        parent::__construct();
    }

  /**
   * Méthode execute permet de se s'inscrire
   * @return string code html
   */

    public function execute(): string
    {
        unset($_SESSION['User']);
        header('Location: index.php?action=connection');
        return "disconnected";
    }
}