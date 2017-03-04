<?php
namespace lib\Routing;

class RouterCollection{
    
    public $routes = [];

    public function add(Route $route){
        $this->routes[] = $route;
    }
    public function getRoutes(){
        return $this->routes;
    }
}
?>
