<?php

namespace lib;

use lib\Routing\Router;

class Cleverload extends Router{

    public $path;
    public $file = [];
    public $base_file;

    public $start_time;
    public $end_time;

    public static $instance = null;

    public static $router;

    public static $filebase;
    public static $base;
    public static $called_base;
    public static $called;
    public static $root;

    public static $domain;

    public function __construct($path = null){
        $this->start_time = microtime();
        if($path != null){
            $this->path = $path;
        }else{
            $this->path = $_SERVER["REQUEST_URI"];
        }
        self::$called = $this->get_calling_file();
        $this->path = $this->getPath();
        self::$base = $this->getBase();
        self::$filebase = $_SERVER["DOCUMENT_ROOT"].$this->getBase();

        $this->getDomain();
        
        self::setRouter($this->path);
        self::$instance = $this;

        $this->find();
    }
    
    public function find(){
        self::getRouter()->compile();
        $this->end_time = microtime();
        return $this;
    }
    public static function getInstance(){
        if(isset(self::$instance)){
            return self::$instance;
        }
        return null;
    }
    public static function getPages(){
        return include(__DIR__."/../pages.php");
    }
    public function setRouter($path){
        self::$router = new Router($path);
        return $this;
    }
    public static function getRouter(){
        return self::$router;
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
    public function clean_page(){
       return ob_get_clean();
    }
    public function load($url,$method = "GET"){
        $this->clean_page();
        $this->path = $this->getPath($url);
        self::setRouter($url);
        $_SERVER['REQUEST_METHOD'] = $method;
        return self::getRouter()->compile();
    }
    public function getPath(){
        $called_dir = str_replace("\\","/",pathinfo(self::$called)["dirname"]);
        self::$root = $called_dir;
        $this->base_file = $_SERVER["PHP_SELF"];
        $root = str_replace($_SERVER["DOCUMENT_ROOT"], "",$called_dir);
        $from = strtolower('/'.preg_quote($root, '/').'/');
        self::$called_base = preg_replace($from,"",strtolower($this->path),1);
        return self::$called_base;
    }
    public function getBase(){
        $configbase = $this->getConfig("base");
        $root = str_replace($_SERVER["DOCUMENT_ROOT"],"",self::$root);
        return $root.$configbase;
    }
    private function  get_calling_file() {
        $trace = debug_backtrace();
        return $trace[1]['file'];
    }
    public function getDomain(){
        $request = $_SERVER["SERVER_NAME"];
        return self::$domain = $request;
    }
    public function getExcecutiontime(){
        return  $this->end_time - $this->start_time;
    }
}
?>