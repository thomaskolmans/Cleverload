<?php
namespace lib\Routing;

use lib\Template\Template;
use lib\Http\Response;
use lib\Cleverload;

class Route{

    private $router;
    public $response;

    public $uri;
    public $domain;

    public $groupstack;

    private $file = null;
    private $action = null;

    protected $methods = [];
    protected $parameters = [];

    protected $wheres = [];
    protected $if = true;

    protected $sections;
    protected $sectioncount;

    public function __construct($methods,$uri,$action){
        $this->uri = $uri;
        $this->setAction($action);
        $this->methods = $methods;
        $this->run();
    }
    public function run(){
        $this->sections = $this->setSections();
        $this->sectioncount = $this->setSectionCount();

        $this->setParameters();
    }
    public function setGroupstack($groupstack){
        $this->groupstack = $groupstack;
        $this->defractorGroupstack();
        $this->run();
    }
    public function defractorGroupstack(){
        for($i = 0; $i < count($this->groupstack); $i++){
            foreach($this->groupstack[$i] as $grouptype => $value){
                switch($grouptype){
                    case "namespace":
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
    public function getResponse(Router $router){
        $this->run();
        $this->router = $router;
        $this->response = new Response();

        if(is_file(Cleverload::getInstance()->getStaticFilesDir().$this->uri)){
            printf(file_get_contents($this->uri));
        }
        $matchedroute = $this->getMatch($this->router);
        $matchedroute->load();
    }
    public function getMatch(Router $router){
        $routes = $router->getRoutes();
        $found = false;
        for($i = 0; $i < $this->sectioncount; $i++){
            $matches = [];
            if(!$found && count($routes) > 0){
                $matches = $this->matchSectionToRoutes($i,$routes);
                $routes = $matches;
                if(count($routes) == 1){
                    $found = true;
                }
            }else{
                break;
            }
        }
        if(!$found){
        }
        return $routes[0];
    }
    private function matchSectionToRoutes($i,$routes){
        $matches = [];
        foreach($routes as $route){
            if($this->equalsSection($i,$route)){
                if(count($this->getWhere()) > 0 && $this->getSection($i)->isValue()){
                    if(!preg_match("/^".$route->getWhere()[$this->getSection($i)->clean()]."+$/",$this->getSection($i)->get())){
                        continue;
                    }
                }
                if(!$route->getIf()){
                    continue;
                }
                $matches[] = $route;
            }
        }
        return $matches;
    }
    private function equalsSection($i,$route){
        if($this->getSectionCount() >= $route->getSectionCount() && $this->hasMethod($route->getRouter()->getRequest()->getMethod())){
            if($this->getSection($i)->toString() === $route->getSection($i)->toString() || $this->getSection($i)->isValue()){
                return true;
            }
        }
        return false;
    }
    public function where($variable,$regex){
        $this->wheres[$variable] = $regex;
        return $this;
    }
    public function when($true){
        $this->if = $true;
        return $this;
    }
    public function primary(){
        if(count($this->getParameters()) < 1){
            $this->setDefault($this);
        }else{
            throw new \Exception("You can't set the default to a path with a variable");
        }
        return $this;
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
    public function setRouter(Router $router){
        $this->router = $router;
        return $this;
    }
    public function getRouter(){
        return $this->router;
    }
    public function getParameters(){
        return $this->parameters;
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
        foreach($this->sections as $section){
            if($section->isValue()){
                array_push($this->parameters,$section->clean());
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
            return $this->loadCallable($this->action,$this->getParameters());   
        }
        $this->setParametersAsGet();
        $this->action = Cleverload::$filebase."/".$this->action;    
        return $this->loadFile();
    }

    public function loadCallable($func = null,$values = []){
        return $func(...array_values($values));
    }
    public function loadFile(){
        return new Template($this);
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
        return $this->action;
    }
    public function getDomain(){
        return $this->domain;
    }
    public function setDomain($domain){
        $this->domain = $domain;
        return $this;
    }
    public function getSection($number){
        if(array_key_exists($number, $this->getSections())){
            return $this->getSections()[$number];
        }
        return null;
    }
    public function getSections(){
        return $this->sections;
    }
    public function hasMethod($method){
        if(in_array($method, $this->getMethods())){
            return true;
        }
        return false;
    }
    public function getMethods(){
        return $this->methods;
    }
    public function getWhere(){
        return $this->wheres;
    }
    public function getIf(){
        return $this->if;
    }
    public function __call($function,$args){
        return Cleverload::getInstance()->request->router->call($function,$args);
    }
    public static function __callStatic($function,$args){
        return Cleverload::getInstance()->request->router->call($function,$args);
    }
}
?>