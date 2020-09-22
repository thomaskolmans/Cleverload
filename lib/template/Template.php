<?php
namespace lib\template;

use lib\Routing\Router;
use lib\Routing\Route;
use lib\Cleverload;
use lib\exception\UnexpectedPlugin;

class Template extends Router{
    
    public $route = null;
    public $filepath = "";
    public $dom = null;

    public static $php = [];

    public function __construct($input){
        libxml_use_internal_errors(true);
        if($input instanceof Route){
            $this->route = $input;
            $this->filepath = Cleverload::getInstance()->getViewDir()."/".$input->getFile();
            $file = $this->getFile();

            if ($file != null){
                $this->getDomFromFile($file);
            }

        } else {
            $this->getDom($input);
        }

    }

    public function getTemplateTags(){
        return Cleverload::getConfig("template_tags");
    }

    public function getFile(){
        if(file_exists($this->filepath)){
            return $this->filepath;
        }
        return null;
    }

    public function getFileInfo(){
        return pathinfo($this->getFile());
    }

    public function getDomFromFile($file){
        $this->dom = new \DOMDocument();
        $content = file_get_contents($file);
        $this->dom->loadHTML($content);
        $this->executeTags();
        $this->executePlugins();
        $this->dom->loadHTML($this->extractPHP($this->getContent()));
        return $this->dom;
    }

    public function getDom($content){
        $this->dom = new \DOMDocument();
        $this->dom->loadHTML($content);
        $this->executeTags();
        $this->executePlugins();
        $this->dom->loadHTML($this->extractPHP($this->getContent()));
        return $this->dom;
    }

    public function getDomSinExtract($content){
        $this->dom = new \DOMDocument();
        $this->dom->loadHTML($content);
        return $this->dom;
    }

    public function getContent(){
        return $this->dom->saveHTML();
    }

    public function saveContent($content){
        $this->getDomSinExtract($content);
        return $this;
    }

    public function executePlugins(){
        $content = $this->getContent();
        $this->getPlugins($content);
    }

    public function executeTags(){
        $this->getTags();
    }

    public function getTags(){
        $tags = scandir(__DIR__."/tags");
        unset($tags[0]);unset($tags[1]);
        foreach($tags as $tag){
            $file = pathinfo($tag);
            $class = "lib\\template\\tags\\".$file["filename"];
            new $class($this->dom);
        }
    }

    private function getPlugins($content){
        preg_match_all("/(?<=@{)(.*)(?=})/", $content, $plugins);
        foreach($plugins[0] as $plugin){
            $parts = explode(" ",$plugin);
            $compile = $parts[0];
            $class = "lib\\template\\plugins\\TPlugin_".$compile;
            if(class_exists($class)) {
                new $class($content,$plugin);
            } else {
                throw new UnexpectedPlugin($compile." is not valid");
            }
        }
    }
    private function extractPHP($content){
        $matches = self::getInBetween($content,"<?php", "?>");
        foreach($matches as $match){
            $uid = uniqid();
            $content = str_replace("<?php$match?>", "<?php ".$uid." ?>" ,$content);
            self::$php[] = array($uid, $match);
        }
        return $content;
    }

    public static function insertPHP($content){        
        $matches = self::getInBetween($content,"<?php", "?>");
        for($i = 0; $i < count($matches); $i++){
            $match = trim($matches[$i]);
            if($i <= count(self::$php) - 1){
                foreach(self::$php as $phpPart) {
                    if($phpPart[0] === $match){
                        $content = str_replace(trim($match), $phpPart[1], $content);
                        break;
                    }
                }
                continue;
            }
        }
        return $content;
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
        if ($this->dom != null && !empty($this->dom)) {
            $templateLoader = new TemplateLoader($this);
            echo "<code>".htmlspecialchars(htmlspecialchars_decode($this->dom->saveHTML($this->dom)))."</code></br>"; 
            return $templateLoader->execute();
        }
    }
}