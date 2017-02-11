<?php

namespace lib\Template;

use lib\Cleverload;

class TemplateLoader{

    public $dom;


    public function __construct($dom){
        $this->dom = $dom;
        $this->load();
    }

    public function executePlugin(){

    }
    public function executeTags(){

    }
    public function setBase(){
        //sets base of file
    }

    public function load(){
        return printf($this->dom->saveHTML());
    }

}