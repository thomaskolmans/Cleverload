<?php

namespace lib;

use lib\Routing\Router;

class Cleverload extends Router{

    public $path;
    public $file = [];
    public $base_file;

    public static $base;
    public static $called_base;
    public static $called;
    public static $root;

    public static $domain;

    public function __construct($path = null){
        if($path != null){
            $this->path = $path;
        }else{
            $this->path = $_SERVER["REQUEST_URI"];
        }
        self::$called = $this->get_calling_file();
        $this->path = $this->getPath();
        self::$base = $this->getBase();
        $this->getDomain();
        parent::__construct($this->path);
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

    public function clean_page(){
       return ob_get_clean();
    }
    public function load($url){
        $this->clean_page();
        $this->__construct($url);
    }
    public function getPath(){
        $called_dir = str_replace("\\","/",pathinfo(self::$called)["dirname"]);
        self::$root = $called_dir;
        $this->base_file = $_SERVER["PHP_SELF"];
        $root = str_replace($_SERVER["DOCUMENT_ROOT"], "",$called_dir);
        $from = '/'.preg_quote($root, '/').'/';
        self::$called_base = preg_replace($from,"",$this->path,1);
        return self::$called_base;
    }
    public function getBase(){
        $configbase = $this->getConfig("base");
        return self::$root.$configbase;
    }
    private function  get_calling_file() {
        $trace = debug_backtrace();
        return $trace[1]['file'];
    }
    public function getDomain(){
        $request = $_SERVER["SERVER_NAME"];
        return self::$domain = $request;
    }
}
?>