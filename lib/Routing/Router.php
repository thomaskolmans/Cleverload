<?php
namespace lib\Routing;

use lib\Cleverload;
use lib\Http\Redirect;
use lib\Http\Request;
use lib\Http\Response;

class Router{
    
    public $request;
    public $response;

    public $route;
    public $routes;
    public $defaults;

    public $groupstack = [];

    public function __construct(Request $request){
        $this->request = $request;
        $this->response = new Response();
        $this->routes = new RouterCollection();

        $this->route = new Route([$request->getMethod()],$request->getUri(),null);
        $this->route->setDomain($this->request->getDomain());
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
    public function add($method,$uri,$action){
        return $this->routes->add($this->newRoute($method,$uri,$action));
    }
    public function newRoute($method,$uri,$action){
        $route = new Route($method,$uri,$action);
        $route->setRouter($this)->setGroupstack($this->groupstack);
        return $route;
    }
    public function addDefault(Route $route){
        $this->defaults[] = $route;
        return $this;
    }
    public function getDefault(){
        return $this->route->getClosest($this);
    }
    public function addToGroupstack($arguments){
        if(!empty($arguments)){
            $this->groupstack[] = $arguments;
        }
    }
    public function group(array $arguments, callable $action){
        $this->addToGroupstack($arguments,$action);
        $action($this);
        array_pop($this->groupstack);
    }
    public function getRoutes(){
        $this->getRouterFiles();
        return $this->routes->getRoutes();
    }
    public function call($func,$args){
        return $this->{$func}(...array_values($args));
    }
    public function getResponse(){
        $this->getRouterFiles();
        return $this->route->getResponse($this);
    }
    public function getRequest(){
        return $this->request;
    }
    private function getRouterFiles(){
        $this->requireAllFiles(Cleverload::getInstance()->root."/routes/");
        return $this;
    }
    private function requireAllFiles($path){
        $items = scandir($path);
        foreach($items as $item){
            if(is_file($path.$item)){
                require_once($path.$item);
            }
        }
    }
}