<?php
declare(strict_types= 1);

namespace iutnc\touiteur\touite;

class Tag{
    private $id;
    private $libelle;
    private $desciption;
    
    function __construct(string $id, string $libelle, string $desciption){
        $this->id = $id;
        $this->libelle = $libelle;
        $this->desciption = $desciption;
    }
    
}