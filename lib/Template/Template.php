<?php
namespace lib\Template;

class Template{
    
    public $filepath;

    public $dom;

    public function __construct($filepath){
        $this->filepath = $filepath;
        $this->load();
    }

    private function load(){
        new TemplateLoader($this->dom);
    }
}