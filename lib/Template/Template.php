<?php
namespace lib\Template;

use lib\Routing\Router;
use lib\Routing\Route;
use lib\Cleverload;

class Template extends Router{
    
    public $route = null;
    public $filepath = "";

    public $dom;

    public static $php = [];

    public function __construct($input){
        if($input instanceof Route){
            $this->route = $input;
            $this->filepath = $input->getFile();
            $this->dom = $this->getDomFromFile($this->getFile());
            $this->load();
        }else{
            $this->dom = $this->getDom($input);
            $this->load();
        }

    }
    public function getTemplateTags(){
        return Cleverload::getConfig("template_tags");
    }
    public function getFile(){
        if(file_exists($this->filepath)){
            return $this->filepath;
        }
        return $this->redirect()->error("404");
    }
    public function getDomFromFile($file){
        $dom = new \DOMDocument();
        $dom->loadHTMLFile($file);
        return $dom;
    }
    public function getDom($content){
        $dom = new \DOMDocument();
        $dom->loadHTML($content);
        return $dom;
    }
    public function extractPHP($content){
        $matches = self::getInBetween($content,"<?php","?>");
        foreach($matches as $match){
            $uid = uniqid();
            self::$php[] = array($uid,$match);
        }
        return self::$php;
    }
    public static function insertPHP($content){
        $matches = self::getInBetween($content,"<?php"," ?>");

        for($i = 0; $i < count($matches); $i++){
            $match = $matches[$i];
            if($i <= count(self::$php) - 1){
                if(array_keys(self::$php)[$i] === trim($match)){

                }
                continue;
            }
        }
        /*
        $rotation = 0;
        foreach($matches as $match){
            if($rotation <= sizeof(parent::$dom["php"]) - 1){
                if(array_keys(parent::$dom["php"])[$rotation] == trim($match)){
                    $string = " ".parent::$dom["php"][trim($match)]." ";
                    $content = str_replace(trim($match),$string, $content);
                    $rotation = $rotation + 1;
                }else{
                    continue;
                }
            }
        }
        return $content;
        */
    }

    public static function getInBetween($string, $start, $end){
        $contents = array();
        $startLength = strlen($start);
        $endLength = strlen($end);
        $startFrom = $contentStart = $contentEnd = 0;
        while (false !== ($contentStart = strpos($string, $start, $startFrom))) {
            $contentStart += $startLength;
            $contentEnd = strpos($string, $end, $contentStart);
            if (false === $contentEnd) {
                break;
            }
            $contents[] = substr($string, $contentStart, $contentEnd - $contentStart);
            $startFrom = $contentEnd + $endLength;
        }

        return $contents;
    }

    public function load(){
        new TemplateLoader($this->dom);
    }
}