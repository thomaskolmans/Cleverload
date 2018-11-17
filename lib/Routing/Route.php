<?php
namespace lib\Routing;

use lib\Template\Template;
use lib\Cleverload;
use Exception;

class Route{

    private $router;

    public $uri;
    public $domain;

    public $groupstack;

    private $file = null;
    private $action = null;

    protected $methods = [];
    protected $parameters = [];

    protected $wheres = [];
    protected $when = true;

    protected $sections;
    protected $sectioncount;

    public function __construct($methods,$uri,$action){
        if(Cleverload::getInstance()->getCaseSensitive()){
           $this->uri = $uri;
       }else{
            $this->uri = strtolower($uri);
       }

        $this->setAction($action);
        $this->methods = $methods;
    }
    public function run(){
        $this->sections = $this->setSections();
        $this->sectioncount = $this->setSectionCount();
        $this->setParameters();
    }
    public function getResponse(){
        if(is_file(Cleverload::getInstance()->getStaticFilesDir().$this->uri)){
            $extension = pathinfo($this->uri)["extension"];
            $ctype = "text/html";
            switch($extension){
                case "zip": $ctype = "application/zip"; break;
                case "jpeg":
                case "jpg": $ctype = "image/jpg"; break;
                case "png": $ctype = "image/png"; break;
                case "css": $ctype = "text/css"; break;
                case "js": $ctype = "text/javascript"; break;
                case "svg": $ctype = "image/svg+xml"; break;
            }
            header("Content-type: ".$ctype);
            $this->getRouter()->response->sendFile(Cleverload::getInstance()->getStaticFilesDir().$this->uri);
        } else {
            $matchedroute = $this->getMatch($this->getRouter());
            if ($matchedroute == null) {
                $matchedroute = $this->getRouter()->response->notFound();
            }
            $matchedroute->load();
            $this->getRouter()->routes->clear();
            $this->getRouter()->response->send();      
        }
    }
    public function where($variable,$regex){
        $this->wheres[$variable] = $regex;
        return $this;
    }
    public function when($boolean){
        $this->when = $boolean;
        return $this;
    }
    public function primary(){
        if(count($this->getParameters()) < 1){
            $this->getRouter()->addDefault($this);
        }else{
            throw new \Exception("You can't set the default to a path with a variable");
        }
        return $this;
    }
    public function getMatch(Router $router){
        $routes = $router->getRoutes();
        if(count($routes) < 1){
            $this->getRouter()->response->noRoutes();
        }
        $found = false;
        $simelarities = false;
        for($i = 0; $i < $this->sectioncount; $i++){
            if(!$found && count($routes) > 0){
                $previous = $routes;
                $routes = $this->matchSectionToRoutes($i,$routes);
                if(count($routes) == 1){
                    if($this->hasRest($routes[0]->sectioncount)){
                        if($this->validRest($i + 1,true)){
                            $this->getRest($i+1,$routes[0],true);
                        }else{
                            $found = false;
                            continue;
                        }    
                    }    
                    $found = true;
                    break;
                }else if(count($routes) > 1 && $this->sectioncount == $i + 1){
                    foreach($routes as $route){
                        if($route->sectioncount === $this->sectioncount){
                            $routes = [$route];
                            $found = true;
                            break;
                        }
                    }
                }else if(count($routes) < 1){
                    $routes = $previous;
                }
                continue;
            }
        }
        if(!$found){
            if($this->sectioncount <= 1){
                $matches = [];
                foreach($routes as $route){
                    if($this->uri == $route->uri){
                        if($this->equalsRoute($this,$route)){
                            $matches[] = $route;
                        }else{
                            continue;
                        }
                    }
                }
                if(count($matches) == 1){
                    return $this->handleMatch($matches[0]);
                }
            }else if(count($routes) > 0){
                $default = $this->getRouter()->getDefault();
                if($default == null){
                    $this->getClosest($routes);
                }
                return $default;
            }else{
                return $this->getRouter()->response->notFound();
            }
            return $this->getRouter()->getDefault();
        }
        return $this->handleMatch($routes[0]);
    }
    public function getRest($i,$route,$ignore_zero = false){
        if($this->validRest($i,$ignore_zero)){
            for($j = $i + 1; $j < $this->sectioncount; $j += 2){
                $key = $this->getSection($j - 1)->toString();
                $value = $this->getSection($j)->toString();
                $route->bindVariable($key,$value);
                return true;
            } 
        }
        return false;
    }
    public function validRest($i,$ignore_zero = false){
        if($this->hasRest($i)){
            if(!$ignore_zero){
                if($i === 0){
                    $rest = $this->sectioncount;
                }else{
                    $rest = $this->sectioncount - ($i + 1);
                }   
            }else{
                $rest = $this->sectioncount - $i;
            }

            if($rest % 2 == 0 && $rest > 0){
                return true;
            }  
        }
        return false;
    }
    public function hasRest($i){
        if($this->sectioncount > ($i + 1)){
            return true;
        }
        return false;
    }
    public function getClosest($routes){
        $previous = $routes;
        if($routes == null || count($routes) < 1){
            return $this->getRouter()->response->notFound();
        }
        if($this->sectioncount > 0){
            $simelarities = false;
            for($i = 0; $i < $this->sectioncount; $i++){
                $routes = $this->matchSectionToRoutes($i,$routes);
                if(count($routes) > 0){
                    $simelarities = true;
                    $previous = $routes;
                }
                if(!$simelarities && $this->validRest($i)){
                    $results = [];
                    foreach($previous as $route){
                        if($this->equalsRoute($this,$route)){
                            if($route->sectioncount === $i || $route->sectioncount === ($i + 1)){
                                $results[] = $route;
                            }
                        }
                    }
                    if(count($results) > 0){
                        $this->getRest($i,$results[0]);
                        return $this->handleMatch($results[0]);
                    }
                }
                if(!$simelarities && count($routes) < 1){
                    $this->getRouter()->response->notFound();
                }
                if(count($routes) == 1){
                    if($this->hasRest($i)){
                        $this->getRest($i,$routes[0]);   
                        if($this->validRest($i)){
                            $this->getRest($i,$routes[0]);
                        }else{
                            continue;
                        }   
                    }
                    return $this->handleMatch($routes[0]);
                }
            }
            if ($previous != null && count($previous) >= 1){
                $routes = $previous;
            } else {
                return $this->getRouter()->response->notFound();
            }
            return $this->handleMatch($routes[0]);
        }else{
            $results = [];
            foreach($routes as $route){
                if($this->equalsRoute($this,$route)){
                    if($route->sectioncount == 0 || $route->sectioncount == 1){
                        $results[] = $route;
                    }
                }
            }
            if(count($results) > 0){
                return $this->handleMatch($results[0]);
            }else{
                return $this->getRouter()->response->notFound();
            }
        }

    }
    private function matchSectionToRoutes($i,$routes){
        $matches = [];
        foreach($routes as $route){
            if($this->equalsSection($i,$route)){
                if($this->equalsRoute($this,$route)){
                    $matches[] = $route;
                }else{
                    continue;
                }
            }
        }
        return $matches;
    }
    private function equalsRoute($route1,$route2){
        if(count($route1->getWhere()) > 0 && count($route1->getParameters()) > 0){
            for($i = 0; $i < count($route1->sectioncount); $i++){
                if(!preg_match("/^".$route2->getWhere()[$route1->getSection($i)->clean()]."+$/",$route1->getSection($i)->get())){
                    return false;
                }
            }
        }
        if(!$route2->getIf()) return false;
        if($route2->getDomain() != null){
            if($route2->getDomain() !== $route1->getDomain()) return false;
        }
        return true;
    }
    private function equalsSection($i,$route){
        if($route->hasMethod($this->getMethods()[0])){
            if($route->hasSection($i) && $this->hasSection($i)){
                if($this->getSection($i)->toString() === $route->getSection($i)->toString() || $route->getSection($i)->isValue() || $this->getSection($i)->isValue()){
                    return true;
                } 
            }
        }
        return false;
    }
    private function handleMatch($match){
        $this->matchParameters($match);
        return $match;
    }
    private function bindVariable($key,$value){
        $_GET[$key] = $value;
    }
    public function setGroupstack($groupstack){
        $this->groupstack = $groupstack;
        $this->defractorGroupstack();
        $this->run();
    }
    public function defractorGroupstack(){
        for($i = count($this->groupstack) - 1; $i >= 0; $i--){
            foreach($this->groupstack[$i] as $grouptype => $value){
                switch($grouptype){
                    case "namespace":
                        $this->uri = $value.$this->uri;
                    break;
                    case "prefix":
                        $this->uri = $value.$this->uri;
                    break;
                    case "domain":
                        $this->domain = $value;
                    break;
                }
            }
        }
    }
    public function countSectionVariables(){
        $count = 0;
        foreach($this->getSections() as $section){
            if($section->isValue()){
                $count++;
            }
        }
        return $count;
    }
    public function setSectionCount(){
        return count($this->getSections());
    }
    public function setSections(){
        return $this->explodeIntoSections("/",$this->uri,"uri");
    }
    public static function explodeIntoSections($divider,$string,$type){
        $arr = [];
        foreach(array_filter(array_values(explode($divider,$string))) as $section){ 
            if(!empty($section)){
                $arr[] = new Section($section);
            }
        }
        return $arr;
    }
    public function matchParameters($match){
        $sections = $match->getSections();
        for($i = 0; $i < count($sections); $i++){
            $section = $sections[$i];
            if($section->isValue()){
                unset($match->parameters[$i]);
                if(array_key_exists($i, $this->sections)){
                    $match->parameters[$section->clean()] = $this->sections[$i]->clean();
                    $_GET[$section->clean()] = $this->sections[$i]->clean();   
                }
            }
        }
    }
    public function addParameter($parameter){
        $this->parameters[] = $parameter;
        return $this;
    }
    public function addParameters($parameters){
        $this->parameters = [];
        foreach($parameters as $key => $value){
            $this->parameters[$key] = $value;
        }
        return $this;
    }
    public function setParameters(){
        for($i = 0; $i < count($this->sections); $i++){
            $section = $this->getSection($i);
            if($section->isValue()){
                $this->parameters[$i] = $section->clean();
            }    
        }
        return $this;
    }
    public function setParametersAsGet(){
        foreach($this->parameters as $key => $value){
            $_GET[$key] = $value;
        }
        return $_GET;
    }
    public function load(){
        if(is_callable($this->action)){
            return $this->callFunction($this->action,$this->getParameters());   
        }    
        return $this->loadFile();
    }

