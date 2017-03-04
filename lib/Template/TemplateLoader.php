<?php

namespace lib\Template;

use lib\Cleverload;

class TemplateLoader{

    public $dom;
    public $template;

    private $tmp;


    public function __construct($template){
        $this->template = $template;
        $this->dom = $this->template->dom;
        $this->execute();
        $this->addBase();
        $this->load();
    }

    public function execute(){
        $this->executePlugins();
        $this->executeTags();
    }
    public function executePlugins(){
        if(in_array($this->template->getFileInfo()["extension"], $this->template->getAllowdExtensionsForPlugins())){
            $content = $this->template->getContent();
            $this->getPlugins($content);
        }
    }
    public function executeTags(){
        if(in_array($this->template->getFileInfo()["extension"], $this->template->getAllowdExtensionsForTags())){
            $this->getTags($this->dom);
        }
    }
    public function getTags($dom){
        $tags = scandir(__DIR__."/tags");
        unset($tags[0]);unset($tags[1]);
        foreach($tags as $tag){
            $file = pathinfo($tag);
            $class = "lib\Template\\tags\\".$file["filename"];
            new $class($this->dom);
        }
    }
    public function getPlugins($dom){
        
    }
    public function addBase(){
        $content = $this->template->getContent();
        if($this->dom->getElementsByTagName("head")->length == 0){
            $content = $this->setBase().$content;
        }else{
            $content = str_replace("<head>", "<head>".$this->setBase(), $content);
        }
        $this->template->saveContent($content);
        $this->dom = $this->template->dom;
    }
    public function setBase(){
        return "<base href='".$this->getBase()."/'>";
    }
    private function getBase(){
        return Cleverload::$base;
    }
    public function getDomContent(){
        $html = $this->dom->saveHTML();
        $html = htmlspecialchars_decode($html);
        return  $this->template->insertPHP($html);
    }
    public function executeFile($content){
        $tmp = tempnam(sys_get_temp_dir(), "contentfile");
        file_put_contents($tmp, $content);
        ob_start();
        require $tmp;
        $output = ob_get_clean(); 
        return $output;
    }
    public function load(){
        return print($this->executeFile($this->getDomContent()));
    }

}