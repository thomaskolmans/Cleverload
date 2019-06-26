<?php

namespace lib\template;

use lib\Cleverload;
use lib\exception\UnexpectedPlugin;

class TemplateLoader{

    public $dom;
    public $template;

    private $tmp;

    public function __construct($template){
        $this->template = $template;
        $this->dom = $this->template->dom;
    }
    public function execute(){
        $this->executePlugins();
        $this->executeTags();
        return $this->load();
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
    public function getPlugins($content){
        preg_match_all("/(?<=@{)(.*)(?=})/", $content, $plugins);
        foreach($plugins[0] as $plugin){
            $parts = explode(" ",$plugin);
            $compile = $parts[0];
            $class = "lib\Template\\plugins\\TPlugin_".$compile;
            if(class_exists($class)){
                new $class($content,$plugin);
            }else{
                throw new UnexpectedPlugin($compile." is not valid");
            }

        }
    }
    public function getDomContent(){
        $html = $this->dom->saveHTML();
        return htmlspecialchars_decode($this->template->insertPHP($html)); 
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
        return $this->executeFile($this->getDomContent());
    }

}