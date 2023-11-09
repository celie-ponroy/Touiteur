<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;

class DeconnAction extends Action
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execute(): string
    {
        unset($_SESSION['User']);
        return "disconnected";
    }
}