<?php
//
//namespace iutnc\touiteur\action;
//
//use iutnc\touiteur\touite\ListTouite;
//use iutnc\touiteur\touite\Tag;
//
//class TouiteDeTagAction extends Action
//{
//
//    private int $tag;
//
//    public function __construct($tag)
//    {
//        parent::__construct();
//        $this->tag = $tag;
//    }
//
//    public function execute(): string
//    {
//        $html = "";
//        $tag = new Tag(null, $this->tag);
//        $tList = $tag->findTaggedTw();
//        var_dump($tList);
//        $html = (new ListTouite($tList))->afficher();
//        var_dump($html);
//        return $html;
//    }
//}