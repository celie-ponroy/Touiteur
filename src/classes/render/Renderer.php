<?php

namespace iutnc\touiteur\render;

/**
 * Interface Renderer
 */
interface Renderer {
    const COMPACT = 1;
    const LONG = 2;

    /**
     * Méthode render qui affiche le code html
     * @param $selector
     * @return string
     */
    public function render($selector):string;
}