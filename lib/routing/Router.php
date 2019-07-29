<?php
namespace lib\routing;

use lib\Cleverload;
use lib\http\Redirect;
use lib\http\Request;
use lib\http\Response;

class Router {
    
    private $request;
    public $response;

    public $route;
    public $routes;
    public $statusRoutes = [];

    public $defaults = [];

    public $groupstack = [];
    public $middlewarestack = [];

    public function __construct(Request $request){
        $this->request = $request;
        $this->response = new Response();
        $this->routes = new RouterCollection();

        $this->route = new Route([$request->getMethod()],$request->getUri(),null);
        $this->route->setDomain($this->request->getDomain());
        $this->route->run();
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

    public function group(array $arguments, callable $action){
        $this->addToGroupstack($arguments, $action);
        $action($this);
        array_pop($this->groupstack);
    }

    public function middleware(array $arguments, callable $before, callable $action) {
        $this->addToMiddlewareStack($arguments, $before, $action);
        $action($this);
        array_pop($this->middlewarestack);
    }

    private function add($method,$uri,$action){
        if(is_array($uri)){
            $routes = [];
            foreach($uri as $u){
                $routes[] = $this->routes->add($this->newRoute($method,$u,$action));
            }
            return $routes;
        }
        return $this->routes->add($this->newRoute($method,$uri,$action));
    }

    private function newRoute($method, $uri, $action){
        $route = new Route($method, $uri, $action);
        
        $route->setGroupstack($this->groupstack);
        $route->setMiddlewareStack($this->middlewarestack);
        return $route;
    }

    public function addDefault(Route $route){
        $this->defaults[] = $route;
        return $this;
    }

    public function getDefault(){
        if ($this->defaults != null && count($this->defaults) > 0){
            return $this->route->getClosest($this->defaults);
        } 
        return null;
    }

    public function addStatusRoute(Route $route, $statusCode = 404) {
        $this->statusRoutes[$statusCode] = $route;
        return $this;
    }

    private function addToGroupstack($arguments, callable $action){
        $this->groupstack[] = new Group($arguments, $action);
    }

    private function addToMiddlewareStack($arguments, callable $before, callable $action){
        $middleware = new Middleware($arguments, $action);
        $middleware->setBefore($before);
        $this->middlewarestack[] = $middleware;
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
        return $this->route->getResponse();
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