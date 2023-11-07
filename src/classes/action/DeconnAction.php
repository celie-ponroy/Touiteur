<?php

namespace iutnc\touiteur\action;

class DeconnAction extends Action
{
    public function execute(): string
    {
        unset($_SESSION);
        return "disconnected";
    }
}