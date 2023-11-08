<?php

namespace iutnc\touiteur\action;

class TouiteDeTagAction extends Action
{

    private $tag;

    public function __construct($tag)
    {
        $this->tag = $tag;
    }

    public function execute(): string
    {

    }
}