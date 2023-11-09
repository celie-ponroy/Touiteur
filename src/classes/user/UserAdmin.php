<?php

namespace iutnc\touiteur\user;

class UserAdmin extends UserAuthentifie
{

    public function __construct(string $email)
    {
        parent::__construct($email);
    }




}