<?php
namespace lib\Router;

use lib\Cleverload;
use lib\Template\Template;

class Router extends Route{
    
    public $path;
    public $base;
    public $file;

    private $routes;

    public function __construct($path){
        $this->path = $path;
        $this->base = Cleverload::$base;
        $this->routes = $this->checkPages(Cleverload::getPages());
    }
    public function load(){
        return new Template($this->getFile());
    }
    public function getFile(){
        $routes = $this->decode($this->path);
        $matches = array();
        $fullmatches = array();
        if(is_array($routes)){
            for($i = 0; $i < count($routes); $i++){
                $sec = $routes[$i];
                if(count($fullmatches) > 0){
                    $matches = $this->match($fullmatches,$sec,$i);
                }else{
                    $matches = $this->match($this->routes,$sec,$i);
                }
                $fullmatches = $matches;
                $matches = array();
            }
        }
        if(empty($fullmatches)){
            return $this->getIndex();
        }
        return $fullmatches[0];
    }
    public function getIndex(){
        $scan = scandir(Cleverload::$root);
        var_dump($scan);
    }
    public function checkFile($filepath){
        return file_exists($filepath);
    }
    public function checkPages($list){
        foreach(array_keys($list) as $page){
            $path = $list[$page];
            $this->add($page,$path);
        }
        return $this->pages;
    }
    public function add($route,$file){
        return $this->pages[] = new Route($route,$file);
    }
    public function to(string $to){
        return $this->to($to);
    }
    private function match($array, $sec,$i){
        $matches = [];
        foreach($array as $route){
            $presec = $this->getSection($route->getPath(),$i);
            if($presec == $sec){
                array_push($matches,$route);
            }else if($this->is($sec,"value")){
                array_push($matches,$route);
            }
        }
        return $matches;
    }
    private function getPageFromConfig($item,$key = false){
        foreach($this->pages as $keys => $value){
            if($keys == $item){
                if($key){
                    return $keys;
                }
                return $value;
            }
        }
    }
}
?>