<?php
namespace lib\Routing;

use lib\Cleverload;
use lib\Http\Redirect;

class Router{
    
    public $uri;
    public $current;

    private $routes;
    private $group;
    private $groupvalues = [];

    public function __construct($uri){
        $this->routes = new RouterCollection();
        $this->getRoutesFromConfig();
        $this->current = new Route(["HEAD"],$uri,null);
        $this->current->setDomain(Cleverload::$domain);

        $this->findRoute($this->current);
        $this->current->load();
    }

    public function get($uri, $action){
        return $this->add(["GET"],$uri,$action);
    }
    public function group(array $arguments,callable $action){
        $this->addGroup($arguments,$action);
        $reflection = new \ReflectionFunction($action);
        foreach($reflection->getParameters() as $parameter){
            if(!in_array($parameter->getName(), array_keys($this->groupvalues))){
                return false;
            }
        }
        call_user_func($action,...array_values($this->groupvalues));
    }
    public function add($method,$uri,$action){
        $this->routes->add(new Route($method,$uri,$action));
    }
    public function addGroup($arguments,$action){
        $this->resetGroup();
        $this->group["action"] = $action;
        $this->group["arguments"] = $arguments;
        $this->getGroupValues();
    }
    public function resetGroup(){
        $this->group = null;
        $this->groupvalues = [];
    }
    public function getGroupType(){
        return array_keys($this->group["arguments"])[0];
    }
    public function getGroupValues(){
        $grouptype = $this->getGroupType();
        switch($grouptype){
            case "domain":
                $sections_current = Route::explodeIntoSections(".", $this->current->getDomain(),"domain");
                $sections_group = Route::explodeIntoSections(".",$this->group["arguments"][$grouptype],"domain");
                if(count($sections_group) === count($sections_current)){
                    for($i = 0; $i < count($sections_group); $i++){
                        if($sections_group[$i]->is("value")){
                            $this->groupvalues[$sections_group[$i]->clean()] = $sections_current[$i]->get();
                        }
                    }
                }
            break;
            case "namespace":

            break;
            case "prefix":

            break;
        }
    }
    public function findRoute($current){
        $routes = $this->routes->getRoutes();
        $extravariables = [];
        $active = [];
        for($i = 0; $i < count($current->getSections()); $i++){
            if(count($routes) >  1){
                foreach($routes as $route){
                    if($route->getSectionCount() > $i && $current->getSectionCount() >= $route->getSectionCount()){
                        $now = $route->getSection($i);
                        if($now->is("value") || $now->get() == $current->getSection($i)->get()){
                            $active[] = $route;
                        }
                    }
                }
                $routes = $active;
                $active = [];
            }else if(count($routes) < 1){
                $routes = $this->getDefaultRoute();
                break;
            }else{
                if($i > $route->getSectionCount()){
                    $extravariables[] = $current->getSection($i)->get();
                }
            }
        }
        if(count($current->getSections()) < 1){
            foreach($routes as $route){
                if($route->getSectionCount() > $i && $current->getSectionCount() >= $route->getSectionCount()){
                    $now = $route->getSection($i);
                    if($now->is("value") || $now->get() == $current->getSection($i)->get()){
                        $active[] = $route;
                    }
                }
            }
            $routes = $active;
            $active = [];
        }
        if(count($routes) < 1){
            return $this->getDefaultRoute();
        }
        $this->setExtraVariables($extravariables);
        $this->current->setAction($routes[0]->getAction());
        return $this->current;
    }
    public function getDefaultRoute(){
        if(Cleverload::getConfig("default_file") !== ""){
            if(file_exists(Cleverload::getConfig("default_file"))){
                $this->file = Cleverload::getConfig("default_file");
            }
        }
        $accepted = ["php","html","htm","tpl","htpl"];
        $files = scandir(Cleverload::$base);
        if(count($files) > 0){
            foreach($files as $file){
                $pathinfo = pathinfo($file);
                if(array_key_exists("extension", $pathinfo)){
                    if(in_array($pathinfo["extension"], $accepted)){
                        if($pathinfo["filename"] == "index"){
                            $filepos = Cleverload::$base."/".$file;
                            if($filepos != str_replace("\\","/",Cleverload::$called)){
                                return $this->current->setAction($file);
                            }
                        }
                    }
                }
            }
        }
        return $this->Redirect()->error("404");
    }
    public function setExtraVariables($variables){
        $previous = 0;
        for($i = 0; $i < count($variables); $i += 2){
            $next = $i + 1;
            if(array_key_exists($next, $variables)){
                $_GET[$variables[$i]] = $variables[$next];
            }
        }
        return $_GET;
    }
    private function getRoutesFromConfig(){
        $routes = Cleverload::getPages();
        foreach($routes as $uri => $file){
            $this->add(["GET"],$uri,$file);
        }
    }
    private function getPageFromConfig($item,$key = false){
        foreach(Cleverload::getPages() as $keys => $value){
            if($keys == $item){
                if($key){
                    return $keys;
                }
                return $value;
            }
        }
    }
    public function Redirect($url = null){
        $redirect = new Redirect();
        if($url != null){
            return $redirect->to($url);
        }
        return $redirect;
    }
    public function getRoutes(){
        return $this->routes;
    }
    public function getGroupes(){
        return $this->groupes;
    }
}