<?php
namespace lib\Router;

use lib\Router\Router;

class Route extends Router{
    
    public function __construct($path){
        parent::__construct($path);
    }

    public function get(){

    }
    public function add($path){
        $this->route($path);
    }
}