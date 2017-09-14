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
    private $defaults = [];

    public function __construct($uri){
        $this->routes = new RouterCollection();
        $this->getRoutesFromConfig();
        $this->current = new Route(["HEAD"],$uri,null);
        $this->current->setDomain(Cleverload::$domain);
    }

    public function compile(){
        $this->getRouterFiles();
        $this->getRoute($this->current);
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
        return $this->add(["GET","HEAD","PUT","DELETE","OPTIONS","PATCH","POST"],$uri,$action);
    }
    public function group(array $arguments,callable $action){
        $this->addGroup($arguments,$action);
        $reflection = new \ReflectionFunction($action);
        foreach($reflection->getParameters() as $parameter){
            if(!in_array($parameter->getName(), array_keys($this->groupvalues))){
                return false;
            }
        }
        if(array_key_exists('match', $this->group)){
            if(!$this->group['match'])
                return false;
        }else{
            return false;
        }
        call_user_func($action,...array_values($this->groupvalues));
        return $this;
    }
    public function add($method,$uri,$action){
        return $this->routes->add(new Route($method,$uri,$action));
    }
    public function addGroup($arguments,$action){
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
                        if($sections_group[$i]->is("value")){
                            return $this->groupvalues[$sections_group[$i]->clean()] = $sections_current[$i]->get();
                        }else{
                            if($this->current->getDomain() === $this->group["arguments"][$grouptype]){
                                return $this->group['match'] = true;
                            }
                            return $this->group['match'] = false;
                        }
            break;
            case "namespace":
                $namespace = new Route(["GET","HEAD","PUT","DELETE","OPTIONS","PATCH","POST"],$this->group["arguments"][$this->getGroupType()],null);
                $match = false;
                if(!(count($this->current->getSections()) > 0)){
                    return $this->group['match'] = false;
                }
                for($i = 0; $i < count($namespace->getSections()); $i++){
                    $namespace_section = $namespace->getSection($i);
                    $current = $this->current->getSection($i);
                    if($namespace_section->get() !== $current->get()){
                        return $this->group['match'] = false;
                        break;
                    }
                }
                return $this->group['match'] = true;
            break;
        }
    }
    public function getArguments($current,$route){
        $arguments = [];
        for($i = 0; $i < count($route->getSections()); $i++){
            if(count($current->getSections()) > 0){
                $csec = $current->getSection($i);
                $rsec = $route->getSection($i);
                if($rsec->is("value"))
                    $arguments[$rsec->clean()] = $csec->get();
            }
        }
        return $arguments;
    }
    public function getRoute($current){
        $routes = $this->routes->getRoutes();
        $extravariables = [];
        $active = [];
        $notfound  = true;
        for($i = 0; $i < count($current->getSections()); $i++){
            if($notfound){
                foreach($routes as $route){
                    $now = $route->getSection($i);
                    if($route->getSectionCount() > $i && 
                        $current->getSectionCount() >= $route->getSectionCount() &&
                            in_array($_SERVER['REQUEST_METHOD'], $route->getMethods())){
                        if($now->is("value") || $now->get() == $current->getSection($i)->get()){
                            if(count($route->getWhere()) > 0 && $now->is("value")){
                                if(!preg_match("/^".$route->getWhere()[$now->clean()]."+$/",$current->getSection($i)->get())){
                                    continue;
                                }
                            }
                            if(!$route->getIf()){
                                continue;
                            }
                            $active[] = $route; 
                        }
                    }
                }
                $routes = $active;
                $active = [];
                if(count($routes) == 1)
                    if(count($routes[0]->getSections()) - 1 <= $i)
                        $notfound = false;
                if(count($current->getSections()) < 1 && $notfound){
                    $this->getDefaultRoute();
                }
            }else if(count($routes) < 1){
                if(count($current->getSections()) < 1){
                    return $this->getDefaultRoute();
                }else{
                    return $this->Redirect()->error("404");
                }
                break;
            }else{
                if($i >= $routes[0]->getSectionCount()){
                    $extravariables[] = $current->getSection($i)->get();
                }
            }
        }
        if(count($routes) < 1 || $notfound){
            if(count($current->getSections()) < 1){
                return $this->getDefaultRoute();
            }else{
                $count_sections = count($current->getSections());
                if($count_sections % 2 == 0){
                    for($i = 0; $i < $count_sections; $i++){
                        $extravariables[] = $current->getSection($i)->get();
                    }
                    $this->setExtraVariables($extravariables);
                    return $this->getDefaultRoute();
                }else{
                    $namespace = new Route(["GET","HEAD","PUT","DELETE","OPTIONS","PATCH","POST"],$this->group["arguments"][$this->getGroupType()],null);
                    $match = false;
                    for($i = 0; $i < count($namespace->getSections()); $i++){
                        $namespace_section = $namespace->getSection($i);
                        $current = $this->current->getSection($i);
                        if($namespace_section->get() !== $current->get()){
                            return $this->Redirect()->error("404");
                            break;
                        }
                    }
                    if(count($namespace->getSections()) === 0){
                        return $this->Redirect()->error("404");
                    }
                    return $this->getDefaultRoute();
                }
            }
        }
        $this->setExtraVariables($extravariables);
        $this->current->setAction($routes[0]->getAction());
        $match = array($this->current,$routes[0]);
        $this->current = $match;

        return $this->current;
    }
    public function getDefaultRoute(){
        $default = $this->getDefault();
        if($default !== null){
            $this->current->setAction($default->getAction());
            $match = array($this->current,$default);
            $this->current = $match;
            return $this->current;
        }else if(Cleverload::getConfig("default_file") !== ""){
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
        foreach($files as $file){
            if(is_file(Cleverload::$root."/routes/".$file)){
                require(Cleverload::$root."/routes/".$file);
            }
        }
        return $this;
    }
    private function getRoutesFromConfig(){
        $routes = Cleverload::getPages();
        foreach($routes as $uri => $file){
            if(is_array($uri)){
                foreach($uri as $url){
                    $this->get($uri,$file);
                }
                return $this;
            }
            $this->get($uri,$file);
            return $this;
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
    public function setDefault($default){
        $this->defaults[] = $default;
        return $this;
    }
    public function getDefault(){
        foreach($this->defaults as $default){
            if($default->getIf()){
                return $default;
            }
        }
        return null;
    }
}