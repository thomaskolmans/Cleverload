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
    }

    public function compile(){
        $this->getRouterFiles();
        $this->findRoute($this->current);
        if(is_array($this->current)){
            $this->current[0]->addParameters($this->getArguments($this->current[0],$this->current[1]));
            $this->current[0]->load();
        }else{
            $this->current->load();
        }
    }

    public function get($uri, $action){
        return $this->add(["GET","HEAD"],$uri,$action);
    }
    public function post($uri,$action){
        return $this->add(["POST"],$uri,$action);
    }
    public function delete($uri,$action){
        return $this->add(["DELETE"],$uri,$action);
    }
    public function put($uri,$action){
        return $this->add(["PUT"],$uri,$action);
    }
    public function patch($uri,$action){
        return $this->add(["PATCH"],$uri,$action);
    }
    public function any($uri,$action){
        return $this->add(["GET","HEAD","PUT","DELETE","OPTIONS","PATCH"]);
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
        return $this;
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
                if(count($sections_group) === count($sections_current))
                    for($i = 0; $i < count($sections_group); $i++)
                        if($sections_group[$i]->is("value"))
                            $this->groupvalues[$sections_group[$i]->clean()] = $sections_current[$i]->get();
            break;
            case "prefix":
                $prefix = $this->group["arguments"][$grouptype];
            break;
        }
    }
    public function getArguments($current,$route){
        $arguments = [];
        for($i = 0; $i < count($route->getSections()); $i++){
            $csec = $current->getSection($i);
            $rsec = $route->getSection($i);
            if($rsec->is("value")){
                $arguments[$rsec->clean()] = $csec->get();
            }
        }
        return $arguments;
    }
    public function findRoute($current){
        $routes = $this->routes->getRoutes();
        $extravariables = [];
        $active = [];
        $notfound  = true;
        for($i = 0; $i < count($current->getSections()); $i++){
            if($notfound){
                foreach($routes as $route){
                    if($route->getSectionCount() > $i && $current->getSectionCount() >= $route->getSectionCount()){
                        if(in_array($_SERVER['REQUEST_METHOD'], $route->getMethods())){
                        $now = $route->getSection($i);
                        if($now->is("value") || $now->get() == $current->getSection($i)->get())
                            $active[] = $route; 
                        }
                    }
                }
                $routes = $active;
                $active = [];
                if(count($routes) == 1)
                    $notfound = false;
            }else if(count($routes) < 1){
                if(count($current->getSections()) < 1){
                    return $this->getDefaultRoute();
                }else{
                    return $this->Redirect()->error("404");
                }
                break;
            }else{
                if($i > $current->getSectionCount()){
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
            if(count($routes) < 1){
                return $this->getDefaultRoute();
            }
        }else{
            if(count($routes) < 1){
                return $this->Redirect()->error("404");
            }
        }

        $this->setExtraVariables($extravariables);
        $this->current->setAction($routes[0]->getAction());
        $match = array($this->current,$routes[0]);
        $this->current = $match;

        return $this->current;
    }
    public function getDefaultRoute(){
        if(Cleverload::getConfig("default_file") !== ""){
            if(file_exists(Cleverload::getConfig("default_file"))){
                $file =  Cleverload::getConfig("default_file");
                return $this->current->setAction($file);
            }
        }
        $accepted = ["php","html","htm","tpl","htpl"];
        $files = scandir(Cleverload::$filebase);
        if(count($files) > 0){
            foreach($files as $file){
                $pathinfo = pathinfo($file);
                if(array_key_exists("extension", $pathinfo) 
                    && in_array($pathinfo["extension"], $accepted) 
                    && $pathinfo["filename"] == "index"){
                        $filepos = Cleverload::$filebase."/".$file;
                        if($filepos != str_replace("\\","/",Cleverload::$called)){
                            return $this->current->setAction($file);
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
    private function getRouterFiles(){
        $files = scandir(Cleverload::$root."/routes");
        $files = array_slice($files, -1,1);
        foreach($files as $file){
            require_once(Cleverload::$root."/routes/".$file);
        }
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

    public function call($func,$args){
        return $this->{$func}(...array_values($args));
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