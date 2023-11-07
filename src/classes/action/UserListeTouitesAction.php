<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\user\UserAuthentifie;

class UserListeTouitesAction extends Action
{
    private UserAuthentifie $user;

    public function __construct($user=null)
    {
        parent::__construct();
        $this->user = $user;
    }

//    public function execute(): string
//    {
//        $html = '';
//        if (isset($this->user)){
//            $touites = $this->user->getTouites();
//            foreach ($touites as $t){
//                $t
//                    $html .= "$t['message'] " . "$t";
//            }
//        }
//        return $html;
//    }
}