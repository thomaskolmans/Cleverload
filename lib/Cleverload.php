<?php

namespace lib;

use lib\Router\Router;

class Cleverload{

    public $path;
    public $called;
    public $file = [];

    public static $base;
    public static $called_base;
    public static $root;

    public function __construct(string $path,$called = __FILE__){
        $this->path = $path;
        $this->called = $called;
        self::$base = $this->getConfig("base");
        $route = new Router($this->getPath());
        $route->load();
    }

    public static function getPages(){
        return include(__DIR__."/../pages.php");
    }
    public static function getConfig($item, $key = false){
        $config = include(__DIR__."/../config.php");  
        foreach($config as $keys => $value){
            if($keys == $item){
                if($key){
                    return $keys;
                }
                return $value;
            }
        }
    }

    private function getPath(){
        $called_dir = str_replace("\\","/",pathinfo($this->called)["dirname"]);
        self::$root = $called_dir;
        $root = str_replace($_SERVER["DOCUMENT_ROOT"], "",$called_dir);
        $from = '/'.preg_quote($root, '/').'/';
        self::$called_base = preg_replace($from,"",$this->path,1);
        return self::$called_base;
    }
}
?>