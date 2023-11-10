<?php
declare(strict_types=1);
namespace iutnc\touiteur\action;

/**
 * Class Action
 */
abstract class Action {

    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;

    /**
     * Constructeur
     */
    public function __construct(){
        
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];
    }

    /**
     * Méthode execute
     * @return string code html
     */
    abstract public function execute() : string;
    
}