<?php

    /**
     * déclarations des namespaces
     */
    namespace iutnc\touiteur\user;

    class UserAuthentifie extends User{

        /**
         * déclarations des attributs
         */
        protected string $login, $password, $date;

        /**
         * Constructeur
         */
        public function __construct(string $login, string $password){
            $this->login = $login;
            $this->password = $password;
        }

    }