    public function callFunction($func = null,$values = []){
        return $func(...array_values($values));
    }
    public function loadFile(){
        if(Cleverload::getInstance()->template){
            return $this->getRouter()->response->setBody((new Template($this))->load());
        }
    }
    public function isValid(){
        if(preg_match("/[a-zA-Z\/}{]*/", $path)){
            return true;
        }
        return false;
    }
    public function getURI(){
        return $this->URI;
    }
    public function getSectionCount(){
        return $this->sectioncount;
    }
    public function setAction($action){
        if(is_callable($action)){
            $this->action = $action;
        }else{
            $this->file = $action;
        }
    }
    public function getAction(){
        return $this->action;
    }
    public function getFile(){
        return $this->file;
    }
    public function getDomain(){
        return $this->domain;
    }
    public function setDomain($domain){
        $this->domain = $domain;
        return $this;
    }
    public function getSection($number){
        if($this->hasSection($number)){
            return $this->getSections()[$number];
        }
        return null;
    }
    public function getSections(){
        return $this->sections;
    }
    public function hasSection($i){
        return array_key_exists($i, $this->sections);
    }
    public function hasMethod($method){
        if(in_array($method, $this->getMethods())){
            return true;
        }
        return false;
    }
    public function getRouter(){
        return Cleverload::getInstance()->request->router;
    }
    public function getParameters(){
        return $this->parameters;
    }
    public function getMethods(){
        return $this->methods;
    }
    public function getWhere(){
        return $this->wheres;
    }
    public function getIf(){
        return $this->when;
    }
    public function __call($function,$args){
        return Cleverload::getInstance()->request->router->call($function,$args);
    }
    public static function __callStatic($function,$args){
        return Cleverload::getInstance()->request->router->call($function,$args);
    }
}
?>