<?php

namespace lib\template;

class TemplateLoader{

    public $dom;
    public $template;

    public function __construct($template){
        $this->template = $template;
        $this->dom = $this->template->dom;
    }

    public function execute(){
        return $this->load();
    }

    private function getDomContent(){
        $html = $this->dom->saveHTML($this->dom);
        return htmlspecialchars_decode($this->template->insertPHP($html)); 
    }

    private function executeFile($content){
        $tmp = tempnam(sys_get_temp_dir(), "contentfile");
        file_put_contents($tmp, $content);
        ob_start();
        
        require $tmp;
        $output = ob_get_clean(); 

        unlink($tmp);

        return $output;
    }

    private function load(){
        return $this->executeFile($this->getDomContent());
    }

}