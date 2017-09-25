<?php
namespace lib\Routing;

class RouterCollection{
    
    public $routes = [];

    public function __constructor(){

    }
    public function add(Route $route){
        $this->routes[] = $route;
        return $route;
    }
    public function getRoutes(){
        return $this->routes;
    }
    private function getRoutesFromConfig(){
        $routes = Cleverload::getPages();
        foreach($routes as $uri => $file){
            if(is_array($uri)){
                foreach($uri as $url){
                    Route::get($uri,$file);
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
}
?>

